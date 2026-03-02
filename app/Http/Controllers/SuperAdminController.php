<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignMember;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function campaigns(Request $request)
    {
        $search = trim($request->input('q', ''));

        $query = Campaign::with(['members' => function ($query) {
                $query->select('users.id', 'name', 'email');
            }])
            ->withCount('members')
            ->latest('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $campaigns = $query->paginate(25)->appends($request->query());

        return view('superadmin.campaign', [
            'campaigns' => $campaigns,
            'search' => $search,
        ]);
    }
    public function users(Request $request)
    {
        $search = trim($request->input('q', ''));

        $query = User::with(['campaignMember.campaign'])
            ->latest('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%");
            });
        }

        $users = $query->paginate(25)->appends($request->query());
        $campaignOptions = Campaign::orderBy('name')->get(['id', 'name', 'description']);

        return view('superadmin.users', [
            'users' => $users,
            'search' => $search,
            'campaignOptions' => $campaignOptions,
        ]);
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:user,admin,superadmin',
        ], [
            'role.required' => 'Please choose a role.',
            'role.in' => 'Invalid role selected.',
        ]);

        // Prevent removing the last superadmin
        $isDemotingLastSuperadmin = $user->role === 'superadmin'
            && $validated['role'] !== 'superadmin'
            && User::where('role', 'superadmin')->count() <= 1;

        if ($isDemotingLastSuperadmin) {
            return response()->json([
                'message' => 'Cannot remove the last superadmin.',
            ], 422);
        }

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'user' => $user,
        ]);
    }

    public function assignUserToCampaign(Request $request, User $user)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'access_level' => 'required|in:viewer,editor,all',
        ], [
            'campaign_id.required' => 'Please select a campaign.',
            'campaign_id.exists' => 'Selected campaign not found.',
            'access_level.required' => 'Please choose an access level.',
            'access_level.in' => 'Invalid access level selected.',
        ]);

        $membership = CampaignMember::updateOrCreate(
            ['user_id' => $user->id],
            [
                'campaign_id' => $validated['campaign_id'],
                'access_level' => $validated['access_level'],
            ]
        );

        $membership->load('campaign');

        return response()->json([
            'success' => true,
            'message' => 'User assigned to campaign.',
            'campaign' => $membership->campaign,
            'access_level' => $membership->access_level,
        ]);
    }

    public function assignUsersToCampaignBulk(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'campaign_id' => 'required|exists:campaigns,id',
            'access_level' => 'required|in:viewer,editor,all',
        ], [
            'user_ids.required' => 'Select at least one user.',
            'user_ids.array' => 'Invalid users payload.',
            'campaign_id.required' => 'Please select a campaign.',
            'campaign_id.exists' => 'Selected campaign not found.',
            'access_level.required' => 'Please choose an access level.',
            'access_level.in' => 'Invalid access level selected.',
        ]);

        $userIds = array_unique($validated['user_ids']);

        foreach ($userIds as $id) {
            CampaignMember::updateOrCreate(
                ['user_id' => $id],
                [
                    'campaign_id' => $validated['campaign_id'],
                    'access_level' => $validated['access_level'],
                ]
            );
        }

        $campaign = Campaign::find($validated['campaign_id']);

        return response()->json([
            'success' => true,
            'message' => 'Users assigned to campaign.',
            'campaign' => $campaign,
            'access_level' => $validated['access_level'],
            'count' => count($userIds),
        ]);
    }

    public function campaignMembers(Campaign $campaign)
    {
        $members = $campaign->members()
            ->select('users.id', 'users.name', 'users.email')
            ->withPivot('access_level')
            ->orderBy('users.name')
            ->paginate(25);

        return view('superadmin.campaign-members', [
            'campaign' => $campaign,
            'members' => $members,
        ]);
    }

    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $campaign = Campaign::create($validated);

        return response()->json([
            'message' => 'Campaign created successfully',
            'campaign' => $campaign,
        ], 201);
    }
}