<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignProject;
use App\Models\CampaignTask;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function overview(Request $request)
    {
        $user = $request->user();

        // Get user's campaigns
        $userCampaigns = Campaign::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->pluck('id');

        // Statistics
        $totalCampaigns = $userCampaigns->count();
        $totalProjects = Project::whereIn('campaign_id', $userCampaigns)
            ->orWhere('user_id', $user->id)
            ->count()
            + CampaignProject::whereIn('campaign_id', $userCampaigns)->count();

        $totalCampaignTasks = CampaignTask::whereIn('campaign_id', $userCampaigns)->count();

        $totalProjectTasks = ProjectTask::whereHas('project', function ($query) use ($userCampaigns, $user) {
            $query->whereIn('campaign_id', $userCampaigns)
                ->orWhere('user_id', $user->id);
        })->count();

        // Completed tasks assigned to the current user (campaign tasks)
        $totalAccomplishedAssigned = CampaignTask::where('status', 'accomplished')
            ->whereHas('taskMembers.campaignMember', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        // Tasks assigned to the current user (across all their campaigns)
        $myTasksAssignedQuery = CampaignTask::whereIn('campaign_id', $userCampaigns)
            ->whereHas('taskMembers.campaignMember', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        $myTasksCount = (clone $myTasksAssignedQuery)->where('status', '!=', 'accomplished')->count();

        $myOpenTasks = (clone $myTasksAssignedQuery)
            ->where('status', '!=', 'accomplished')
            ->with(['campaign:id,name', 'project:id,title'])
            ->orderByRaw('target_date IS NULL, target_date ASC')
            ->limit(6)
            ->get();

        // Task Status Breakdown (Campaign Tasks)
        $campaignTasksStatus = CampaignTask::whereIn('campaign_id', $userCampaigns)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Recent Campaign Tasks (upcoming deadlines)
        $upcomingCampaignTasks = CampaignTask::whereIn('campaign_id', $userCampaigns)
            ->where('status', '!=', 'accomplished')
            ->whereNotNull('target_date')
            ->where('target_date', '>=', now())
            ->orderBy('target_date', 'asc')
            ->with(['campaign', 'taskMembers.campaignMember.user', 'project'])
            ->limit(5)
            ->get();

        // Recent Projects
        $recentProjects = Project::whereIn('campaign_id', $userCampaigns)
            ->orWhere('user_id', $user->id)
            ->with('campaign')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(fn ($p) => tap($p, fn ($m) => $m->type = 'project'))
            ->concat(
                CampaignProject::whereIn('campaign_id', $userCampaigns)
                    ->with('campaign')
                    ->latest('created_at')
                    ->limit(5)
                    ->get()
                    ->map(fn ($p) => tap($p, fn ($m) => $m->type = 'campaign_project'))
            )
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        // Overdue Tasks
        $overdueCampaignTasksList = CampaignTask::whereIn('campaign_id', $userCampaigns)
            ->where('status', '!=', 'accomplished')
            ->whereNotNull('target_date')
            ->where('target_date', '<', now())
            ->with(['campaign:id,name', 'project:id,title'])
            ->orderBy('target_date', 'asc')
            ->get();
        $overdueCampaignTasks = $overdueCampaignTasksList->count();

        // Calendar tasks — campaign tasks + project tasks assigned to the campaign
        $calendarCampaignTasks = CampaignTask::whereIn('campaign_id', $userCampaigns)
            ->whereNotNull('target_date')
            ->select('id', 'title', 'status', 'start_date', 'target_date', 'completed_at', 'campaign_id')
            ->with('campaign:id,name')
            ->get()
            ->map(fn ($task) => [
                'type' => 'campaign',
                'title' => $task->title,
                'status' => $task->status,
                'start_date' => $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('Y-m-d') : null,
                'target_date' => \Carbon\Carbon::parse($task->target_date)->format('Y-m-d'),
                'completed_at' => $task->completed_at ? \Carbon\Carbon::parse($task->completed_at)->format('Y-m-d') : null,
                'source' => $task->campaign?->name,
                'url' => route('user.campaign'),
            ]);

        $calendarProjectTasks = ProjectTask::whereIn('assigned_campaign_id', $userCampaigns)
            ->whereNotNull('target_date')
            ->select('id', 'title', 'status', 'start_date', 'target_date', 'project_id')
            ->with('project:id,name')
            ->get()
            ->map(fn ($task) => [
                'type' => 'project',
                'title' => $task->title,
                'status' => $task->status,
                'start_date' => $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('Y-m-d') : null,
                'target_date' => \Carbon\Carbon::parse($task->target_date)->format('Y-m-d'),
                'completed_at' => null,
                'source' => $task->project?->name,
                'url' => $task->project_id ? route('projects.view', $task->project_id) : route('user.projects'),
            ]);

        $calendarTasks = $calendarCampaignTasks->concat($calendarProjectTasks);

        return view('user.overview', [
            'totalCampaigns' => $totalCampaigns,
            'totalProjects' => $totalProjects,
            'totalCampaignTasks' => $totalCampaignTasks,
            'totalProjectTasks' => $totalProjectTasks,
            'totalAccomplishedAssigned' => $totalAccomplishedAssigned,
            'campaignTasksStatus' => $campaignTasksStatus,
            'upcomingCampaignTasks' => $upcomingCampaignTasks,
            'recentProjects' => $recentProjects,
            'overdueCampaignTasks' => $overdueCampaignTasks,
            'overdueCampaignTasksList' => $overdueCampaignTasksList,
            'calendarTasks' => $calendarTasks,
            'myTasksCount' => $myTasksCount,
            'myOpenTasks' => $myOpenTasks,
        ]);
    }

    public function tasks()
    {
        return view('user.tasks');
    }

    public function profile(Request $request)
    {
        return view('user.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,'.$user->id,
            'email'    => 'required|email|max:150|unique:users,email,'.$user->id,
            'bio'      => 'nullable|string|max:500',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return response()->json(['message' => 'Profile updated successfully.']);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function removeAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return response()->json(['message' => 'Profile photo removed.']);
    }

    public function projects(Request $request)
    {
        $campaignId = DB::table('campaign_members')
            ->where('user_id', $request->user()->id)
            ->value('campaign_id');

        $search = $request->input('search');
        $status = $request->input('status');

        $projects = Project::where(function ($query) use ($campaignId) {
                $query->where('campaign_id', $campaignId)
                    ->orWhereHas('contributors', function ($q) use ($campaignId) {
                        $q->where('campaign_id', $campaignId);
                    });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->with(['user', 'campaign'])
            ->latest('created_at')
            ->get();

        $campaigns = DB::table('campaigns')
            ->where('id', $campaignId)
            ->get();

        return view('user.projects.index', [
            'projects' => $projects,
            'campaigns' => $campaigns,
            'filterSearch' => $search,
            'filterStatus' => $status,
        ]);
    }
}
