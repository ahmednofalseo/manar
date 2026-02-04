<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Approval;
use App\Models\User;
use App\Enums\PaymentStatus;
use App\Enums\ExpenseStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Helpers\PermissionHelper;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Apply filters based on user role
        $projectQuery = Project::query();
        $taskQuery = Task::query()->whereNotNull('project_id');
        
        // Filter projects based on role
        if ($user->hasRole('engineer') || $user->hasRole('admin_staff')) {
            // للمستخدمين العاديين: إخفاء المشاريع المخفية
            $projectQuery->where('is_hidden', false);
            $projectQuery->where(function($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                  ->orWhereJsonContains('team_members', (string)$user->id)
                  ->orWhereJsonContains('team_members', $user->id);
            });
            $taskQuery->where('assignee_id', $user->id);
        } elseif ($user->hasRole('project_manager')) {
            // للمديرين: إخفاء المشاريع المخفية
            $projectQuery->where('is_hidden', false);
            $projectIds = Project::where('project_manager_id', $user->id)
                ->orWhereHas('teamUsers', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->pluck('id');
            $projectQuery->whereIn('id', $projectIds);
            $taskQuery->whereIn('project_id', $projectIds);
        }
        // Super Admin sees everything (بما في ذلك المشاريع المخفية - no filters)

        // Apply filters from request
        if ($request->filled('city')) {
            $projectQuery->where('city', $request->city);
        }

        if ($request->filled('owner')) {
            $projectQuery->where('owner', 'like', "%{$request->owner}%");
        }

        if ($request->filled('status')) {
            $projectQuery->where('status', $request->status);
        }

        if ($request->filled('engineer_id')) {
            $projectQuery->whereJsonContains('team_members', (int)$request->engineer_id);
        }

        if ($request->filled('date_from')) {
            $projectQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $projectQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // KPIs
        $totalProjects = $projectQuery->count();
        $avgProgress = $projectQuery->avg('progress') ?? 0;

        $totalTasks = $taskQuery->count();
        $tasksInProgress = $taskQuery->where('status', 'in_progress')->count();
        $tasksDone = $taskQuery->where('status', 'done')->count();
        $tasksOverdue = $taskQuery->where('due_date', '<', now())
            ->whereNotIn('status', ['done', 'rejected'])
            ->count();

        // Financial KPIs
        // Financials data - only if user has permission
        $totalInvoices = 0;
        $totalCollected = 0;
        $totalDue = 0;
        $collectedThisMonth = 0;
        $recentInvoices = collect();
        
        if (PermissionHelper::hasPermission('financials.view') || PermissionHelper::hasPermission('financials.manage')) {
            $collectedThisMonth = Payment::where('status', PaymentStatus::PAID)
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount');

            $totalDue = Invoice::sum(DB::raw('total_amount - paid_amount'));
            $totalCollected = Payment::where('status', PaymentStatus::PAID)->sum('amount');
            $totalInvoices = Invoice::sum('total_amount');
        }

        // Clients KPIs
        $activeClients = Client::where('status', 'active')->count();
        $newClientsThisMonth = Client::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $pendingApprovals = Approval::where('status', 'pending')->count();

        // Recent Projects (with filters applied) - eager load all needed relationships
        $recentProjects = $projectQuery->with([
            'client', 
            'projectManager',
            'projectStages' => function($q) {
                $q->where('status', 'in_progress');
            },
            'attachments',
            'tasks'
        ])
            ->latest()
            ->limit(6)
            ->get();

        // Upcoming Tasks
        $upcomingTasks = $taskQuery->with(['project', 'assignee', 'projectStage'])
            ->where('due_date', '>=', now())
            ->whereNotIn('status', ['done', 'rejected'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Recent Invoices - only if user has permission
        if (PermissionHelper::hasPermission('financials.view') || PermissionHelper::hasPermission('financials.manage')) {
            $recentInvoices = Invoice::with(['client', 'project'])
                ->latest('issue_date')
                ->limit(5)
                ->get();
        }

        // Top Performers (Engineers with best task completion rate)
        $topPerformers = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['engineer', 'project_manager']);
            })
            ->withCount(['assignedTasks as completed_tasks_count' => function($q) {
                $q->where('status', 'done')->whereNotNull('project_id');
            }])
            ->withCount(['assignedTasks as total_tasks_count' => function($q) {
                $q->whereNotNull('project_id');
            }])
            ->having('total_tasks_count', '>', 0)
            ->get()
            ->map(function($user) {
                $completionRate = $user->total_tasks_count > 0 
                    ? round(($user->completed_tasks_count / $user->total_tasks_count) * 100, 1)
                    : 0;
                return [
                    'user' => $user,
                    'completion_rate' => $completionRate,
                    'completed_tasks' => $user->completed_tasks_count,
                    'total_tasks' => $user->total_tasks_count,
                ];
            })
            ->sortByDesc('completion_rate')
            ->take(3)
            ->values();

        // Project Status Distribution for Chart (optimized - single query)
        $projectStatusDistribution = Project::select('status', DB::raw('count(*) as count'))
            ->whereIn('status', ['مكتمل', 'قيد التنفيذ', 'متوقف'])
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Ensure all statuses exist in array
        $projectStatusDistribution = array_merge([
            'مكتمل' => 0,
            'قيد التنفيذ' => 0,
            'متوقف' => 0,
        ], $projectStatusDistribution);

        // Recent Client Activities
        $recentClientActivities = collect();
        
        // Recent approvals
        $recentApprovals = Approval::with(['requester', 'project', 'client'])
            ->latest('requested_at')
            ->limit(2)
            ->get();
        
        foreach ($recentApprovals as $approval) {
            $recentClientActivities->push([
                'type' => 'approval',
                'message' => 'موافقة جديدة من ' . ($approval->client->name ?? $approval->project->name ?? 'غير محدد'),
                'time' => $approval->requested_at,
                'icon' => 'fa-clipboard-check',
                'color' => 'blue',
            ]);
        }

        // Recent client notes
        $recentClientNotes = \App\Models\ClientNote::with(['client', 'creator'])
            ->latest()
            ->limit(2)
            ->get();
        
        foreach ($recentClientNotes as $note) {
            $recentClientActivities->push([
                'type' => 'note',
                'message' => 'ملاحظة من ' . ($note->client->name ?? 'غير محدد'),
                'time' => $note->created_at,
                'icon' => 'fa-comment',
                'color' => 'green',
            ]);
        }

        $recentClientActivities = $recentClientActivities->sortByDesc('time')->take(2);

        // For filters dropdowns
        $cities = Project::distinct()->pluck('city')->filter()->sort()->values();
        $owners = Project::distinct()->pluck('owner')->filter()->sort()->values();
        $engineers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['engineer', 'project_manager']);
        })->orderBy('name')->get();

        // User-specific data (for non-admin users)
        // Check if user is admin BEFORE applying filters
        $isAdmin = $user->hasRole('super_admin') || Gate::allows('manage-roles-permissions');
        $userStats = [
            'total_projects' => 0,
            'total_tasks' => 0,
            'done_tasks' => 0,
            'rejected_tasks' => 0,
            'in_progress_tasks' => 0,
            'new_tasks' => 0,
            'completion_rate' => 0,
            'overdue_tasks' => 0,
            'upcoming_tasks' => 0,
        ];
        $userProjects = collect();
        $projectsWithOverdueTasks = collect();
        $projectsWithUpcomingTasks = collect();
        $userRank = null;
        $leaderboard = collect();

        if (!$isAdmin) {
            // Rebuild queries for user-specific data (without admin filters)
            $userProjectQuery = Project::query();
            $userTaskQuery = Task::query()->whereNotNull('project_id');
            
            // Apply user-specific filters
            if ($user->hasRole('engineer') || $user->hasRole('admin_staff')) {
                // للمستخدمين العاديين: إخفاء المشاريع المخفية
                $userProjectQuery->where('is_hidden', false);
                $userProjectQuery->where(function($q) use ($user) {
                    $q->where('project_manager_id', $user->id)
                      ->orWhereJsonContains('team_members', (string)$user->id)
                      ->orWhereJsonContains('team_members', $user->id);
                });
                $userTaskQuery->where('assignee_id', $user->id);
            } elseif ($user->hasRole('project_manager')) {
                // للمديرين: إخفاء المشاريع المخفية
                $userProjectQuery->where('is_hidden', false);
                $projectIds = Project::where('project_manager_id', $user->id)
                    ->orWhereHas('teamUsers', function($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    ->pluck('id');
                $userProjectQuery->whereIn('id', $projectIds);
                $userTaskQuery->whereIn('project_id', $projectIds);
            } else {
                // For other roles, show projects where user is manager or team member
                $userProjectQuery->where('is_hidden', false);
                $userProjectQuery->where(function($q) use ($user) {
                    $q->where('project_manager_id', $user->id)
                      ->orWhereJsonContains('team_members', (string)$user->id)
                      ->orWhereJsonContains('team_members', $user->id);
                });
                $userTaskQuery->where('assignee_id', $user->id);
            }
            
            // User's projects with tasks
            $userProjects = $userProjectQuery->with(['tasks' => function($q) use ($user) {
                $q->where('assignee_id', $user->id);
            }])->get();

            // User statistics - use the filtered task query
            $userTasks = $userTaskQuery->get();
            $userDoneTasks = $userTasks->where('status', 'done')->count();
            $userRejectedTasks = $userTasks->where('status', 'rejected')->count();
            $userInProgressTasks = $userTasks->where('status', 'in_progress')->count();
            $userNewTasks = $userTasks->where('status', 'new')->count();
            
            // Projects with overdue tasks
            $projectsWithOverdueTasks = $userProjects->filter(function($project) use ($user) {
                return $project->tasks->where('assignee_id', $user->id)
                    ->where('due_date', '<', now())
                    ->whereNotIn('status', ['done', 'rejected'])
                    ->count() > 0;
            });

            // Projects with upcoming tasks (due within 7 days)
            $projectsWithUpcomingTasks = $userProjects->filter(function($project) use ($user) {
                return $project->tasks->where('assignee_id', $user->id)
                    ->where('due_date', '>=', now())
                    ->where('due_date', '<=', now()->addDays(7))
                    ->whereNotIn('status', ['done', 'rejected'])
                    ->count() > 0;
            });

            // Leaderboard - Top performers
            $leaderboard = User::whereHas('roles', function($q) {
                    $q->whereIn('name', ['engineer', 'project_manager']);
                })
                ->withCount(['assignedTasks as completed_tasks_count' => function($q) {
                    $q->where('status', 'done')->whereNotNull('project_id');
                }])
                ->withCount(['assignedTasks as total_tasks_count' => function($q) {
                    $q->whereNotNull('project_id');
                }])
                ->having('total_tasks_count', '>', 0)
                ->get()
                ->map(function($u) {
                    $completionRate = $u->total_tasks_count > 0 
                        ? round(($u->completed_tasks_count / $u->total_tasks_count) * 100, 1)
                        : 0;
                    return [
                        'user' => $u,
                        'completion_rate' => $completionRate,
                        'completed_tasks' => $u->completed_tasks_count,
                        'total_tasks' => $u->total_tasks_count,
                    ];
                })
                ->sortByDesc('completion_rate')
                ->values();

            // Find user's rank
            $userRank = $leaderboard->search(function($item) use ($user) {
                return $item['user']->id === $user->id;
            });
            $userRank = $userRank !== false ? $userRank + 1 : null;

            // Calculate user statistics
            $userStats = [
                'total_projects' => $userProjects->count(),
                'total_tasks' => $userTasks->count(),
                'done_tasks' => $userDoneTasks,
                'rejected_tasks' => $userRejectedTasks,
                'in_progress_tasks' => $userInProgressTasks,
                'new_tasks' => $userNewTasks,
                'completion_rate' => $userTasks->count() > 0 
                    ? round(($userDoneTasks / $userTasks->count()) * 100, 1) 
                    : 0,
                'overdue_tasks' => $userTasks->filter(function($task) {
                    return $task->due_date && 
                           $task->due_date < now() && 
                           !in_array($task->status, ['done', 'rejected']);
                })->count(),
                'upcoming_tasks' => $userTasks->filter(function($task) {
                    return $task->due_date && 
                           $task->due_date >= now() && 
                           $task->due_date <= now()->addDays(7) && 
                           !in_array($task->status, ['done', 'rejected']);
                })->count(),
            ];
        }

        return view('dashboard.index', compact(
            'totalProjects',
            'avgProgress',
            'totalTasks',
            'tasksInProgress',
            'tasksDone',
            'tasksOverdue',
            'collectedThisMonth',
            'totalDue',
            'totalCollected',
            'totalInvoices',
            'activeClients',
            'newClientsThisMonth',
            'pendingApprovals',
            'recentProjects',
            'upcomingTasks',
            'recentInvoices',
            'topPerformers',
            'projectStatusDistribution',
            'recentClientActivities',
            'cities',
            'owners',
            'engineers',
            'isAdmin',
            'userStats',
            'userProjects',
            'projectsWithOverdueTasks',
            'projectsWithUpcomingTasks',
            'userRank',
            'leaderboard'
        ));
    }
}
