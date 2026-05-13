<?php

namespace App\Http\Controllers;

use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JobTitlesController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', JobTitle::class);

        $jobTitles = JobTitle::ordered()->get();

        return view('settings.job-titles.index', compact('jobTitles'));
    }

    public function create()
    {
        Gate::authorize('create', JobTitle::class);

        return view('settings.job-titles.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', JobTitle::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_titles,name',
            'name_en' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ], [
            'name.required' => __('Job title Arabic required'),
            'name.unique' => __('Job title unique error'),
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        JobTitle::create($validated);

        return redirect()->route('settings.job-titles.index')
            ->with('success', __('Job title created'));
    }

    public function edit(JobTitle $jobTitle)
    {
        Gate::authorize('update', $jobTitle);

        return view('settings.job-titles.edit', compact('jobTitle'));
    }

    public function update(Request $request, JobTitle $jobTitle)
    {
        Gate::authorize('update', $jobTitle);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_titles,name,'.$jobTitle->id,
            'name_en' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ], [
            'name.required' => __('Job title Arabic required'),
            'name.unique' => __('Job title unique error'),
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $jobTitle->update($validated);

        return redirect()->route('settings.job-titles.index')
            ->with('success', __('Job title updated'));
    }

    public function destroy(JobTitle $jobTitle)
    {
        Gate::authorize('delete', $jobTitle);

        $inUse = User::where('job_title', $jobTitle->name)->count();
        if ($inUse > 0) {
            return back()->with('error', __('Cannot delete job title in use', ['count' => $inUse]));
        }

        $jobTitle->delete();

        return redirect()->route('settings.job-titles.index')
            ->with('success', __('Job title deleted'));
    }
}
