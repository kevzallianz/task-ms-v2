@extends('layouts.user-layout')

@section('user-content')
@php
$currentUser = auth()->user();
@endphp
<main class="flex flex-col gap-5">

    <style>
        .campaign-task-row.selected-for-assign {
            background-color: rgba(37, 99, 235, 0.12);
            transition: background-color 120ms ease-in-out;
        }
        .campaign-task-row.selected-for-assign td {
            background-color: transparent;
        }
    </style>

    {{-- Page Header --}}
    <article class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-primary flex items-center gap-2">
                <x-heroicon-o-rectangle-stack class="w-5 h-5" />
                Campaigns
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your team campaigns, projects, and tasks</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="viewContributionsBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <x-heroicon-o-chart-bar-square class="w-4 h-4" />
                Contributions
            </button>
            <button id="bulkAssignBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <x-heroicon-o-user-group class="w-4 h-4" />
                Assign
            </button>
            <button id="createProjectBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-primary bg-white border border-primary/30 rounded-lg hover:bg-primary/5 transition">
                <x-heroicon-o-folder-plus class="w-4 h-4" />
                New Project
            </button>
            <button id="addTaskBtn" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition">
                <x-heroicon-o-plus class="w-4 h-4" />
                Add Task
            </button>
        </div>
    </article>

    {{-- Campaign Tabs --}}
    @if ($campaigns->count() > 0)
    <div class="flex items-center gap-1 border-b border-gray-200">
        @foreach ($campaigns as $index => $campaign)
        <button
            data-campaign-tab="{{ $campaign->id }}"
            class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors
                {{ $index === 0 ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            {{ $campaign->name }}
        </button>
        @endforeach
        <button
            data-campaign-tab="my-tasks"
            class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
            My Tasks
        </button>
        <button
            data-campaign-tab="summary"
            class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
            Summary
        </button>
    </div>
    @endif

    {{-- Campaign Panels --}}
    @if ($campaigns->count() > 0)
    @foreach ($campaigns as $index => $campaign)
    @php $isActive = $index === 0; @endphp
    <div
        data-campaign-panel="{{ $campaign->id }}"
        data-campaign-projects="{{ json_encode($campaign->projects->map(fn($p) => ['id' => $p->id, 'title' => $p->title])->values()) }}"
        class="{{ !$isActive ? 'hidden' : '' }} flex flex-col gap-5">

        {{-- Top Row: Projects + Members --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Projects --}}
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
                        <x-heroicon-o-folder-open class="w-4 h-4 text-gray-400" />
                        Active Projects
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                            {{ $campaign->projects->count() }}
                        </span>
                    </h3>
                    <a href="{{ route('campaigns.accomplished') }}"
                        class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-primary transition">
                        <x-heroicon-o-archive-box class="w-3.5 h-3.5" />
                        View Accomplished
                    </a>
                </div>

                <div class="p-4">
                    @if($campaign->projects->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach($campaign->projects as $project)
                        @php
                        $projPriority = $project->priority ?? 'LOW';
                        $projPriorityClass = match($projPriority) {
                            'LOW'    => 'bg-gray-100 text-gray-600',
                            'MEDIUM' => 'bg-blue-50 text-blue-600',
                            'HIGH'   => 'bg-orange-50 text-orange-600',
                            'URGENT' => 'bg-red-50 text-red-600',
                            default  => 'bg-gray-100 text-gray-600',
                        };
                        $projStatusClass = match($project->status ?? 'planning') {
                            'planning'    => 'bg-secondary/10 text-secondary',
                            'ongoing'     => 'bg-green-50 text-green-600',
                            'on_hold'     => 'bg-orange-50 text-orange-600',
                            'accomplished'=> 'bg-gray-100 text-gray-600',
                            default       => 'bg-gray-100 text-gray-600',
                        };
                        $projStatusLabel = match($project->status ?? 'planning') {
                            'planning'    => 'Planning',
                            'ongoing'     => 'Ongoing',
                            'on_hold'     => 'On Hold',
                            'accomplished'=> 'Accomplished',
                            default       => ucfirst(str_replace('_', ' ', $project->status ?? '')),
                        };
                        @endphp
                        <div class="group border border-gray-200 rounded-lg p-3 hover:border-primary/30 hover:shadow-sm transition-all">
                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <h4 class="text-sm font-medium text-foreground truncate group-hover:text-primary transition-colors">
                                    {{ $project->title }}
                                </h4>
                                <span class="inline-flex items-center shrink-0 px-1.5 py-0.5 text-xs font-medium rounded {{ $projStatusClass }}">
                                    {{ $projStatusLabel }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3 min-h-8">
                                {{ $project->description ?? 'No description provided.' }}
                            </p>
                            <div class="flex items-center justify-between pt-2.5 border-t border-gray-100">
                                <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium rounded {{ $projPriorityClass }}">
                                    {{ ucfirst(strtolower($projPriority)) }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="project-complete-btn inline-flex items-center gap-1 text-xs font-medium text-gray-400 hover:text-green-600 transition"
                                        title="Mark as Accomplished"
                                        data-campaign-id="{{ $campaign->id }}"
                                        data-project-id="{{ $project->id }}"
                                        data-project-title="{{ $project->title }}"
                                        data-complete-url="{{ route('campaigns.projects.update-status', ['campaign' => $campaign->id, 'project' => $project->id]) }}">
                                        <x-heroicon-o-check-circle class="w-4 h-4" />
                                    </button>
                                    <a href="{{ route('campaigns.projects.view', ['campaign' => $campaign->id, 'project' => $project->id]) }}"
                                        class="text-xs font-medium text-primary hover:underline">
                                        View →
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <x-heroicon-o-folder-plus class="w-10 h-10 text-gray-200 mb-2" />
                        <p class="text-sm text-gray-500">No projects yet.</p>
                        <p class="text-xs text-gray-400 mt-0.5">Create a project to group related tasks.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Members --}}
            @php
            $currentMember = $campaign->members->firstWhere('id', $currentUser->id);
            $currentAccessLevel = $currentMember?->pivot->access_level ?? 'viewer';
            $canManageAccess = $currentAccessLevel === 'all';
            @endphp
            <aside class="lg:col-span-1 bg-white border border-gray-200 rounded-xl overflow-hidden" data-campaign-panel-members="{{ $campaign->id }}">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
                        <x-heroicon-o-users class="w-4 h-4 text-gray-400" />
                        Members
                    </h3>
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                        {{ $campaign->members->count() }}
                    </span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($campaign->members as $member)
                    @php
                    $accessLabel = match($member->pivot->access_level) {
                        'viewer' => 'Viewer',
                        'editor' => 'Editor',
                        'all'    => 'Full',
                        default  => ucfirst($member->pivot->access_level),
                    };
                    $accessBadgeClass = match($member->pivot->access_level) {
                        'viewer' => 'bg-gray-100 text-gray-600',
                        'editor' => 'bg-blue-50 text-blue-600',
                        'all'    => 'bg-indigo-50 text-indigo-600',
                        default  => 'bg-gray-100 text-gray-600',
                    };
                    @endphp
                    <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors"
                        data-campaign-member-id="{{ $member->pivot->id }}"
                        data-user-id="{{ $member->id }}">
                        <x-user-avatar :user="$member" class="w-8 h-8 text-xs shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-foreground truncate">{{ $member->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $member->email }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="member-access-badge inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $accessBadgeClass }}"
                                data-access-level="{{ $member->pivot->access_level }}">
                                {{ $accessLabel }}
                            </span>
                            @if ($canManageAccess && $member->id !== $currentUser->id)
                            <button
                                type="button"
                                class="member-access-open p-1 rounded hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition"
                                aria-label="Update access for {{ $member->name }}"
                                data-update-url="{{ route('campaigns.members.update-access', ['campaign' => $campaign->id, 'campaignMember' => $member->pivot->id]) }}"
                                data-current-level="{{ $member->pivot->access_level }}"
                                data-member-name="{{ $member->name }}"
                                data-member-id="{{ $member->pivot->id }}">
                                <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <x-heroicon-o-users class="w-10 h-10 text-gray-200 mb-2" />
                        <p class="text-sm text-gray-500">No members yet.</p>
                    </div>
                    @endforelse
                </div>
            </aside>
        </div>

        {{-- Tasks --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 gap-4 flex-wrap">
                <h2 class="text-sm font-semibold text-foreground flex items-center gap-2">
                    <x-heroicon-o-clipboard-document-list class="w-4 h-4 text-gray-400" />
                    Tasks
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                        {{ $campaign->allTasks->count() }}
                    </span>
                </h2>

                {{-- Inline Filters --}}
                <form method="GET" action="{{ route('user.campaign') }}#{{ $campaign->id }}" class="flex items-center gap-2">
                    <select
                        name="campaign_{{ $campaign->id }}_status"
                        onchange="this.form.submit()"
                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 text-gray-600 bg-white focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                        <option value="">All Statuses</option>
                        <option value="planning"     {{ ($campaign->filterStatus ?? '') === 'planning'     ? 'selected' : '' }}>Planning</option>
                        <option value="for_approval" {{ ($campaign->filterStatus ?? '') === 'for_approval' ? 'selected' : '' }}>For Approval</option>
                        <option value="ongoing"      {{ ($campaign->filterStatus ?? '') === 'ongoing'      ? 'selected' : '' }}>Ongoing</option>
                        <option value="on_hold"      {{ ($campaign->filterStatus ?? '') === 'on_hold'      ? 'selected' : '' }}>On Hold</option>
                        <option value="accomplished" {{ ($campaign->filterStatus ?? '') === 'accomplished' ? 'selected' : '' }}>Accomplished</option>
                    </select>
                    <select
                        name="campaign_{{ $campaign->id }}_member"
                        onchange="this.form.submit()"
                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 text-gray-600 bg-white focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                        <option value="">All Members</option>
                        @foreach($campaign->members as $member)
                        <option value="{{ $member->pivot->id }}" {{ ($campaign->filterMember ?? '') == $member->pivot->id ? 'selected' : '' }}>
                            {{ $member->name }}
                        </option>
                        @endforeach
                    </select>
                    @if($campaign->filterStatus || $campaign->filterMember)
                    <a href="{{ route('user.campaign') }}"
                        class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition"
                        title="Clear filters">
                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                        Clear
                    </a>
                    @endif
                </form>
            </div>

            @if ($campaign->allTasks->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Task</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Project</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Start</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Target</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Priority</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Completed</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Members</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($campaign->allTasks as $task)
                        <x-campaigns.task-card :isProject="true" :task="$task" :campaign="$campaign" />
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-gray-200 mb-2" />
                <p class="text-sm text-gray-500">No tasks found.</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $campaign->filterStatus || $campaign->filterMember ? 'Try clearing the filters.' : 'Add your first task to get started.' }}
                </p>
            </div>
            @endif
        </div>

    </div>
    @endforeach

    {{-- My Tasks Panel --}}
    <div data-campaign-panel="my-tasks" class="hidden flex-col gap-5">
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 gap-4 flex-wrap">
                <h2 class="text-sm font-semibold text-foreground flex items-center gap-2">
                    <x-heroicon-o-clipboard-document-list class="w-4 h-4 text-gray-400" />
                    My Tasks
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                        {{ $myTasks->count() }}
                    </span>
                </h2>

                {{-- Inline Filters --}}
                <form method="GET" action="{{ route('user.campaign') }}#my-tasks" class="flex items-center gap-2">
                    <input
                        type="text"
                        name="my_tasks_search"
                        value="{{ $myTasksFilterSearch }}"
                        placeholder="Search task or project..."
                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 text-gray-600 bg-white focus:border-primary focus:ring-1 focus:ring-primary/20 transition w-48" />
                    <select
                        name="my_tasks_status"
                        onchange="this.form.submit()"
                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 text-gray-600 bg-white focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                        <option value="">All Statuses</option>
                        <option value="planning"     {{ $myTasksFilterStatus === 'planning'     ? 'selected' : '' }}>Planning</option>
                        <option value="for_approval" {{ $myTasksFilterStatus === 'for_approval' ? 'selected' : '' }}>For Approval</option>
                        <option value="ongoing"      {{ $myTasksFilterStatus === 'ongoing'      ? 'selected' : '' }}>Ongoing</option>
                        <option value="on_hold"      {{ $myTasksFilterStatus === 'on_hold'      ? 'selected' : '' }}>On Hold</option>
                        <option value="accomplished" {{ $myTasksFilterStatus === 'accomplished' ? 'selected' : '' }}>Accomplished</option>
                    </select>
                    <select
                        name="my_tasks_project"
                        onchange="this.form.submit()"
                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 text-gray-600 bg-white focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                        <option value="">All Projects</option>
                        @foreach($myTasksProjects as $myTasksProject)
                        <option value="{{ $myTasksProject->id }}" {{ (string) $myTasksFilterProject === (string) $myTasksProject->id ? 'selected' : '' }}>
                            {{ $myTasksProject->title }}
                        </option>
                        @endforeach
                    </select>
                    <select
                        name="my_tasks_priority"
                        onchange="this.form.submit()"
                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 text-gray-600 bg-white focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                        <option value="">All Priorities</option>
                        <option value="LOW"    {{ $myTasksFilterPriority === 'LOW'    ? 'selected' : '' }}>Low</option>
                        <option value="MEDIUM" {{ $myTasksFilterPriority === 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                        <option value="HIGH"   {{ $myTasksFilterPriority === 'HIGH'   ? 'selected' : '' }}>High</option>
                        <option value="URGENT" {{ $myTasksFilterPriority === 'URGENT' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    <input
                        type="date"
                        name="my_tasks_completed_date"
                        value="{{ $myTasksFilterCompletedDate }}"
                        onchange="this.form.submit()"
                        title="Completed date"
                        class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 text-gray-600 bg-white focus:border-primary focus:ring-1 focus:ring-primary/20 transition" />
                    @if($myTasksFilterStatus || $myTasksFilterProject || $myTasksFilterPriority || $myTasksFilterSearch || $myTasksFilterCompletedDate)
                    <a href="{{ route('user.campaign') }}#my-tasks"
                        class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition"
                        title="Clear filters">
                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                        Clear
                    </a>
                    @endif
                </form>
            </div>

            @if ($myTasks->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Task</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Campaign</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Project</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Start</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Target</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Priority</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($myTasks as $task)
                        @php
                        $myTaskStatusClasses = [
                            'planning' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'for_approval' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'ongoing' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'on_hold' => 'bg-red-50 text-red-700 border-red-200',
                            'accomplished' => 'bg-green-50 text-green-700 border-green-200',
                        ];
                        $myTaskPriority = $task->priority ?? 'LOW';
                        $myTaskPriorityClass = match($myTaskPriority) {
                            'LOW'    => 'bg-gray-100 text-gray-700',
                            'MEDIUM' => 'bg-blue-100 text-blue-700',
                            'HIGH'   => 'bg-orange-100 text-orange-700',
                            'URGENT' => 'bg-red-100 text-red-700',
                            default  => 'bg-gray-100 text-gray-700',
                        };
                        @endphp
                        <tr class="border-b border-secondary/20 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <h4 class="text-sm font-semibold text-foreground">{{ $task->title }}</h4>
                                    @if ($task->description)
                                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-1">{{ $task->description }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-foreground">{{ $task->campaign->name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($task->project)
                                <a href="{{ route('campaigns.projects.view', ['campaign' => $task->campaign_id, 'project' => $task->project->id]) }}"
                                    class="text-sm text-primary hover:text-primary/80 hover:underline font-medium transition">
                                    {{ $task->project->title }}
                                </a>
                                @else
                                <span class="text-xs text-gray-400">No Project</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border',
                                    $myTaskStatusClasses[$task->status] ?? 'bg-gray-50 text-gray-700 border-gray-200',
                                ])>
                                    {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($task->start_date)
                                <span class="text-sm text-nowrap text-foreground">{{ date('M d, Y', strtotime($task->start_date)) }}</span>
                                @else
                                <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($task->target_date)
                                <span class="text-sm text-nowrap text-foreground">{{ date('M d, Y', strtotime($task->target_date)) }}</span>
                                @else
                                <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $myTaskPriorityClass }}">
                                    {{ ucfirst(strtolower($myTaskPriority)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($task->completed_at)
                                <span class="text-sm text-foreground text-nowrap">{{ date('M d, Y', strtotime($task->completed_at)) }}</span>
                                @else
                                <span class="text-xs text-gray-400">Not yet completed</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-gray-200 mb-2" />
                <p class="text-sm text-gray-500">No tasks assigned to you yet.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Summary Panel --}}
    <div data-campaign-panel="summary" class="hidden flex-col gap-5">
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-foreground flex items-center gap-2">
                    <x-heroicon-o-check-badge class="w-4 h-4 text-gray-400" />
                    Accomplished Today
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                        {{ now()->format('M d, Y') }}
                    </span>
                </h2>
                <button type="button" id="reportAccomplishmentsBtn"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition">
                    <x-heroicon-o-paper-airplane class="w-3.5 h-3.5" />
                    Report to Leader
                </button>
            </div>

            <div class="p-4 space-y-5">
                {{-- Accomplished Tasks --}}
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-2">
                        <x-heroicon-o-clipboard-document-check class="w-4 h-4 text-gray-400" />
                        My Tasks
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded-full">
                            {{ $summaryAccomplishedTasks->count() }}
                        </span>
                    </h3>
                    @if ($summaryAccomplishedTasks->count() > 0)
                    <div class="border border-gray-100 rounded-lg divide-y divide-gray-100">
                        @foreach ($summaryAccomplishedTasks as $task)
                        <a href="{{ $task->campaign_project_id ? route('campaigns.projects.view', [$task->campaign_id, $task->campaign_project_id]) : route('user.campaign') . '#' . $task->campaign_id }}"
                            class="flex items-center justify-between gap-4 p-3 hover:bg-gray-50 transition">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-foreground truncate">{{ $task->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                    <span class="font-medium">{{ $task->campaign->name ?? '—' }}</span>
                                    @if ($task->project)
                                        &middot; {{ $task->project->title }}
                                    @endif
                                </p>
                            </div>
                            <span class="text-xs font-medium text-green-600 shrink-0">
                                {{ \Carbon\Carbon::parse($task->completed_at)->format('h:i A') }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-8 text-center border border-gray-100 rounded-lg">
                        <p class="text-sm text-gray-500">No tasks accomplished today.</p>
                    </div>
                    @endif
                </div>

                {{-- Accomplished Projects --}}
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-2">
                        <x-heroicon-o-folder-open class="w-4 h-4 text-gray-400" />
                        Projects
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded-full">
                            {{ $summaryAccomplishedProjects->count() }}
                        </span>
                    </h3>
                    @if ($summaryAccomplishedProjects->count() > 0)
                    <div class="border border-gray-100 rounded-lg divide-y divide-gray-100">
                        @foreach ($summaryAccomplishedProjects as $project)
                        <a href="{{ route('campaigns.projects.view', ['campaign' => $project->campaign_id, 'project' => $project->id]) }}"
                            class="flex items-center justify-between gap-4 p-3 hover:bg-gray-50 transition">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-foreground truncate">{{ $project->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                    <span class="font-medium">{{ $project->campaign->name ?? '—' }}</span>
                                </p>
                            </div>
                            <span class="text-xs font-medium text-green-600 shrink-0">
                                {{ \Carbon\Carbon::parse($project->updated_at)->format('h:i A') }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-8 text-center border border-gray-100 rounded-lg">
                        <p class="text-sm text-gray-500">No projects accomplished today.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Report History --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mt-2">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 gap-4 flex-wrap">
                <h2 class="text-sm font-semibold text-foreground flex items-center gap-2">
                    <x-heroicon-o-paper-airplane class="w-4 h-4 text-gray-400" />
                    Report History
                </h2>
                <form method="GET" action="{{ route('user.campaign') }}#summary" class="flex items-center gap-2">
                    <label for="reportLogsDate" class="text-xs font-medium text-gray-500">Date</label>
                    <input type="date" id="reportLogsDate" name="report_logs_date" value="{{ $reportLogsDate }}"
                        class="px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                        onchange="this.form.submit()">
                    @if ($reportLogsDate)
                    <a href="{{ route('user.campaign') }}#summary" class="text-xs font-medium text-gray-500 hover:text-primary transition">
                        Clear
                    </a>
                    @endif
                </form>
            </div>
            <div class="p-4">
                @if ($reportLogs->count() > 0)
                <div class="border border-gray-100 rounded-lg divide-y divide-gray-100">
                    @foreach ($reportLogs as $log)
                    <div class="flex items-center justify-between gap-4 p-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-foreground truncate">
                                Sent to {{ $log->leader_email }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                                @if ($log->status === 'failed' && $log->error_message)
                                    &middot; {{ \Illuminate\Support\Str::limit($log->error_message, 80) }}
                                @endif
                            </p>
                        </div>
                        @if ($log->status === 'sent')
                        <span class="inline-flex items-center gap-1 shrink-0 px-2 py-0.5 text-xs font-medium rounded-full bg-green-50 text-green-600">
                            <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                            Sent
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 shrink-0 px-2 py-0.5 text-xs font-medium rounded-full bg-red-50 text-red-600">
                            <x-heroicon-o-x-circle class="w-3.5 h-3.5" />
                            Failed
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-8 text-center border border-gray-100 rounded-lg">
                    <p class="text-sm text-gray-500">No reports sent yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @else
    <div class="flex flex-col items-center justify-center py-16 text-center bg-white border border-gray-200 rounded-xl">
        <x-heroicon-o-rectangle-stack class="w-14 h-14 text-gray-200 mb-3" />
        <h2 class="text-base font-semibold text-gray-600 mb-1">No campaigns yet</h2>
        <p class="text-sm text-gray-400">Join or create a campaign to get started.</p>
    </div>
    @endif

    <x-campaigns.add-task-modal :currentUser="auth()->user()" />
    <x-campaigns.edit-task-modal />
    <x-campaigns.task-status-modal />
    <x-campaigns.delete-task-modal />
    <x-campaigns.member-access-modal />
    <x-campaigns.task-remarks-modal />
    <x-campaigns.member-contributions-modal />

    {{-- Report Accomplishments Modal --}}
    <div id="reportAccomplishmentsModal" class="fixed inset-0 bg-black/40 items-center justify-center z-50 overflow-y-auto hidden">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-foreground flex items-center gap-2">
                    <x-heroicon-o-paper-airplane class="w-4 h-4 text-gray-400" />
                    Report Accomplishments to Leader
                </h3>
                <button type="button" id="reportAccomplishmentsModalClose" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition" aria-label="Close">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
            <form id="reportAccomplishmentsForm">
                @csrf
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Your Name</label>
                            <input type="text" value="{{ $currentUser->name }}" disabled
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Your Email</label>
                            <input type="text" value="{{ $currentUser->email }}" disabled
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                        </div>
                    </div>
                    <div>
                        <label for="leaderEmail" class="block text-xs font-medium text-gray-500 mb-1">
                            Leader's Email
                        </label>
                        <input type="email" id="leaderEmail" name="leader_email" required
                            placeholder="leader@example.com"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label for="accomplishmentsText" class="block text-xs font-medium text-gray-500 mb-1">
                            What did you accomplish today?
                        </label>
                        <textarea id="accomplishmentsText" name="accomplishments" rows="6" required
                            placeholder="Summarize what you accomplished today..."
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none">{{ "Tasks accomplished today:\n" . ($summaryAccomplishedTasks->count() > 0 ? $summaryAccomplishedTasks->map(fn($t) => '- ' . $t->title)->implode("\n") : '- None') . "\n\nProjects accomplished today:\n" . ($summaryAccomplishedProjects->count() > 0 ? $summaryAccomplishedProjects->map(fn($p) => '- ' . $p->title)->implode("\n") : '- None') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">This will be emailed to your leader.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-gray-100">
                    <button type="button" id="reportAccomplishmentsCancel" class="px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" id="reportAccomplishmentsSubmit" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition">
                        <x-heroicon-o-paper-airplane class="w-4 h-4" />
                        Send Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Create Campaign Project Modal --}}
    <div id="createProjectModal" class="fixed inset-0 bg-black/40 items-center justify-center z-50 overflow-y-auto hidden">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Create Project</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Group related tasks within this campaign.</p>
                </div>
                <button type="button" id="createProjectModalClose" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition" aria-label="Close">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form id="create-project-form" class="p-6 space-y-4" action="{{ route('campaigns.projects.store', ['campaign' => $campaigns->first()->id ?? 0]) }}" method="POST">
                @csrf
                <input type="hidden" name="campaign_id" value="{{ $campaigns->first()?->id ?? '' }}">

                <div>
                    <label for="projectTitle" class="text-xs font-medium text-gray-700 mb-1 block">Title <span class="text-red-500">*</span></label>
                    <input id="projectTitle" type="text" name="title" required maxlength="150" placeholder="Project title"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 transition" />
                    <span class="text-xs text-red-500 hidden" id="projectTitleError"></span>
                </div>

                <div>
                    <label for="projectDescription" class="text-xs font-medium text-gray-700 mb-1 block">Description</label>
                    <textarea id="projectDescription" name="description" rows="3" placeholder="Optional description"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none transition"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="projectStatus" class="text-xs font-medium text-gray-700 mb-1 block">Status</label>
                        <select id="projectStatus" name="status"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                            <option value="planning" selected>Planning</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="on_hold">On Hold</option>
                            <option value="accomplished">Accomplished</option>
                        </select>
                    </div>
                    <div>
                        <label for="projectPriority" class="text-xs font-medium text-gray-700 mb-1 block">Priority</label>
                        <select id="projectPriority" name="priority"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                            <option value="LOW" selected>Low</option>
                            <option value="MEDIUM">Medium</option>
                            <option value="HIGH">High</option>
                            <option value="URGENT">Urgent</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="projectStartDate" class="text-xs font-medium text-gray-700 mb-1 block">Start Date</label>
                        <input id="projectStartDate" type="date" name="start_date"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 transition" />
                    </div>
                    <div>
                        <label for="projectTargetDate" class="text-xs font-medium text-gray-700 mb-1 block">Target Date</label>
                        <input id="projectTargetDate" type="date" name="target_date"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 transition" />
                    </div>
                </div>
            </form>

            <div class="flex items-center justify-end gap-2 px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-100">
                <button type="button" id="createProjectCancel"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit" form="create-project-form" id="createProjectSubmit"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition">
                    Create Project
                </button>
            </div>
        </div>
    </div>

    {{-- Import Tasks Modal --}}
    <div id="importModal" class="fixed inset-0 z-50 items-center justify-center p-4" style="display:none" aria-hidden="true">
        <div id="importModalBackdrop" class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 w-full max-w-3xl bg-white rounded-xl shadow-xl overflow-hidden" role="dialog" aria-modal="true" aria-labelledby="importModalTitle">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 id="importModalTitle" class="text-base font-semibold">Import Tasks</h3>
                <button id="importModalClose" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition" aria-label="Close import modal">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-500">Upload a CSV or XLSX file with columns: <strong class="text-gray-700">assigned_members, title, description, start_date, target_date, status, completed_at</strong>.</p>
                <div class="flex items-center gap-3">
                    <input id="importFileInput" type="file" accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="w-full text-sm" />
                    <button id="importPreviewBtn" class="px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition whitespace-nowrap">Preview</button>
                </div>
                <div id="importPreviewContainer" class="hidden">
                    <h4 class="text-sm font-medium mb-2">Task Preview</h4>
                    <div class="overflow-auto max-h-64 border border-gray-200 rounded-lg">
                        <table id="importPreviewTable" class="w-full text-sm"></table>
                    </div>
                    <div class="flex items-center justify-end gap-2 mt-3">
                        <button id="importCancelBtn" class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button id="importConfirmBtn" class="px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition">Import</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="importLoadingOverlay" class="absolute inset-0 z-20 items-center justify-center bg-white/70" style="display:none">
            <div class="flex items-center gap-3">
                <svg class="animate-spin w-5 h-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span class="text-sm text-gray-700">Processing…</span>
            </div>
        </div>
    </div>

</main>
@endsection

<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
@vite(['resources/js/campaigns/index.js'])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.project-complete-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const title = btn.dataset.projectTitle;
                const url = btn.dataset.completeUrl;
                if (!confirm('Mark "' + title + '" as accomplished?')) return;

                btn.disabled = true;
                const icon = btn.querySelector('svg');
                if (icon) icon.classList.add('animate-spin');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        status: 'accomplished',
                        _token: document.querySelector('meta[name="csrf-token"]').content,
                    },
                    success: function (resp) {
                        showToast('success', resp.message || 'Project marked as accomplished');
                        setTimeout(function () { location.reload(); }, 700);
                    },
                    error: function (xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to update project';
                        showToast('error', msg);
                        btn.disabled = false;
                        if (icon) icon.classList.remove('animate-spin');
                    },
                });
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('[data-campaign-tab]');
        const taskPanels = document.querySelectorAll('[data-campaign-panel]');
        const memberPanels = document.querySelectorAll('[data-campaign-panel-members]');

        function activate(campaignId) {
            tabs.forEach(btn => {
                const isActive = btn.getAttribute('data-campaign-tab') === campaignId;
                btn.classList.toggle('border-primary', isActive);
                btn.classList.toggle('text-primary', isActive);
                btn.classList.toggle('border-transparent', !isActive);
                btn.classList.toggle('text-gray-500', !isActive);
            });
            taskPanels.forEach(panel => {
                panel.classList.toggle('hidden', panel.getAttribute('data-campaign-panel') !== campaignId);
            });
            memberPanels.forEach(panel => {
                panel.classList.toggle('hidden', panel.getAttribute('data-campaign-panel-members') !== campaignId);
            });
        }

        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                const campaignId = btn.getAttribute('data-campaign-tab');
                activate(campaignId);
                history.replaceState(null, '', '#' + campaignId);
            });
        });

        if (tabs.length) {
            const hashId = window.location.hash.replace('#', '');
            const hasMatchingTab = Array.from(tabs).some(btn => btn.getAttribute('data-campaign-tab') === hashId);
            activate(hasMatchingTab ? hashId : tabs[0].getAttribute('data-campaign-tab'));
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('createProjectBtn');
        const modal = document.getElementById('createProjectModal');
        const closeBtn = document.getElementById('createProjectModalClose');
        const cancelBtn = document.getElementById('createProjectCancel');
        const form = document.getElementById('create-project-form');

        if (!openBtn || !modal) return;

        function open() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.querySelector('input,textarea,button')?.focus();
        }
        function close() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        openBtn.addEventListener('click', open);
        if (closeBtn) closeBtn.addEventListener('click', close);
        if (cancelBtn) cancelBtn.addEventListener('click', close);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = form.getAttribute('action');
            const data = $(form).serialize();
            const submitBtn = document.getElementById('createProjectSubmit');
            const originalText = submitBtn?.textContent ?? 'Create';
            if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Creating…'; }

            $.ajax({
                url, method: 'POST', data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success(resp) {
                    showToast('success', resp.message || 'Project created');
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; }
                    close();
                    setTimeout(() => location.reload(), 700);
                },
                error(xhr) {
                    const msg = (xhr.responseJSON && (xhr.responseJSON.message || Object.values(xhr.responseJSON.errors ?? {}).flat().join(' '))) || 'Failed to create project';
                    showToast('error', msg);
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; }
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewContributionsBtn = document.getElementById('viewContributionsBtn');
        if (viewContributionsBtn) {
            viewContributionsBtn.addEventListener('click', function() {
                const activeCampaignPanel = document.querySelector('[data-campaign-panel]:not(.hidden)');
                if (activeCampaignPanel) {
                    const campaignId = activeCampaignPanel.getAttribute('data-campaign-panel');
                    if (window.openMemberContributions) window.openMemberContributions(campaignId);
                } else {
                    showToast('info', 'Please select a campaign first');
                }
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('reportAccomplishmentsBtn');
        const modal = document.getElementById('reportAccomplishmentsModal');
        const closeBtn = document.getElementById('reportAccomplishmentsModalClose');
        const cancelBtn = document.getElementById('reportAccomplishmentsCancel');
        const form = document.getElementById('reportAccomplishmentsForm');

        if (!openBtn || !modal) return;

        function open() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.querySelector('textarea')?.focus();
        }
        function close() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        openBtn.addEventListener('click', open);
        if (closeBtn) closeBtn.addEventListener('click', close);
        if (cancelBtn) cancelBtn.addEventListener('click', close);
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) close(); });

        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const data = $(form).serialize();
            const submitBtn = document.getElementById('reportAccomplishmentsSubmit');
            const originalHtml = submitBtn?.innerHTML;
            if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Sending…'; }

            $.ajax({
                url: '{{ route('campaigns.report-accomplishments') }}',
                method: 'POST',
                data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success(resp) {
                    showToast('success', resp.message || 'Report sent to your leader');
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHtml; }
                    close();
                    setTimeout(() => location.hash = 'summary', 0);
                    setTimeout(() => location.reload(), 700);
                },
                error(xhr) {
                    const msg = (xhr.responseJSON && (xhr.responseJSON.message || Object.values(xhr.responseJSON.errors ?? {}).flat().join(' '))) || 'Failed to send report';
                    showToast('error', msg);
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHtml; }
                    setTimeout(() => location.hash = 'summary', 0);
                    setTimeout(() => location.reload(), 700);
                }
            });
        });
    });
</script>
