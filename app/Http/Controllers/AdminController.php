<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignProject;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function campaigns(Request $request)
    {
        $campaigns = Campaign::withCount('members')
            ->with(['projects' => function ($query) {
                $query->whereNotIn('status', ['accomplished', 'on_hold'])
                    ->orderBy('created_at', 'desc');
            }])
            ->get();

        return view('admin.campaigns.campaigns', ['campaigns' => $campaigns]);
    }

    public function show(Campaign $campaign)
    {
        $campaign->load([
            'campaignMembers.user',
            'projects',
            'tasks.project',
            'tasks.assignedMembers.user',
        ]);

        return view('admin.campaigns.show', ['campaign' => $campaign]);
    }

    public function showProject(Campaign $campaign, CampaignProject $project)
    {
        if ($project->campaign_id !== $campaign->id) {
            abort(404);
        }

        $tasks = $campaign->tasks()
            ->where('campaign_project_id', $project->id)
            ->with(['assignedMembers.user'])
            ->orderByDesc('created_at')
            ->get();

        $project->load(['activities.user']);

        return view('admin.campaigns.project', [
            'campaign' => $campaign,
            'project'  => $project,
            'tasks'    => $tasks,
        ]);
    }

    public function projects(Request $request)
    {
        $campaigns = Campaign::with(['projects' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->get();

        return view('admin.campaign-projects', ['campaigns' => $campaigns]);
    }
}
