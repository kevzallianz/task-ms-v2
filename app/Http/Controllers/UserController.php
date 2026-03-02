<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Campaign;
use App\Models\CampaignTask;
use App\Models\ProjectTask;
use App\Models\CampaignTaskMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->count();

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
            ->with(['campaign', 'taskMembers.campaignMember.user'])
            ->limit(5)
            ->get();

        // Recent Projects
        $recentProjects = Project::whereIn('campaign_id', $userCampaigns)
            ->orWhere('user_id', $user->id)
            ->with('campaign')
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Overdue Tasks
        $overdueCampaignTasks = CampaignTask::whereIn('campaign_id', $userCampaigns)
            ->where('status', '!=', 'accomplished')
            ->whereNotNull('target_date')
            ->where('target_date', '<', now())
            ->count();

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
        ]);
    }
    public function tasks()
    {
        return view('user.tasks');
    }
    public function projects(Request $request)
    {
        $campaignId = DB::table('campaign_members')
            ->where('user_id', $request->user()->id)
            ->value('campaign_id');

        $projects = Project::where('campaign_id', $campaignId)
            ->orWhereHas('contributors', function ($query) use ($campaignId) {
                $query->where('campaign_id', $campaignId);
            })
            ->latest('created_at')
            ->get();

        $campaigns = DB::table('campaigns')
            ->where('id', $campaignId)
            ->get();

        return view('user.projects.index', [
            'projects' => $projects,
            'campaigns' => $campaigns,
        ]);
    }
}
