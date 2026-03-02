@extends('layouts.user-layout')

@section('user-content')
<main class="flex flex-col gap-6">
    @php

    $statistics = [
    1 => [
    'label' => 'Accomplished Tasks',
    'value' => $totalAccomplishedAssigned,
    'icon' => 'check-circle',
    'color' => 'green',
    ],
    2 => [
    'label' => 'Total Projects',
    'value' => $totalProjects,
    'icon' => 'folder',
    'color' => 'secondary',
    ],
    3 => [
    'label' => 'Campaign Tasks',
    'value' => $totalCampaignTasks,
    'icon' => 'clipboard-document-list',
    'color' => 'blue',
    ],
    4 => [
    'label' => 'Project Tasks',
    'value' => $totalProjectTasks,
    'icon' => 'clipboard-document-check',
    'color' => 'purple',
    ],
    ]

    @endphp
    {{-- Page Header --}}
    <article class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Welcome Back, <span class="text-primary">{{ auth()->user()->name }}</span></h1>
            <p class="text-sm text-gray-600">Here's what's happening with your campaigns and projects</p>
        </div>
        <div class="text-sm text-gray-500">
            <span class="font-medium">{{ now()->format('l, F j, Y') }}</span>
        </div>
    </article>

    {{-- Statistics Cards --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($statistics as $statistic)
        <div class="bg-white rounded-lg p-6 border border-gray-200 transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ $statistic['label'] }}</p>
                    <p class="text-xl font-bold text-{{ $statistic['color'] }} mt-2">{{ $statistic['value'] }}</p>
                </div>
                <div class="bg-{{ $statistic['color'] }}/10 p-3 rounded-lg">
                    <x-heroicon-o-{{ $statistic['icon'] }} class="w-5 h-5 text-{{ $statistic['color'] }}" />
                </div>
            </div>
        </div>
        @endforeach
    </section>

    {{-- Status Overview & Alerts --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Campaign Tasks Status --}}
        <div class="bg-white rounded-lg p-6 border border-gray-200 col-span-2">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-chart-bar class="w-5 h-5 text-primary" />
                <h2 class="text-base font-semibold text-primary">Campaign Tasks</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Planning</span>
                    <span class="text-lg font-bold text-blue-600">{{ $campaignTasksStatus->get('planning', 0) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Ongoing</span>
                    <span class="text-lg font-bold text-yellow-600">{{ $campaignTasksStatus->get('ongoing', 0) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">On Hold</span>
                    <span class="text-lg font-bold text-orange-600">{{ $campaignTasksStatus->get('on_hold', 0) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Accomplished</span>
                    <span class="text-lg font-bold text-green-600">{{ $campaignTasksStatus->get('accomplished', 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Alerts & Notifications --}}
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-bell class="w-5 h-5 text-red-600" />
                <h2 class="text-base font-semibold text-primary">Alerts</h2>
            </div>
            <div class="space-y-4">
                @if($overdueCampaignTasks > 0)
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                        <div>
                            <p class="font-semibold text-red-800 text-sm">Overdue Tasks</p>
                            <p class="text-sm text-red-700 mt-1">You have <span class="font-bold">{{ $overdueCampaignTasks }}</span> overdue campaign task(s)</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($upcomingCampaignTasks->count() > 0)
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-clock class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                        <div>
                            <p class="font-semibold text-blue-800 text-sm">Upcoming Deadlines</p>
                            <p class="text-sm text-blue-700 mt-1">{{ $upcomingCampaignTasks->count() }} task(s) due soon</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($overdueCampaignTasks == 0 && $upcomingCampaignTasks->count() == 0)
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
                        <div>
                            <p class="font-semibold text-green-800 text-sm">All Clear!</p>
                            <p class="text-sm text-green-700 mt-1">No overdue tasks or urgent deadlines</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Recent Activity --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Upcoming Tasks --}}
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-5 h-5 text-primary" />
                        <h2 class="text-base font-semibold text-primary">Upcoming Campaign Tasks</h2>
                    </div>
                    <a href="{{ route('user.campaign') }}" class="text-sm text-primary hover:underline">View All</a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($upcomingCampaignTasks as $task)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 truncate">{{ $task->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-medium">Campaign:</span> {{ $task->campaign->name }}
                            </p>
                            @if($task->taskMembers->count() > 0)
                            <div class="flex items-center gap-2 mt-2">
                                <x-heroicon-o-user class="w-4 h-4 text-gray-400" />
                                <div class="flex flex-wrap gap-1">
                                    @foreach($task->taskMembers as $taskMember)
                                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                        {{ $taskMember->campaignMember->user->name }}
                                    </span>
                                    @endforeach
                                    @if($task->taskMembers->count() > 3)
                                    <span class="text-xs text-gray-500">+{{ $task->taskMembers->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            @php
                            $secondsUntil = now()->diffInSeconds($task->target_date, false);
                            $daysUntil = $secondsUntil > 0 ? (int) ceil($secondsUntil / 86400) : 0;
                            $isUrgent = $secondsUntil >= 0 && $secondsUntil <= (3 * 86400);
                                @endphp
                                <span class="text-xs font-medium {{ $isUrgent ? 'text-red-600' : 'text-gray-600' }}">
                                {{ now()->parse($task->target_date)->format('M d, Y') }}
                                </span>
                                @if($isUrgent)
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">
                                    @if($daysUntil >= 1)
                                    Due in {{ $daysUntil }} {{ $daysUntil == 1 ? 'day' : 'days' }}
                                    @else
                                    Due today
                                    @endif
                                </span>
                                @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <x-heroicon-o-calendar-days class="w-12 h-12 text-gray-300 mx-auto mb-2" />
                    <p class="text-sm text-gray-500">No upcoming campaign tasks</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Projects --}}
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-folder class="w-5 h-5 text-primary" />
                        <h2 class="text-base font-semibold text-primary">Recent Projects</h2>
                    </div>
                    <a href="{{ route('user.projects') }}" class="text-sm text-primary hover:underline">View All</a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentProjects as $project)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 truncate">{{ $project->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                {{ $project->description ?? 'No description' }}
                            </p>
                            @if($project->campaign)
                            <p class="text-xs text-gray-500 mt-2">
                                <span class="font-medium">Campaign:</span> {{ $project->campaign->name }}
                            </p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            @php
                            $statusColors = [
                            'planning' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'ongoing' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                            'on_hold' => 'bg-orange-100 text-orange-700 border-orange-200',
                            'accomplished' => 'bg-green-100 text-green-700 border-green-200',
                            ];
                            @endphp
                            <span class="text-xs px-2 py-1 rounded border {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $project->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <x-heroicon-o-folder-open class="w-12 h-12 text-gray-300 mx-auto mb-2" />
                    <p class="text-sm text-gray-500">No recent projects</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Quick Actions --}}
    <section class="bg-linear-to-r from-primary to-secondary rounded-lg p-6 text-white">
        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('user.campaign') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg p-4 transition flex items-center gap-3 group">
                <div class="bg-white/20 p-3 rounded-lg">
                    <x-heroicon-o-users class="w-6 h-6" />
                </div>
                <div>
                    <p class="font-semibold">View Campaigns</p>
                    <p class="text-sm text-white/80">Manage your team campaigns</p>
                </div>
            </a>
            <a href="{{ route('user.projects') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg p-4 transition flex items-center gap-3 group">
                <div class="bg-white/20 p-3 rounded-lg">
                    <x-heroicon-o-folder class="w-6 h-6" />
                </div>
                <div>
                    <p class="font-semibold">View Projects</p>
                    <p class="text-sm text-white/80">Browse all your projects</p>
                </div>
            </a>
        </div>
    </section>
</main>
@endsection