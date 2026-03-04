<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecideApprovalRequest;
use App\Http\Requests\RequestApprovalRequest;
use App\Models\Approval;
use App\Models\Project;
use App\Services\StageFlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ApprovalsController extends Controller
{
    protected $stageFlowService;

    public function __construct(StageFlowService $stageFlowService)
    {
        $this->stageFlowService = $stageFlowService;
    }

    /**
     * Display a listing of approvals.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Approval::class);

        $status = $request->get('status', 'pending'); // pending, approved, rejected
        $projectId = $request->get('project_id');
        $stageKey = $request->get('stage_key');

        $query = Approval::with(['project', 'client', 'requester', 'decider', 'approvable'])
            ->latest('requested_at');

        // فلترة حسب الحالة
        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        // فلترة حسب المشروع
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        // فلترة حسب المرحلة
        if ($stageKey) {
            $query->where('stage_key', $stageKey);
        }

        // فلترة حسب الصلاحيات
        $user = auth()->user();
        if ($user->hasRole('engineer')) {
            // المهندس يرى فقط الموافقات المتعلقة بمشاريعه
            $projectIds = $user->projects()->pluck('projects.id')
                ->merge($user->managedProjects()->pluck('id'));
            $query->whereIn('project_id', $projectIds);
        }

        $approvals = $query->paginate(15);

        // الإحصائيات
        $stats = [
            'pending' => Approval::where('status', 'pending')->count(),
            'approved' => Approval::where('status', 'approved')->count(),
            'rejected' => Approval::where('status', 'rejected')->count(),
        ];

        // المراحل
        $stages = \App\Enums\ProjectStageKey::allWithLabels();

        // المشاريع للفلترة
        $projects = Project::select('id', 'name', 'project_number')
            ->orderBy('name')
            ->get();

        return view('approvals.index', compact('approvals', 'status', 'stats', 'stages', 'projects', 'projectId', 'stageKey'));
    }

    /**
     * Request approval for an item.
     */
    public function request(RequestApprovalRequest $request)
    {
        DB::beginTransaction();
        try {
            // التحقق من وجود العنصر
            $approvableType = $request->approvable_type;
            $approvable = $approvableType::findOrFail($request->approvable_id);

            // التحقق من أن العنصر ينتمي للمشروع
            if (isset($approvable->project_id) && $approvable->project_id != $request->project_id) {
                return back()->with('error', 'العنصر لا ينتمي للمشروع المحدد');
            }

            // إنشاء طلب الموافقة
            $approval = Approval::create([
                'project_id' => $request->project_id,
                'client_id' => $request->client_id ?? Project::find($request->project_id)->client_id,
                'approvable_type' => $request->approvable_type,
                'approvable_id' => $request->approvable_id,
                'stage_key' => $request->stage_key,
                'status' => 'pending',
                'manager_note' => $request->manager_note,
                'requested_by' => auth()->id(),
                'requested_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'تم إرسال طلب الموافقة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إرسال طلب الموافقة: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'حدث خطأ أثناء إرسال طلب الموافقة: ' . $e->getMessage());
        }
    }

    /**
     * Decide on approval (approve/reject).
     */
    public function decide(DecideApprovalRequest $request, Approval $approval)
    {
        if (!$approval->canBeApproved() && !$approval->canBeRejected()) {
            return back()->with('error', 'لا يمكن اتخاذ قرار على هذه الموافقة');
        }

        DB::beginTransaction();
        try {
            $decision = $request->decision;
            $note = $request->note;

            $approval->update([
                'status' => $decision === 'approve' ? 'approved' : 'rejected',
                'client_note' => $note,
                'decided_by' => auth()->id(),
                'decided_at' => now(),
            ]);

            // إذا تمت الموافقة، التحقق من إمكانية الانتقال للمرحلة التالية
            if ($decision === 'approve') {
                $this->stageFlowService->checkAndAdvanceStage($approval->project, $approval->stage_key);
            }

            DB::commit();

            $message = $decision === 'approve' 
                ? 'تم الموافقة بنجاح' 
                : 'تم الرفض بنجاح';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'approval' => $approval->fresh(),
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء اتخاذ القرار: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'حدث خطأ أثناء اتخاذ القرار: ' . $e->getMessage());
        }
    }

    /**
     * Show approval details.
     */
    public function show(Approval $approval)
    {
        Gate::authorize('view', $approval);

        $approval->load(['project', 'client', 'requester', 'decider', 'approvable']);

        return view('approvals.show', compact('approval'));
    }
}
