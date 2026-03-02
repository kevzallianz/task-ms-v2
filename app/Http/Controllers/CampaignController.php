<?php

namespace App\Http\Controllers;

use App\Mail\CampaignMemberJoined;
use App\Models\Campaign;
use App\Models\CampaignMember;
use App\Models\CampaignProject;
use App\Models\CampaignProjectActivity;
use App\Models\CampaignTask;
use App\Models\CampaignTaskMember;
use App\Models\CampaignTaskRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CampaignController extends Controller
{
    public function index()
    {
        $user = request()->user();

        // Get all campaigns the user is a member of
        $campaigns = Campaign::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with([
            'projects',
            'members',
        ])->get();

        // Get all tasks for each campaign with optional status filter
        foreach ($campaigns as $campaign) {
            $query = CampaignTask::where('campaign_id', $campaign->id)
                ->with(['taskMembers.campaignMember.user', 'project']);

            // Apply status filter if provided
            if ($status = request()->get('campaign_'.$campaign->id.'_status')) {
                $query->where('status', $status);
            }

            $campaign->allTasks = $query->orderByDesc('created_at')->get();
            $campaign->filterStatus = request()->get('campaign_'.$campaign->id.'_status');
        }

        return view('user.campaigns.index', [
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Import tasks in bulk from parsed client-side data.
     * Expects a JSON payload: { tasks: [ { assigned_members: ["Name"], title, description, start_date, target_date, status, completed_at } ] }
     */
    public function importTasks(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.assigned_members' => 'nullable|array',
            'tasks.*.assigned_members.*' => 'nullable|string',
            'tasks.*.start_date' => 'nullable|date',
            'tasks.*.target_date' => 'nullable|date',
            'tasks.*.status' => 'required|in:planning,ongoing,on_hold,accomplished',
            'tasks.*.completed_at' => 'nullable|date',
        ]);

        // Ensure the requester is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $created = 0;
        foreach ($validated['tasks'] as $row) {
            $task = $campaign->tasks()->create([
                'title' => $row['title'],
                'description' => $row['description'] ?? null,
                'start_date' => $row['start_date'] ?? null,
                'target_date' => $row['target_date'] ?? null,
                'status' => $row['status'],
                'completed_at' => $row['completed_at'] ?? null,
            ]);

            // Attach members by searching their names within the campaign members
            if (! empty($row['assigned_members']) && is_array($row['assigned_members'])) {
                foreach ($row['assigned_members'] as $memberName) {
                    $name = trim($memberName);
                    if ($name === '') {
                        continue;
                    }

                    $campaignMember = $campaign->campaignMembers()->whereHas('user', function ($q) use ($name) {
                        $q->where('name', $name);
                    })->first();

                    if (! $campaignMember) {
                        // try partial match
                        $campaignMember = $campaign->campaignMembers()->whereHas('user', function ($q) use ($name) {
                            $q->where('name', 'like', "%{$name}%");
                        })->first();
                    }

                    if ($campaignMember) {
                        CampaignTaskMember::create([
                            'campaign_task_id' => $task->id,
                            'campaign_member_id' => $campaignMember->id,
                        ]);
                    }
                }
            }

            $created++;
        }

        return response()->json(['success' => true, 'created' => $created]);
    }

    /**
     * Get remarks for a campaign task
     */
    public function getTaskRemarks(Request $request, Campaign $campaign, CampaignTask $task)
    {
        if ($task->campaign_id !== $campaign->id) {
            return response()->json([
                'message' => 'Task not found in this campaign.',
            ], 404);
        }

        // Ensure user is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $remarks = $task->remarks()->with('user')->orderBy('created_at', 'desc')->get();

        return response()->json(['remarks' => $remarks]);
    }

    /**
     * Add a remark to a campaign task.
     */
    public function addTaskRemark(Request $request, Campaign $campaign, CampaignTask $task)
    {
        if ($task->campaign_id !== $campaign->id) {
            return response()->json([
                'message' => 'Task not found in this campaign.',
            ], 404);
        }

        // Ensure user is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'remark' => 'required|string|max:500',
        ], [
            'remark.required' => 'Remark is required',
            'remark.max' => 'Remark must not exceed 500 characters',
        ]);

        try {
            CampaignTaskRemark::create([
                'campaign_task_id' => $task->id,
                'user_id' => Auth::id(),
                'remarks' => $validated['remark'],
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
     * Store a newly created task for a campaign
     */
    public function storeTask(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'assigned_member_ids' => 'nullable|array',
            'assigned_member_ids.*' => 'exists:campaign_members,id',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date',
            'status' => 'required|in:planning,ongoing,on_hold,accomplished',
        ]);

        // Check if user is a member of this campaign
        $user = request()->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();

        if (! $isMember) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Create the task
        $task = $campaign->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'status' => $validated['status'],
        ]);

        // Attach campaign members if provided
        if (! empty($validated['assigned_member_ids'])) {
            foreach ($validated['assigned_member_ids'] as $memberId) {
                CampaignTaskMember::create([
                    'campaign_task_id' => $task->id,
                    'campaign_member_id' => $memberId,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'task' => $task->load('taskMembers.campaignMember.user'),
        ]);
    }

    /**
     * Update a campaign task.
     */
    public function updateTask(Request $request, Campaign $campaign, CampaignTask $task)
    {
        $validated = $request->validate([
            // allow partial updates: title only required when present
            'title' => 'sometimes|required|string|max:100',
            'description' => 'sometimes|nullable|string',
            'assigned_member_ids' => 'sometimes|nullable|array',
            'assigned_member_ids.*' => 'nullable|exists:campaign_members,id',
            'start_date' => 'sometimes|nullable|date',
            'target_date' => 'sometimes|nullable|date',
            'completed_at' => 'sometimes|nullable|date',
            'status' => 'sometimes|required|in:planning,ongoing,on_hold,accomplished',
        ], [
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title must not exceed 50 characters.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Invalid status selected.',
        ]);

        // Ensure task belongs to this campaign
        if ($task->campaign_id !== $campaign->id) {
            return response()->json(['message' => 'Task not found in this campaign.'], 404);
        }

        // Ensure user is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update only provided fields so bulk-assign can update members only
        $updateData = [];
        if (array_key_exists('title', $validated)) {
            $updateData['title'] = $validated['title'];
        }
        if (array_key_exists('description', $validated)) {
            $updateData['description'] = $validated['description'];
        }
        if (array_key_exists('start_date', $validated)) {
            $updateData['start_date'] = $validated['start_date'];
        }
        if (array_key_exists('target_date', $validated)) {
            $updateData['target_date'] = $validated['target_date'];
        }
        if (array_key_exists('completed_at', $validated)) {
            $updateData['completed_at'] = $validated['completed_at'];
        }
        if (array_key_exists('status', $validated)) {
            $updateData['status'] = $validated['status'];
        }

        if (! empty($updateData)) {
            $task->update($updateData);
        }

        // Update assigned members if provided
        if (array_key_exists('assigned_member_ids', $validated)) {
            $task->assignedMembers()->sync($validated['assigned_member_ids'] ?? []);
        }

        // Log activity if task belongs to a project
        if ($task->campaign_project_id && ! empty($updateData)) {
            CampaignProjectActivity::create([
                'campaign_project_id' => $task->campaign_project_id,
                'user_id' => $user->id,
                'action_type' => 'task_updated',
                'description' => "updated task '{$task->title}'",
                'metadata' => [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'changes' => $updateData,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task,
        ]);
    }

    /**
     * Update the status of a campaign task.
     */
    public function updateTaskStatus(Request $request, Campaign $campaign, CampaignTask $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:planning,ongoing,on_hold,accomplished',
        ], [
            'status.required' => 'Please select a status.',
            'status.in' => 'Invalid status selected.',
        ]);

        // Ensure task belongs to this campaign
        if ($task->campaign_id !== $campaign->id) {
            return response()->json(['error' => 'Task not found in this campaign.'], 404);
        }

        // Ensure user is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $oldStatus = $task->status;
        $task->status = $validated['status'];
        $task->completed_at = $validated['status'] === 'accomplished' ? now() : null;
        $task->save();

        // Log activity if task belongs to a project
        if ($task->campaign_project_id) {
            CampaignProjectActivity::create([
                'campaign_project_id' => $task->campaign_project_id,
                'user_id' => $user->id,
                'action_type' => 'task_status_changed',
                'description' => "changed task '{$task->title}' status from {$oldStatus} to {$validated['status']}",
                'metadata' => [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task status updated',
            'task' => $task,
        ]);
    }

    /**
     * Delete a campaign task.
     */
    public function deleteTask(Request $request, Campaign $campaign, CampaignTask $task)
    {
        // Ensure task belongs to this campaign
        if ($task->campaign_id !== $campaign->id) {
            return response()->json(['message' => 'Task not found in this campaign.'], 404);
        }

        // Ensure user is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Log activity if task belongs to a project (before deletion)
        if ($task->campaign_project_id) {
            CampaignProjectActivity::create([
                'campaign_project_id' => $task->campaign_project_id,
                'user_id' => $user->id,
                'action_type' => 'task_deleted',
                'description' => "deleted task '{$task->title}'",
                'metadata' => [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                ],
            ]);
        }

        // Delete the task (cascade will handle task members)
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Add a member to a campaign.
     */
    public function addMember(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user is a member of this campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();

        if (! $isMember) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if user is already a member
        $newMember = $campaign->members()->where('user_id', $validated['user_id'])->first();
        if ($newMember) {
            return response()->json(['error' => 'User is already a member of this campaign'], 400);
        }

        // Add member to campaign
        /** @var \App\Models\User|null $memberUser */
        $memberUser = \App\Models\User::find($validated['user_id']);
        $campaign->campaignMembers();

        // Send welcome email to new member
        if ($memberUser && $memberUser->email) {
            Mail::to($memberUser->email)->send(new CampaignMemberJoined($campaign, $memberUser));
        }

        return response()->json([
            'success' => true,
            'message' => 'Member added successfully',
        ]);
    }

    /**
     * Store a campaign project (CampaignProject)
     */
    public function storeProject(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date',
            'status' => 'nullable|in:planning,ongoing,on_hold,accomplished',
        ]);

        // Ensure requester is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $project = CampaignProject::create([
            'campaign_id' => $campaign->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'status' => $validated['status'] ?? 'planning',
        ]);

        // Log activity
        CampaignProjectActivity::create([
            'campaign_project_id' => $project->id,
            'user_id' => $user->id,
            'action_type' => 'created',
            'description' => 'created this project',
            'metadata' => [
                'project_title' => $project->title,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project created',
            'project' => $project,
        ], 201);
    }

    /**
     * Show a campaign project page with related tasks
     */
    public function viewProject(Request $request, Campaign $campaign, CampaignProject $project)
    {
        // ensure project belongs to campaign
        if ($project->campaign_id !== $campaign->id) {
            abort(404);
        }

        // Ensure user is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return redirect()->route('user.campaign')->with('error', 'Unauthorized');
        }

        $tasks = $campaign->tasks()->where('campaign_project_id', $project->id)
            ->with('taskMembers.campaignMember.user')
            ->orderByDesc('start_date')
            ->orderByDesc('created_at')
            ->get();

        // Load activities with user relationship
        $project->load(['activities' => function ($query) {
            $query->with('user')->orderByDesc('created_at');
        }]);

        return view('user.campaigns.project', [
            'campaign' => $campaign,
            'project' => $project,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a task for a specific campaign project
     */
    public function storeProjectTask(Request $request, Campaign $campaign, CampaignProject $project)
    {
        // ensure project belongs to campaign
        if ($project->campaign_id !== $campaign->id) {
            return response()->json(['message' => 'Project not found in this campaign.'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'assigned_member_ids' => 'nullable|array',
            'assigned_member_ids.*' => 'exists:campaign_members,id',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date',
            'status' => 'required|in:planning,ongoing,on_hold,accomplished',
        ]);

        // Ensure user is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Create the task attached to the project
        $task = $campaign->tasks()->create([
            'campaign_project_id' => $project->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'status' => $validated['status'],
        ]);

        // Attach campaign members if provided
        if (! empty($validated['assigned_member_ids'])) {
            foreach ($validated['assigned_member_ids'] as $memberId) {
                CampaignTaskMember::create([
                    'campaign_task_id' => $task->id,
                    'campaign_member_id' => $memberId,
                ]);
            }
        }

        // Log activity
        CampaignProjectActivity::create([
            'campaign_project_id' => $project->id,
            'user_id' => $user->id,
            'action_type' => 'task_added',
            'description' => "added task '{$task->title}'",
            'metadata' => [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'task_status' => $task->status,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'task' => $task->load('taskMembers.campaignMember.user'),
        ], 201);
    }

    /**
     * Update the status of a campaign project
     */
    public function updateProjectStatus(Request $request, Campaign $campaign, CampaignProject $project)
    {
        // ensure project belongs to campaign
        if ($project->campaign_id !== $campaign->id) {
            return response()->json(['message' => 'Project not found in this campaign.'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:planning,ongoing,on_hold,accomplished',
        ], [
            'status.required' => 'Please select a status.',
            'status.in' => 'Invalid status selected.',
        ]);

        // Ensure user is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $oldStatus = $project->status;
        $project->update([
            'status' => $validated['status'],
        ]);

        // Log activity
        CampaignProjectActivity::create([
            'campaign_project_id' => $project->id,
            'user_id' => $user->id,
            'action_type' => 'status_changed',
            'description' => "changed project status from {$oldStatus} to {$validated['status']}",
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project status updated successfully',
            'project' => $project,
        ]);
    }

    /**
     * Update a campaign project
     */
    public function updateProject(Request $request, Campaign $campaign, CampaignProject $project)
    {
        // ensure project belongs to campaign
        if ($project->campaign_id !== $campaign->id) {
            return response()->json(['message' => 'Project not found in this campaign.'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date',
            'status' => 'nullable|in:planning,ongoing,on_hold,accomplished',
        ]);

        // Ensure user is a member of the campaign
        $user = $request->user();
        $isMember = $campaign->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $changes = [];
        if ($project->title !== $validated['title']) {
            $changes['title'] = ['from' => $project->title, 'to' => $validated['title']];
        }
        if (isset($validated['start_date']) && $project->start_date !== $validated['start_date']) {
            $changes['start_date'] = ['from' => $project->start_date, 'to' => $validated['start_date']];
        }
        if (isset($validated['target_date']) && $project->target_date !== $validated['target_date']) {
            $changes['target_date'] = ['from' => $project->target_date, 'to' => $validated['target_date']];
        }

        $project->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'status' => $validated['status'] ?? $project->status,
        ]);

        // Log activity if there were changes
        if (! empty($changes)) {
            CampaignProjectActivity::create([
                'campaign_project_id' => $project->id,
                'user_id' => $user->id,
                'action_type' => 'updated',
                'description' => 'updated project details',
                'metadata' => ['changes' => $changes],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'project' => $project,
        ]);
    }

    /**
     * Delete a campaign project
     */
    public function destroyProject(Request $request, Campaign $campaign, CampaignProject $project)
    {
        // ensure project belongs to campaign
        if ($project->campaign_id !== $campaign->id) {
            return response()->json(['message' => 'Project not found in this campaign.'], 404);
        }

        // Ensure user is a member of the campaign with appropriate access
        $user = $request->user();
        $member = $campaign->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only 'all' access level can delete projects
        $accessLevel = $member->pivot->access_level ?? 'viewer';
        if ($accessLevel !== 'all') {
            return response()->json(['error' => 'Unauthorized. Only members with full access can delete projects.'], 403);
        }

        // Store project info before deletion for activity log
        $projectTitle = $project->title;
        $projectId = $project->id;

        // Delete all tasks associated with this project
        $campaign->tasks()->where('campaign_project_id', $project->id)->delete();

        // Log activity before deletion (will be deleted with project due to cascade)
        CampaignProjectActivity::create([
            'campaign_project_id' => $projectId,
            'user_id' => $user->id,
            'action_type' => 'deleted',
            'description' => "deleted project '{$projectTitle}'",
            'metadata' => [
                'project_title' => $projectTitle,
            ],
        ]);

        // Delete the project
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully',
        ]);
    }

    /**
     * Update a campaign member's access level. Only allowed for members with "all" access.
     */
    public function updateMemberAccess(Request $request, Campaign $campaign, CampaignMember $campaignMember)
    {
        $validated = $request->validate([
            'access_level' => 'required|in:viewer,editor,all',
        ], [
            'access_level.required' => 'Please choose an access level.',
            'access_level.in' => 'Invalid access level selected.',
        ]);

        // Ensure the member belongs to the campaign
        if ($campaignMember->campaign_id !== $campaign->id) {
            return response()->json([
                'message' => 'Member not found in this campaign.',
            ], 404);
        }

        // Ensure the current user is part of the campaign with "all" access
        $currentMember = $campaign->campaignMembers()
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $currentMember) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($currentMember->access_level !== 'all') {
            return response()->json([
                'message' => 'You need "all" access to update member permissions.',
            ], 403);
        }

        $campaignMember->update([
            'access_level' => $validated['access_level'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member access level updated.',
            'member' => $campaignMember->load('user'),
        ]);
    }
}
