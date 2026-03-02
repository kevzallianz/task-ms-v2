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
    <aside class="flex items-center gap-3 w-full">
        <div class="relative w-full">
            <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" />
            <input type="text" placeholder="Search projects..." class="pl-9 pr-3 py-2 text-sm bg-white border border-secondary/30 rounded-lg w-full" />
        </div>

        <div class="relative">
            <x-heroicon-o-funnel class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" />
            <select class="pl-9 pr-3 py-2 text-sm bg-white border border-secondary/30 rounded-lg">
                <option>Status: All</option>
            </select>
        </div>
    </aside>

    {{-- Project List --}}
    <section class="grid grid-cols-2 gap-4">
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
                startDate="{{ date('M d, Y', strtotime($project->start_date)) }}"
                targetDate="{{ date('M d, Y', strtotime($project->target_date)) }}"
                ownerName="{{ $project->user->name ?? 'Unknown' }}"
                ownerCampaign="{{ $project->campaign->name ?? 'No Campaign' }}" />
        </a>
        @endforeach

        @if ($projects->isEmpty())
        <div class="p-12 border-2 border-dashed border-secondary/30 rounded-xl w-full col-span-2">
            <x-heroicon-o-folder-open class="w-12 h-12 mx-auto text-gray-400" />
            <p class="text-center text-gray-500">No projects found. Create a new project to get started!</p>
        </div>
        @endif
    </section>
</main>

<!-- Include Create Project Modal -->
<x-projects.create-project :campaigns="$campaigns" />

<!-- Include Create Project Script -->
@vite(['resources/js/projects/index.js'])

@endsection