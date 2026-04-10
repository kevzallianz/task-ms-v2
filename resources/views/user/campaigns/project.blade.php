@extends('layouts.user-layout')

@section('user-content')
@php
/**
* $campaign: App\Models\Campaign
* $project: App\Models\CampaignProject
* $tasks: Collection of CampaignTask
*/
$userAccessLevel = request()->user()->campaignMember->access_level ?? 'viewer';
@endphp

<main class="grid grid-cols-1 gap-6" data-project-id="{{ $project->id }}">
    <article class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('user.campaign') }}" class="flex items-center justify-center w-10 h-10 rounded-lg border border-secondary/30 hover:bg-gray-100 transition">
                <x-heroicon-o-arrow-left class="w-5 h-5 text-foreground" />
            </a>
        </div>

        <div class="ml-3 text-sm text-gray-500">{{ $tasks->count() }} task(s)</div>
    </article>

    {{-- Row 1: Project Overview | Statistics and Actions --}}
    <div class="grid grid-cols-3 gap-6">
        <section class="col-span-2 bg-white border border-secondary/30 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">Project Overview</h2>
            </div>

            <div class="mb-6">
                <h3 class="text-sm font-medium text-foreground">Project Title</h3>
                <p class="text-lg font-semibold text-primary leading-relaxed">{{ $project->title ?? 'No title provided.' }}</p>
            </div>

            <div class="mb-6">
                <h3 class="text-sm font-medium text-foreground mb-2">Description</h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $project->description ?? 'No description provided.' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-foreground mb-2">Start Date</h3>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                        <span class="text-sm text-gray-600">{{ $project->start_date ? date('M d, Y', timestamp: strtotime($project->start_date)) : 'Not set' }}</span>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-foreground mb-2">Target Date</h3>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-flag class="w-4 h-4 text-gray-400" />
                        <span class="text-sm text-gray-600">{{ $project->target_date ? date('M d, Y', strtotime($project->target_date)) : 'Not set' }}</span>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-foreground mb-2">Status</h3>
                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full
                        {{ $project->status === 'planning' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $project->status === 'ongoing' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $project->status === 'accomplished' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $project->status === 'on_hold' ? 'bg-red-100 text-red-700' : '' }}">
                        <x-heroicon-o-arrow-path class="w-3 h-3" />
                        {{ ucwords(str_replace('_', ' ', $project->status ?? 'unknown')) }}
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-foreground mb-2">Priority Level</h3>
                    @php
                    $priority = $project->priority ?? 'LOW';
                    $priorityClass = match($priority) {
                        'LOW'    => 'bg-gray-100 text-gray-700',
                        'MEDIUM' => 'bg-blue-100 text-blue-700',
                        'HIGH'   => 'bg-orange-100 text-orange-700',
                        'URGENT' => 'bg-red-100 text-red-700',
                        default  => 'bg-gray-100 text-gray-700',
                    };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full {{ $priorityClass }}">
                        <x-heroicon-o-signal class="w-3 h-3" />
                        {{ ucfirst(strtolower($priority)) }}
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-foreground mb-2"></h3>Created</h3>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-clock class="w-4 h-4 text-gray-400" />
                        <span class="text-sm text-gray-600">{{ $project->created_at ? date('M d, Y', strtotime($project->created_at)) : 'Unknown' }}</span>
                    </div>
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="bg-white border border-secondary/30 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-foreground mb-4">Statistics</h3>
                @php
                $totalTasks = $tasks->count();
                $planningTasks = $tasks->where('status', 'planning')->count();
                $forApprovalTasks = $tasks->where('status', 'for_approval')->count();
                $ongoingTasks = $tasks->where('status', 'ongoing')->count();
                $accomplishedTasks = $tasks->where('status', 'accomplished')->count();
                $onHoldTasks = $tasks->where('status', 'on_hold')->count();
                @endphp

                @if ($totalTasks > 0)
                <div class="flex flex-col items-center">
                    <div class="w-full max-w-50 mb-4">
                        <canvas id="taskStatsChart"
                            data-planning="{{ $planningTasks }}"
                            data-for-approval="{{ $forApprovalTasks }}"
                            data-ongoing="{{ $ongoingTasks }}"
                            data-accomplished="{{ $accomplishedTasks }}"
                            data-on-hold="{{ $onHoldTasks }}"></canvas>
                    </div>

                    <div class="w-full space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                <span class="text-xs font-medium text-gray-600">Planning</span>
                            </div>
                            <span class="text-sm font-semibold text-blue-600">{{ $planningTasks }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                                <span class="text-xs font-medium text-gray-600">For Approval</span>
                            </div>
                            <span class="text-sm font-semibold text-purple-600">{{ $forApprovalTasks }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <span class="text-xs font-medium text-gray-600">Ongoing</span>
                            </div>
                            <span class="text-sm font-semibold text-yellow-600">{{ $ongoingTasks }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span class="text-xs font-medium text-gray-600">Accomplished</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600">{{ $accomplishedTasks }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <span class="text-xs font-medium text-gray-600">On Hold</span>
                            </div>
                            <span class="text-sm font-semibold text-red-600">{{ $onHoldTasks }}</span>
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

            <section class="bg-white border border-secondary/30 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-foreground mb-4">Actions</h3>
                <div class="flex flex-col gap-2">
                    @if (in_array($userAccessLevel, ['editor', 'all']))
                    <button id="createTaskBtn" class="w-full text-left px-3 py-2 text-sm font-medium border border-primary/20 text-primary rounded-lg hover:bg-primary/10 transition flex items-center gap-2">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Create Task
                    </button>

                    <button id="updateProjectStatusBtn" class="w-full text-left px-3 py-2 text-sm font-medium rounded-lg hover:bg-secondary/20 border border-secondary/30 text-secondary transition flex items-center gap-2">
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                        Update Project Status
                    </button>

                    <button id="editProjectBtn" class="w-full text-left px-3 py-2 text-sm font-medium text-green-600 border border-green-200 rounded-lg hover:bg-green-50 transition flex items-center gap-2">
                        <x-heroicon-o-pencil class="w-4 h-4" />
                        Edit Project
                    </button>
                    @endif

                    @if(in_array($userAccessLevel, ['all']))
                    <button id="deleteProjectBtn" data-project-name="{{ $project->name }}" class="w-full text-left px-3 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition flex items-center gap-2">
                        <x-heroicon-o-trash class="w-4 h-4" />
                        Delete Project
                    </button>
                    @endif
                </div>
            </section>
        </aside>
    </div>

    {{-- Row 2: Tasks (Full Width) --}}
    <section class="bg-white border border-secondary/30 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                Tasks
            </h2>
            <span class="text-xs text-gray-500" id="taskCount">{{ $tasks->count() }} task(s)</span>
        </div>

        @if ($tasks->count() > 0)
        {{-- Filter Bar --}}
        <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-secondary/20">
            <div class="grid grid-cols-6 gap-3 mb-3">
                {{-- Search --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400" />
                        </div>
                        <input type="text" id="projectTaskSearch" placeholder="Search tasks..." class="w-full pl-10 pr-3 py-2 text-sm border border-secondary/30 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20">
                    </div>
                </div>

                {{-- Filter by Status --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Status</label>
                    <select id="projectStatusFilter" class="w-full px-3 py-2 text-sm border border-secondary/30 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20">
                        <option value="">All Statuses</option>
                        <option value="planning">Planning</option>
                        <option value="for_approval">For Approval</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="on_hold">On Hold</option>
                        <option value="accomplished">Accomplished</option>
                    </select>
                </div>

                {{-- Filter by Start Date --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Start Date</label>
                    <input type="date" id="projectDateFilter" class="w-full px-3 py-2 text-sm border border-secondary/30 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20">
                </div>

                {{-- Filter by Month --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Month</label>
                    <select id="projectMonthFilter" class="w-full px-3 py-2 text-sm border border-secondary/30 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20">
                        <option value="">All Months</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                {{-- Filter by Year --}}
                <div>
                    @php $currentYear = now()->year; @endphp
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Year</label>
                    <select id="projectYearFilter" class="w-full px-3 py-2 text-sm border border-secondary/30 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20">
                        <option value="">All Years</option>
                        @for ($y = $currentYear - 1; $y <= $currentYear + 1; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                    </select>
                </div>

                {{-- Filter by Member --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Member</label>
                    <select id="projectMemberFilter" class="w-full px-3 py-2 text-sm border border-secondary/30 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20">
                        <option value="">All Members</option>
                        @foreach ($campaign->members as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Clear Filters --}}
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600" id="taskFilterCount">
                    Showing {{ $tasks->count() }} of {{ $tasks->count() }} tasks
                </span>
                <button type="button" id="projectClearFilters" class="text-xs text-primary hover:text-primary/80 font-medium">
                    Clear Filters
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-secondary/20">
                    <tr>
                        <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Task</th>
                        <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Start Date</th>
                        <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Target Date</th>
                        <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Priority</th>
                        <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Completed At</th>
                        <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Members</th>
                        <th class="px-4 text-nowrap py-3 text-right font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                    <x-campaigns.task-card :task="$task" :campaign="$campaign" />
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <x-heroicon-o-clipboard-document-list class="w-12 h-12 text-gray-300 mb-2" />
            <p class="text-sm text-gray-500">No tasks yet. Create your first task to get started.</p>
        </div>
        @endif
    </section>

    {{-- Row 3: Activity Timeline (Full Width) --}}
    <section class="bg-white border border-secondary/30 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <x-heroicon-o-clock class="w-5 h-5" />
                Activity Timeline
            </h2>
        </div>

        @if ($project->activities && $project->activities->count() > 0)
        <div class="space-y-0 max-h-100 overflow-y-auto">
            @foreach ($project->activities->sortByDesc('created_at')->take(20) as $activity)
            <x-campaign-project.activity-item :activity="$activity" />
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <x-heroicon-o-clock class="w-12 h-12 text-gray-300 mb-2" />
            <p class="text-sm text-gray-500">No activity yet. Project activities will appear here.</p>
        </div>
        @endif
    </section>
</main>

@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('taskStatsChart');

        if (ctx) {
            const planningTasks = parseInt(ctx.dataset.planning) || 0;
            const forApprovalTasks = parseInt(ctx.dataset.forApproval) || 0;
            const ongoingTasks = parseInt(ctx.dataset.ongoing) || 0;
            const accomplishedTasks = parseInt(ctx.dataset.accomplished) || 0;
            const onHoldTasks = parseInt(ctx.dataset.onHold) || 0;
            const totalTasks = planningTasks + forApprovalTasks + ongoingTasks + accomplishedTasks + onHoldTasks;

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Planning', 'For Approval', 'Ongoing', 'Accomplished', 'On Hold'],
                    datasets: [{
                        data: [planningTasks, forApprovalTasks, ongoingTasks, accomplishedTasks, onHoldTasks],
                        backgroundColor: [
                            'rgb(59, 130, 246)',   // blue-500
                            'rgb(168, 85, 247)',   // purple-500
                            'rgb(234, 179, 8)',    // yellow-500
                            'rgb(34, 197, 94)',    // green-500
                            'rgb(239, 68, 68)',    // red-500
                        ],
                        borderColor: [
                            'rgb(37, 99, 235)',    // blue-600
                            'rgb(147, 51, 234)',   // purple-600
                            'rgb(202, 138, 4)',    // yellow-600
                            'rgb(22, 163, 74)',    // green-600
                            'rgb(220, 38, 38)',    // red-600
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
                                    const percentage = totalTasks > 0 ? Math.round((value / totalTasks) * 100) : 0;
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

@vite(['resources/js/project-page/modals.js', 'resources/js/project-page/search.js'])

<x-project-page.modals.create-task :campaign="$campaign" :project="$project" />
<x-project-page.modals.update-project-status />
<x-project-page.modals.edit-project />
<x-project-page.modals.delete-project />
<x-project-page.modals.edit-task :campaign="$campaign" />
<x-project-page.modals.task-status />
<x-project-page.modals.delete-task />
<x-project-page.modals.task-remarks />