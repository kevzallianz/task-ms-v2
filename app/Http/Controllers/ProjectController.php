<?php

namespace App\Http\Controllers;

use App\Mail\CampaignMemberJoined;
use App\Mail\NewCampaignTask;
use App\Models\Campaign;
use App\Models\Project;
use App\Models\ProjectContributor;
use App\Models\ProjectTask;
use App\Models\ProjectRemarks;
use App\Models\ProjectActivity;
use App\Mail\ProjectContributorAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProjectController extends Controller
{
    /**
     * Get the current user's access level in a campaign.
     */

    private function getUserAccessLevel()
    {
        return request()->user()->campaignMember->access_level ?? 'viewer';
    }

    /**
     * Log an activity for a project.
     */
    private function logActivity(Project $project, string $actionType, string $description, ?array $metadata = null)
    {
        ProjectActivity::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'action_type' => $actionType,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'name' => 'required|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'target_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planning,in_progress,completed,on_hold',
        ], [
            'name.required' => 'Project name is required',
            'name.max' => 'Project name must not exceed 50 characters',
            'start_date.required' => 'Start date is required',
            'start_date.date' => 'Start date must be a valid date',
            'target_date.date' => 'Target date must be a valid date',
            'target_date.after_or_equal' => 'Target date must be after or equal to start date',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ]);

        $campaign = DB::table('campaign_members')
            ->where('user_id', Auth::id())
            ->value('campaign_id');

        if (!$campaign) {
            return response()->json([
                'message' => 'You must be part of a campaign to create a project.',
            ], 403);
        }

        try {
            $project = Project::create([
                'campaign_id' => $validated['campaign_id'],
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'target_date' => $validated['target_date'] ?? null,
                'status' => $validated['status'],
            ]);

            // Log activity
            $this->logActivity($project, 'created', 'Created project "' . $project->name . '"', [
                'status' => $project->status,
                'start_date' => $project->start_date,
            ]);

            return response()->json([
                'message' => 'Project created successfully!',
                'project' => $project,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create project. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing project.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'target_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planning,in_progress,completed,on_hold',
        ], [
            'name.required' => 'Project name is required',
            'name.max' => 'Project name must not exceed 50 characters',
            'start_date.required' => 'Start date is required',
            'start_date.date' => 'Start date must be a valid date',
            'target_date.date' => 'Target date must be a valid date',
            'target_date.after_or_equal' => 'Target date must be after or equal to start date',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ]);

        try {
            $oldName = $project->name;
            $changes = [];

            if ($project->name !== $validated['name']) {
                $changes['name'] = ['from' => $project->name, 'to' => $validated['name']];
            }

            $project->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'target_date' => $validated['target_date'] ?? null,
                'status' => $validated['status'],
            ]);

            // Log activity
            $description = $changes ? 'Updated project details' : 'Updated project "' . $project->name . '"';
            $this->logActivity($project, 'updated', $description, $changes ?: null);

            return response()->json([
                'message' => 'Project updated successfully!',
                'project' => $project,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update project. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a specific project with its contributors.
     */
    public function view(Project $project)
    {
        // Eager load all relationships to avoid N+1 queries
        $project->load([
            'user',
            'campaign',
            'tasks.campaign',
            'contributors.campaign',
            'activities.user.campaignMember.campaign'
        ]);

        // Get campaign IDs for task assignment (owner campaign + contributor campaigns)
        $taskCampaignIds = $project->contributors
            ->pluck('campaign_id')
            ->push()
            ->unique()
            ->filter()
            ->values();

        // Use already loaded relationships to avoid additional queries
        $taskCampaigns = collect()
            ->push($project->campaign)
            ->merge($project->contributors->pluck('campaign'))
            ->unique('id');

        // Get only campaigns that can be added as contributors (exclude already contributed ones)
        $allCampaigns = Campaign::whereNotIn('id', $taskCampaignIds)->get();

        // Check if current user is the project owner
        $isProjectOwner = $project->user_id === Auth::id();

        // Get current user's access level in the project's campaign
        $userAccessLevel = $isProjectOwner ? 'all' : $this->getUserAccessLevel();

        return view('user.projects.project', [
            'project' => $project,
            'taskCampaigns' => $taskCampaigns,
            'allCampaigns' => $allCampaigns,
            'isProjectOwner' => $isProjectOwner,
            'userAccessLevel' => $userAccessLevel,
        ]);
    }

    /* Add a campaign as contributor to a project. */
    public function addContributor(Request $request, Project $project)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id|unique:project_contributors,campaign_id,NULL,id,project_id,' . $project->id,
        ], [
            'campaign_id.required' => 'Please select a campaign',
            'campaign_id.exists'   => 'The selected campaign does not exist',
            'campaign_id.unique'   => 'This campaign is already a contributor to this project',
        ]);

        try {
            DB::transaction(function () use ($project, $validated) {
                // Create the contributor record
                ProjectContributor::create([
                    'project_id'  => $project->id,
                    'campaign_id' => $validated['campaign_id'],
                ]);

                // Get campaign
                $campaign = Campaign::findOrFail($validated['campaign_id']);

                // Log activity
                $this->logActivity(
                    $project,
                    'contributor_added',
                    'Added "' . $campaign->name . '" as contributor',
                    [
                        'campaign_id'   => $campaign->id,
                        'campaign_name' => $campaign->name,
                    ]
                );

                // Notify campaign members
                $campaignMembers = $campaign->campaignMembers;

                foreach ($campaignMembers as $member) {
                    if ($member && $member->user && $member->user->email) {
                        Mail::to($member->user->email)->send(new ProjectContributorAdded($project, $campaign, $member->user));
                    }
                }
            });

            return response()->json([
                'message' => 'Contributor added successfully!',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add contributor. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    /* Remove a contributor from a project. */
    public function removeContributor(Project $project, ProjectContributor $contributor)
    {
        try {
            if ($contributor->project_id !== $project->id) {
                return response()->json([
                    'message' => 'Contributor not found in this project.',
                ], 404);
            }

            $contributor->delete();

            return response()->json([
                'message' => 'Contributor removed successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove contributor. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new task for the project.
     */
    public function addTask(Request $request, Project $project)
    {
        // Check authorization - only project owner or users with editor/all access can add tasks
        $isOwner = $project->user_id === Auth::id();
        $accessLevel = $this->getUserAccessLevel();

        if (!$isOwner && !in_array($accessLevel, ['editor', 'all'])) {
            return response()->json([
                'message' => 'You do not have permission to add tasks to this project.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,ongoing,completed',
            'assigned_campaign_id' => 'required|exists:campaigns,id',
        ], [
            'title.required' => 'Task title is required',
            'title.max' => 'Task title must not exceed 50 characters',
            'target_date.after_or_equal' => 'Target date must be after or equal to start date',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
            'assigned_campaign_id.required' => 'Please select a campaign',
        ]);

        try {
            $projectTask = ProjectTask::create([
                'project_id' => $project->id,
                'assigned_campaign_id' => $validated['assigned_campaign_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'target_date' => $validated['target_date'] ?? null,
                'status' => $validated['status'],
            ]);

            $campaign = Campaign::find($validated['assigned_campaign_id']);

            // Send email notifications to all campaign members
            $campaignMembers = $campaign->campaignMembers;

            foreach ($campaignMembers as $member) {
                if ($member && $member->user && $member->user->email) {
                    Mail::to($member->user->email)->send(new NewCampaignTask($campaign, $projectTask, $member->user));
                }
            }


            return response()->json([
                'message' => 'Task created successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create task. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Request $request, Project $project, ProjectTask $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        // Check authorization - only project owner or users with editor/all access can edit tasks
        $isOwner = $project->user_id === Auth::id();
        $accessLevel = $this->getUserAccessLevel();

        if (!$isOwner && !in_array($accessLevel, ['editor', 'all'])) {
            return response()->json([
                'message' => 'You do not have permission to edit tasks.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,ongoing,completed',
            'assigned_campaign_id' => 'required|exists:campaigns,id',
        ], [
            'title.required' => 'Task title is required',
            'title.max' => 'Task title must not exceed 50 characters',
            'target_date.after_or_equal' => 'Target date must be after or equal to start date',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
            'assigned_campaign_id.required' => 'Please select a campaign',
            'assigned_campaign_id.exists' => 'Selected campaign does not exist',
        ]);

        try {
            $oldTitle = $task->title;
            $changes = [];

            if ($task->title !== $validated['title']) {
                $changes['title'] = ['from' => $task->title, 'to' => $validated['title']];
            }

            $task->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'target_date' => $validated['target_date'] ?? null,
                'status' => $validated['status'],
                'assigned_campaign_id' => $validated['assigned_campaign_id'],
            ]);

            // Log activity
            $this->logActivity($project, 'task_updated', 'Updated task "' . $task->title . '"', [
                'task_id' => $task->id,
                'changes' => $changes ?: null,
            ]);

            return response()->json([
                'message' => 'Task updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update task. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the status of a task belonging to the project.
     */
    public function updateTaskStatus(Request $request, Project $project, ProjectTask $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        // Check authorization - only project owner or users with editor/all access can update status
        $isOwner = $project->user_id === Auth::id();
        $accessLevel = $this->getUserAccessLevel();

        if (!$isOwner && !in_array($accessLevel, ['editor', 'all'])) {
            return response()->json([
                'message' => 'You do not have permission to update task status.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,ongoing,completed',
        ], [
            'status.required' => 'Please select a status',
            'status.in' => 'Invalid status selected',
        ]);

        try {
            $oldStatus = $task->status;
            $task->update(['status' => $validated['status']]);

            // Log activity
            $this->logActivity($project, 'task_status_changed', 'Changed task "' . $task->title . '" status from ' . $oldStatus . ' to ' . $validated['status'], [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
            ]);

            return response()->json([
                'message' => 'Task status updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update status. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch remarks for a specific task.
     */
    public function getTaskRemarks(Project $project, ProjectTask $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        $remarks = $task->remarks()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'remarks' => $remarks,
        ]);
    }

    /**
     * Add a remark to a task.
     */
    public function addTaskRemark(Request $request, Project $project, ProjectTask $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        // Check authorization - all campaign members (viewer, editor, all) can add remarks
        $accessLevel = $this->getUserAccessLevel();

        if (!in_array($accessLevel, ['viewer', 'editor', 'all'])) {
            return response()->json([
                'message' => 'You do not have permission to add remarks.',
            ], 403);
        }

        $validated = $request->validate([
            'remark' => 'required|string|max:500',
        ], [
            'remark.required' => 'Remark is required',
            'remark.max' => 'Remark must not exceed 500 characters',
        ]);

        try {
            ProjectRemarks::create([
                'project_task_id' => $task->id,
                'user_id' => Auth::id(),
                'remarks' => $validated['remark'],
            ]);

            // Log activity
            $remarkPreview = strlen($validated['remark']) > 50
                ? substr($validated['remark'], 0, 50) . '...'
                : $validated['remark'];

            $this->logActivity($project, 'task_remark_added', 'Added a remark on task "' . $task->title . '"', [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'remark_preview' => $remarkPreview,
            ]);

            return response()->json([
                'message' => 'Remark added successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add remark. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the status of a project.
     */
    public function updateStatus(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => 'required|in:planning,in_progress,completed,on_hold',
        ], [
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ]);

        try {
            $oldStatus = $project->status;

            // Only update the status column to minimize write operations
            $project->timestamps = false; // Skip updating updated_at if status hasn't changed
            $project->status = $validated['status'];
            $project->save();

            // Log activity
            $this->logActivity($project, 'status_changed', 'Changed project status from ' . $oldStatus . ' to ' . $validated['status'], [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
            ]);

            return response()->json([
                'message' => 'Project status updated successfully!',
                'status' => $project->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update project status. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a task from the project.
     */
    public function deleteTask(Request $request, Project $project, ProjectTask $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task not found in this project.',
            ], 404);
        }

        // Check authorization - only project owner or users with editor/all access can delete tasks
        $isOwner = $project->user_id === Auth::id();
        $accessLevel = $this->getUserAccessLevel();

        if (!$isOwner && !in_array($accessLevel, ['editor', 'all'])) {
            return response()->json([
                'message' => 'You do not have permission to delete tasks.',
            ], 403);
        }

        try {
            $taskTitle = $task->title;

            // Delete associated remarks first (if any)
            $task->remarks()->delete();

            // Delete the task
            $task->delete();

            // Log activity
            $this->logActivity($project, 'task_deleted', 'Deleted task "' . $taskTitle . '"', [
                'task_title' => $taskTitle,
            ]);

            return response()->json([
                'message' => 'Task deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete task. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a project.
     */
    public function deleteProject(Request $request, Project $project)
    {
        // Check authorization - only project owner can delete the project
        if ($project->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'You do not have permission to delete this project.',
            ], 403);
        }

        try {
            $projectName = $project->name;

            // Delete all related data
            // Delete all task remarks for all tasks in this project
            ProjectRemarks::whereIn('project_task_id', $project->tasks()->pluck('id'))->delete();

            // Delete all tasks
            $project->tasks()->delete();

            // Delete all activities
            $project->activities()->delete();

            // Delete all contributors
            $project->contributors()->delete();

            // Delete the project itself
            $project->delete();

            return response()->json([
                'message' => 'Project deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete project. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
