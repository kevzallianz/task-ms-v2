@extends('layouts.user-layout')

@section('user-content')
@php
    $userAccessLevel = request()->user()->campaignMember->access_level ?? 'viewer'
@endphp
<main class="grid grid-cols-1 gap-6" data-project-id="{{ $project->id }}">
    {{-- Page Header with Back Button --}}
    <article class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('user.projects') }}" class="flex items-center justify-center w-10 h-10 rounded-lg border border-secondary/30 hover:bg-gray-100 transition">
                <x-heroicon-o-arrow-left class="w-5 h-5 text-foreground" />
            </a>
            <div class="flex items-start gap-3">
                <div>
                    <h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
                        <x-heroicon-o-folder-open class="w-6 h-6" />
                        {{ $project->name }}
                    </h1>
                    <p class="text-sm text-gray-600">Manage and track your project progress</p>
                </div>
                {{-- Role Badge --}}
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full whitespace-nowrap flex items-center gap-1
                    {{ $isProjectOwner ? 'bg-purple-100 text-purple-700' : '' }}
                    {{ !$isProjectOwner && $userAccessLevel === 'editor' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ !$isProjectOwner && $userAccessLevel === 'all' ? 'bg-indigo-100 text-indigo-700' : '' }}
                    {{ !$isProjectOwner && $userAccessLevel === 'viewer' ? 'bg-foreground text-white' : '' }}">
                    @if ($isProjectOwner)
                        <x-heroicon-o-star class="w-3 h-3" />
                        Owner
                    @elseif ($userAccessLevel === 'editor')
                        Editor Access
                    @elseif ($userAccessLevel === 'all')
                        All Access
                    @else
                        View Access
                    @endif
                </span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
             @if ($isProjectOwner)
             <button id="addContributorBtnHeader" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">
                <x-heroicon-o-user-plus class="w-4 h-4" />
                Add Contributor
            </button>
             @endif
             @if ($isProjectOwner || in_array($userAccessLevel, ['editor', 'all']))
            <button id="addTaskBtnHeader" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition">
                <x-heroicon-o-plus class="w-4 h-4" />
                New Task
            </button>
             @endif
        </div>
    </article>

    <div class="grid grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="col-span-2 space-y-6">

            {{-- Project Overview Card --}}
            <section class="bg-white border border-secondary/30 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-foreground">Project Overview</h2>
                    @if ($isProjectOwner)
                    <button id="updateProjectStatusBtn" class="text-sm font-medium px-3 py-2 rounded-lg text-white bg-primary hover:text-white/80 transition flex items-center gap-1">
                        <x-heroicon-o-pencil class="w-4 h-4" />
                        Change Status
                    </button>
                    @endif
                </div>
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-foreground mb-2">Description</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ $project->description ?? 'No description provided.' }}
                    </p>
                </div>

                {{-- Project Details Grid --}}
                <div class="grid grid-cols-2 gap-6">
                    {{-- Start Date --}}
                    <div>
                        <h3 class="text-sm font-medium text-foreground mb-2">Start Date</h3>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                            <span class="text-sm text-gray-600">{{ date('M d, Y', strtotime($project->start_date)) }}</span>
                        </div>
                    </div>

                    {{-- Target Date --}}
                    <div>
                        <h3 class="text-sm font-medium text-foreground mb-2">Target Date</h3>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-flag class="w-4 h-4 text-gray-400" />
                            <span class="text-sm text-gray-600">
                                @if ($project->target_date)
                                    {{ date('M d, Y', strtotime($project->target_date)) }}
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <h3 class="text-sm font-medium text-foreground mb-2">Status</h3>
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full
                            {{ $project->status === 'planning' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $project->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $project->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $project->status === 'on_hold' ? 'bg-red-100 text-red-700' : '' }}">
                            <x-heroicon-o-arrow-path class="w-3 h-3" />
                            {{ ucwords(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>

                    {{-- Created Date --}}
                    <div>
                        <h3 class="text-sm font-medium text-foreground mb-2">Created</h3>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-clock class="w-4 h-4 text-gray-400" />
                            <span class="text-sm text-gray-600">{{ date('M d, Y', strtotime($project->created_at)) }}</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Project Tasks Section --}}
            <section class="bg-white border border-secondary/30 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-foreground">Tasks</h2>
                    @if ($isProjectOwner || in_array($userAccessLevel, ['editor', 'all']))
                    <button id="addTaskBtn" class="text-sm font-medium text-primary hover:text-primary/80 transition flex items-center gap-1">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Add Task
                    </button>
                    @endif
                </div>

                {{-- Tasks List with campaign tabs --}}
                <div class="space-y-5">
                    @php
                        $allTasks = $project->tasks ?? collect();
                        $tasksByCampaign = $allTasks->groupBy('assigned_campaign_id');
                        $activeCampaignId = 'all';
                    @endphp

                    @if ($allTasks->count() > 0)
                        {{-- Tabs --}}
                        <div class="flex flex-wrap gap-2 border-b border-secondary/30 pb-3 mb-4">
                            @php
                                $isAllActive = $activeCampaignId === 'all';
                            @endphp
                            <button
                                type="button"
                                data-campaign-tab="all"
                                class="px-3 py-1.5 text-sm font-medium rounded-lg border transition {{ $isAllActive ? 'bg-primary text-white border-primary' : 'border-secondary/30 text-foreground hover:bg-gray-100' }}">
                                All
                            </button>
                            @foreach ($taskCampaigns as $campaign)
                                @php
                                    $isActive = false;
                                @endphp
                                <button
                                    type="button"
                                    data-campaign-tab="{{ $campaign->id }}"
                                    class="px-3 py-1.5 text-sm font-medium rounded-lg border transition {{ $isActive ? 'bg-primary text-white border-primary' : 'border-secondary/30 text-foreground hover:bg-gray-100' }}">
                                    {{ $campaign->name }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Panels --}}
                        <div class="space-y-4">
                            {{-- All tasks panel --}}
                            <div data-campaign-panel="all" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-foreground">All Tasks</h3>
                                    <span class="text-xs text-gray-500">{{ $allTasks->count() }} task(s)</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach ($allTasks as $task)
                                        <x-projects.task-card :task="$task" :userAccessLevel="$userAccessLevel" :isProjectOwner="$isProjectOwner" />
                                    @endforeach
                                </div>
                            </div>

                            {{-- Campaign-specific panels --}}
                            @foreach ($taskCampaigns as $campaign)
                                @php
                                    $campaignTasks = $tasksByCampaign[$campaign->id] ?? collect();
                                @endphp
                                <div data-campaign-panel="{{ $campaign->id }}" class="space-y-2 hidden">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-foreground">{{ $campaign->name }}</h3>
                                        <span class="text-xs text-gray-500">{{ $campaignTasks->count() }} task(s)</span>
                                    </div>

                                    @if ($campaignTasks->count() > 0)
                                        <div class="space-y-3">
                                            @foreach ($campaignTasks as $task)
                                                <x-projects.task-card :task="$task" :userAccessLevel="$userAccessLevel" :isProjectOwner="$isProjectOwner" />
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-500">No tasks for this campaign yet.</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <x-heroicon-o-document-text class="w-12 h-12 text-gray-300 mb-2" />
                            <p class="text-sm text-gray-500">No tasks yet. Create your first task to get started.</p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Project Activity Section --}}
            <section class="bg-white border border-secondary/30 rounded-lg p-6 w-full">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                        <x-heroicon-o-clock class="w-5 h-5" />
                        Activity Timeline
                    </h2>
                    @if ($project->activities->count() > 0)
                        <span class="text-xs text-gray-500">{{ $project->activities->count() }} {{ Str::plural('activity', $project->activities->count()) }}</span>
                    @endif
                </div>

                @if ($project->activities->count() > 0)
                    <div class="space-y-0 max-h-100 overflow-y-auto">
                        @foreach ($project->activities->sortByDesc('created_at')->take(20) as $activity)
                            <x-projects.activity-item :activity="$activity" />
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <x-heroicon-o-clock class="w-12 h-12 text-gray-300 mb-2" />
                        <p class="text-sm text-gray-500">No activity yet. Project activities will appear here.</p>
                    </div>
                @endif
            </section>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-6">

            {{-- Project Stats Card --}}
            <section class="bg-white border border-secondary/30 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-foreground mb-4">Statistics</h3>

                @php
                    $tasks = $project->tasks ?? collect();
                    $totalTasks = $tasks->count();
                    $completedTasks = $tasks->where('status', 'completed')->count();
                    $pendingTasks = $totalTasks - $completedTasks;
                    $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                @endphp

                @if ($totalTasks > 0)
                <div class="flex flex-col items-center">
                    <div class="w-full max-w-[200px] mb-4">
                        <canvas id="projectStatsChart" data-completed="{{ $completedTasks }}" data-pending="{{ $pendingTasks }}"></canvas>
                    </div>
                    
                    <div class="w-full space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span class="text-xs font-medium text-gray-600">Completed</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600">{{ $completedTasks }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <span class="text-xs font-medium text-gray-600">Pending</span>
                            </div>
                            <span class="text-sm font-semibold text-yellow-600">{{ $pendingTasks }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                            <span class="text-xs font-medium text-gray-600">Total</span>
                            <span class="text-sm font-semibold text-primary">{{ $totalTasks }}</span>
                        </div>
                    </div>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <x-heroicon-o-chart-pie class="w-12 h-12 text-gray-300 mb-2" />
                    <p class="text-sm text-gray-500">No statistics yet.</p>
                    <p class="text-xs text-gray-400 mt-1">Create tasks to see statistics.</p>
                </div>
                @endif
            </section>

            {{-- Contributors Card --}}
            <section class="bg-white border border-secondary/30 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-foreground">Contributors</h3>
                    @if ($isProjectOwner)
                    <button id="addContributorBtn" class="text-sm font-medium text-primary hover:text-primary/80 transition flex items-center gap-1">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Add
                    </button>
                    @endif
                </div>

                <div class="flex flex-col gap-3">
                    {{-- Creator --}}
                    @if ($project->user)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-sm font-semibold text-primary">
                            {{ substr($project->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-foreground">{{ $project->user->name }}</p>
                            <span class="text-xs text-gray-500">Owner - {{ $project->campaign->name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                    @else
                    <div class="text-xs text-gray-500">No owner assigned</div>
                    @endif

                    {{-- Campaign Contributors --}}
                    @if (count($project->contributors ?? []) > 0)
                        @foreach ($project->contributors as $contributor)
                        <div class="flex items-center gap-3 justify-between">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-8 h-8 rounded-full bg-secondary/20 flex items-center justify-center text-sm font-semibold text-secondary">
                                    {{ substr($contributor->campaign->name ?? 'C', 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-foreground">{{ $contributor->campaign->name ?? 'Unknown' }}</p>
                                    <span class="text-xs">Added {{ $contributor->created_at ? $contributor->created_at->diffForHumans() : 'Unknown' }}</span>
                                </div>
                            </div>
                            @if ($isProjectOwner)
                            <button class="removeContributorBtn text-red-500 hover:text-red-700 transition" data-id="{{ $contributor->id }}">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                            @endif
                        </div>
                        @endforeach
                    @endif
                </div>
            </section>

            {{-- Quick Actions Card --}}
            <section class="bg-white border border-secondary/30 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-foreground mb-4">Actions</h3>

                <div class="flex flex-col gap-2">
                    @if ($isProjectOwner)
                    <button id="editProjectBtn" class="w-full text-left px-3 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
                        <x-heroicon-o-pencil class="w-4 h-4" />
                        Edit Project
                    </button>
                    <button id="deleteProjectBtn" data-project-name="{{ $project->name }}" class="w-full text-left px-3 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition flex items-center gap-2">
                        <x-heroicon-o-trash class="w-4 h-4" />
                        Delete Project
                    </button>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</main>

<!-- Include Add Contributor Modal Component -->
<x-projects.add-contributor-modal :project="$project" :campaigns="$allCampaigns" />

<!-- Include Add Task Modal Component -->
<x-projects.add-task-modal :project="$project" :campaigns="$taskCampaigns" />

<!-- Include Update Task Status Modal -->
<x-projects.update-task-status-modal />

<!-- Include Update Project Status Modal -->
<x-projects.update-project-status-modal />

<!-- Include Edit Project Modal -->
<x-projects.edit-project-modal :project="$project" />

<!-- Include Edit Task Modal -->
<x-projects.edit-task-modal :campaigns="$taskCampaigns" />

<!-- Include Task Remarks Modal -->
<x-projects.task-remarks-modal />

<!-- Include Confirmation Modal (Reusable) -->
<x-confirmation-modal />

<!-- Hidden input for Project ID -->
<input type="hidden" id="projectId" value="{{ $project->id }}" />

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('projectStatsChart');
        
        if (ctx) {
            const completedTasks = parseInt(ctx.dataset.completed) || 0;
            const pendingTasks = parseInt(ctx.dataset.pending) || 0;
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Completed', 'Pending'],
                    datasets: [{
                        data: [completedTasks, pendingTasks],
                        backgroundColor: [
                            'rgb(34, 197, 94)',  // green-500
                            'rgb(234, 179, 8)',   // yellow-500
                        ],
                        borderColor: [
                            'rgb(22, 163, 74)',  // green-600
                            'rgb(202, 138, 4)',   // yellow-600
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = completedTasks + pendingTasks;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

@vite(['resources/js/projects/index.js'])
<!-- Task Tabs Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('[data-campaign-tab]');
        const panels = document.querySelectorAll('[data-campaign-panel]');

        function activateTab(campaignId) {
            tabButtons.forEach(btn => {
                const isActive = btn.getAttribute('data-campaign-tab') === campaignId;
                btn.classList.toggle('bg-primary', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('border-primary', isActive);
                btn.classList.toggle('border-secondary/30', !isActive);
                btn.classList.toggle('text-foreground', !isActive);
            });

            panels.forEach(panel => {
                panel.classList.toggle('hidden', panel.getAttribute('data-campaign-panel') !== campaignId);
            });
        }

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                activateTab(btn.getAttribute('data-campaign-tab'));
            });
        });

        // Activate the "All" tab by default
        if (tabButtons.length) {
            const allTab = Array.from(tabButtons).find(btn => btn.getAttribute('data-campaign-tab') === 'all');
            const fallback = Array.from(tabButtons).find(btn => btn.classList.contains('bg-primary')) || tabButtons[0];
            const initial = allTab || fallback;
            activateTab(initial.getAttribute('data-campaign-tab'));
        }
    });
</script>
@endsection
