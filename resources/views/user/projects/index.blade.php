@extends('layouts.user-layout')

@section('user-content')
<main class="flex flex-col gap-6">

    {{-- Page Header --}}
    <article class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
                <x-heroicon-o-folder class="w-6 h-6" />
                Projects
            </h1>
            <p class="text-sm text-gray-600">View and manage projects under your campaigns.</p>
        </div>
        {{-- Primary Action --}}
        <button id="openCreateProjectModal" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
            <x-heroicon-o-plus class="w-4 h-4" />
            New Project
        </button>
    </article>

    {{-- Filters --}}
    <aside class="flex flex-col sm:flex-row sm:items-center gap-3 w-full">
        <form method="GET" action="{{ route('user.projects') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 w-full">
            <div class="relative w-full">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" />
                <input type="text" name="search" value="{{ $filterSearch }}" placeholder="Search projects by name or description..."
                    class="pl-9 pr-3 py-2 text-sm bg-white border border-secondary/30 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
            </div>

            <div class="relative w-full sm:w-56">
                <x-heroicon-o-funnel class="w-4 h-4 absolute left-3 top-2.5 text-gray-400 pointer-events-none" />
                <select name="status" onchange="this.form.submit()" class="pl-9 pr-3 py-2 text-sm bg-white border border-secondary/30 rounded-lg w-full appearance-none">
                    <option value="">Status: All</option>
                    <option value="planning" @selected($filterStatus === 'planning')>Planning</option>
                    <option value="in_progress" @selected($filterStatus === 'in_progress')>In Progress</option>
                    <option value="on_hold" @selected($filterStatus === 'on_hold')>On Hold</option>
                    <option value="completed" @selected($filterStatus === 'completed')>Completed</option>
                    <option value="cancelled" @selected($filterStatus === 'cancelled')>Cancelled</option>
                </select>
            </div>

            <button type="submit" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-secondary/30 rounded-lg hover:bg-gray-50 transition shrink-0">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                Search
            </button>

            @if ($filterSearch || $filterStatus)
            <a href="{{ route('user.projects') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-primary transition shrink-0">
                <x-heroicon-o-x-mark class="w-4 h-4" />
                Clear
            </a>
            @endif
        </form>
    </aside>

    {{-- Results count --}}
    @if ($filterSearch || $filterStatus)
    <p class="text-sm text-gray-500 -mt-2">
        Showing {{ $projects->count() }} {{ \Illuminate\Support\Str::plural('result', $projects->count()) }}
        @if ($filterSearch) for "<span class="font-medium text-foreground">{{ $filterSearch }}</span>" @endif
    </p>
    @endif

    {{-- Project List --}}
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($projects as $project)
        <a href="{{ route('projects.view', $project->id) }}" class="w-full">
            <x-projects.project-card
                name="{{ $project->name }}"
                description="{{ $project->description }}"
                status="{{ $project->status }}"
                :statusColor="match($project->status) {
                    'in_progress' => 'green',
                    'on_hold' => 'yellow',
                    'completed' => 'blue',
                    'planning' => 'orange',
                    'cancelled' => 'red',
                    default => 'gray',
                }"
                startDate="{{ $project->start_date ? date('M d, Y', strtotime($project->start_date)) : '—' }}"
                targetDate="{{ $project->target_date ? date('M d, Y', strtotime($project->target_date)) : '—' }}"
                :targetDateRaw="$project->target_date"
                :taskCount="$project->tasks_count"
                :completedTaskCount="$project->completed_tasks_count"
                ownerName="{{ $project->user->name ?? 'Unknown' }}"
                ownerCampaign="{{ $project->campaign->name ?? 'No Campaign' }}" />
        </a>
        @endforeach

        @if ($projects->isEmpty())
        <div class="p-12 border-2 border-dashed border-secondary/30 rounded-xl w-full col-span-1 md:col-span-2 xl:col-span-3 flex flex-col items-center">
            <x-heroicon-o-folder-open class="w-12 h-12 mx-auto text-gray-400" />
            @if ($filterSearch || $filterStatus)
            <p class="text-center text-gray-500 mt-2">No projects match your filters.</p>
            <a href="{{ route('user.projects') }}" class="text-sm font-medium text-primary hover:underline mt-1">Clear filters</a>
            @else
            <p class="text-center text-gray-500 mt-2">No projects found. Create a new project to get started!</p>
            <button id="openCreateProjectModalEmpty" class="mt-3 inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition">
                <x-heroicon-o-plus class="w-4 h-4" />
                New Project
            </button>
            @endif
        </div>
        @endif
    </section>
</main>

<!-- Include Create Project Modal -->
<x-projects.create-project :campaigns="$campaigns" />

<!-- Include Create Project Script -->
@vite(['resources/js/projects/index.js'])

@endsection