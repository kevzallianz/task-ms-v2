@extends('layouts.user-layout')

@section('user-content')
    <main class="flex flex-col gap-6">

        {{-- Page Header --}}
        <article class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <a href="{{ route('admin.campaigns.show', $campaign) }}"
                    class="mt-1 p-1.5 rounded-lg border border-gray-200 text-gray-400 hover:text-primary hover:border-primary/40 transition">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">
                        <a href="{{ route('admin.campaigns') }}" class="hover:text-primary transition">Campaigns</a>
                        <span class="mx-1">/</span>
                        <a href="{{ route('admin.campaigns.show', $campaign) }}"
                            class="hover:text-primary transition">{{ $campaign->name }}</a>
                    </p>
                    <h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
                        <x-heroicon-o-folder-open class="w-6 h-6" />
                        {{ $project->title }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $project->description ?? 'No description provided.' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0 mt-1">
                @php
                    $headerStatusMap = [
                        'planning' => ['bg-secondary/10 text-secondary border-secondary/20', 'Planning'],
                        'ongoing' => ['bg-green-50 text-green-700 border-green-200', 'Ongoing'],
                        'on_hold' => ['bg-accent/10 text-accent border-accent/20', 'On Hold'],
                        'accomplished' => ['bg-gray-100 text-gray-500 border-gray-200', 'Accomplished'],
                        'cancelled' => ['bg-red-50 text-red-500 border-red-200', 'Cancelled'],
                    ];
                    [$headerBadge, $headerLabel] = $headerStatusMap[$project->status] ?? [
                        'bg-gray-100 text-gray-500 border-gray-200',
                        ucfirst(str_replace('_', ' ', $project->status)),
                    ];
                @endphp
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border {{ $headerBadge }}">
                    <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                    {{ $headerLabel }}
                </span>
                <div
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-accent/5 border border-accent/20 rounded-lg text-xs font-medium text-accent">
                    <x-heroicon-o-clipboard-document-list class="w-3.5 h-3.5" />
                    {{ $tasks->count() }} Tasks
                </div>
            </div>
        </article>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: Project Details + Tasks --}}
            <div class="lg:col-span-2 flex flex-col gap-6">

                {{-- Project Info --}}
                <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-foreground flex items-center gap-2">
                            <x-heroicon-o-information-circle class="w-4 h-4 text-secondary" />
                            Project Details
                        </h2>
                    </div>
                    <div class="px-5 py-4 grid grid-cols-2 gap-x-8 gap-y-4">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Start Date</p>
                            <p class="text-sm text-foreground flex items-center gap-1.5">
                                <x-heroicon-o-calendar class="w-4 h-4 text-gray-400 shrink-0" />
                                {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : 'Not set' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Target Date</p>
                            <p class="text-sm text-foreground flex items-center gap-1.5">
                                <x-heroicon-o-flag class="w-4 h-4 text-gray-400 shrink-0" />
                                {{ $project->target_date ? \Carbon\Carbon::parse($project->target_date)->format('M d, Y') : 'Not set' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Campaign</p>
                            <p class="text-sm text-foreground flex items-center gap-1.5">
                                <x-heroicon-o-rectangle-stack class="w-4 h-4 text-gray-400 shrink-0" />
                                {{ $campaign->name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Created</p>
                            <p class="text-sm text-foreground flex items-center gap-1.5">
                                <x-heroicon-o-clock class="w-4 h-4 text-gray-400 shrink-0" />
                                {{ $project->created_at->format('M d, Y') }} ({{ $project->created_at->diffForHumans() }})
                            </p>
                        </div>
                    </div>
                </section>


            </div>

            {{-- RIGHT: Activity Log --}}
            <div class="lg:col-span-1">
                <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-foreground flex items-center gap-2">
                            <x-heroicon-o-clock class="w-4 h-4 text-primary" />
                            Activity Log
                        </h2>
                        <span class="text-xs text-gray-400 font-medium">{{ $project->activities->count() }} entries</span>
                    </div>

                    @if ($project->activities->isNotEmpty())
                        <ul class="divide-y divide-gray-100 max-h-130 overflow-y-auto">
                            @foreach ($project->activities as $activity)
                                <li class="px-5 py-3 hover:bg-gray-50/60 transition-colors">
                                    <div class="flex items-start gap-2.5">
                                        <div
                                            class="w-6 h-6 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center shrink-0 mt-0.5">
                                            <span class="text-[9px] font-bold text-primary uppercase leading-none">
                                                {{ substr($activity->user->name ?? '?', 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-medium text-foreground">
                                                {{ $activity->user->name ?? 'System' }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $activity->description }}</p>
                                            <p class="text-[10px] text-gray-400 mt-1">
                                                {{ $activity->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex items-center gap-3 px-5 py-6 text-gray-400">
                            <x-heroicon-o-clock class="w-5 h-5 text-gray-300 shrink-0" />
                            <p class="text-sm">No activity recorded yet.</p>
                        </div>
                    @endif
                </section>
            </div>

            {{-- Tasks --}}
            <section class="bg-white col-span-3 rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-foreground flex items-center gap-2">
                        <x-heroicon-o-clipboard-document-list class="w-4 h-4 text-accent" />
                        Tasks
                    </h2>
                    <span class="text-xs text-gray-400 font-medium">{{ $tasks->count() }} total</span>
                </div>

                @if ($tasks->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th
                                        class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-5 py-2.5">
                                        Task</th>
                                    <th
                                        class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-4 py-2.5">
                                        Assigned To</th>
                                    <th
                                        class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-4 py-2.5">
                                        Status</th>
                                    <th
                                        class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-4 py-2.5">
                                        Start</th>
                                    <th
                                        class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-4 py-2.5">
                                        Target</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($tasks as $task)
                                    @php
                                        $taskStatusMap = [
                                            'planning' => ['bg-secondary/10 text-secondary', 'Planning'],
                                            'ongoing' => ['bg-green-100 text-green-700', 'Ongoing'],
                                            'on_hold' => ['bg-accent/10 text-accent', 'On Hold'],
                                            'accomplished' => ['bg-gray-100 text-gray-500', 'Done'],
                                            'cancelled' => ['bg-red-50 text-red-500', 'Cancelled'],
                                        ];
                                        [$taskBadge, $taskLabel] = $taskStatusMap[$task->status] ?? [
                                            'bg-gray-100 text-gray-500',
                                            ucfirst(str_replace('_', ' ', $task->status)),
                                        ];
                                    @endphp
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="px-5 py-2.5">
                                            <p class="text-xs font-medium text-foreground">{{ $task->title }}</p>
                                            @if ($task->description)
                                                <p class="text-xs text-gray-400 line-clamp-1 mt-0.5">
                                                    {{ $task->description }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5">
                                            @if ($task->assignedMembers->isNotEmpty())
                                                <div class="flex items-center gap-1 flex-wrap">
                                                    @foreach ($task->assignedMembers as $assigned)
                                                        <div
                                                            class="flex items-center gap-1 bg-primary/5 border border-primary/15 rounded-full pl-1 pr-2 py-0.5">
                                                            <div
                                                                class="w-4 h-4 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                                                                <span
                                                                    class="text-[9px] font-bold text-primary uppercase leading-none">
                                                                    {{ substr($assigned->user->name ?? '?', 0, 1) }}
                                                                </span>
                                                            </div>
                                                            <span
                                                                class="text-xs text-primary font-medium whitespace-nowrap">{{ $assigned->user->name ?? '—' }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-300">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $taskBadge }}">
                                                {{ $taskLabel }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5 text-xs text-gray-500">
                                            {{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('M d, Y') : '—' }}
                                        </td>
                                        <td class="px-4 py-2.5 text-xs text-gray-500">
                                            {{ $task->target_date ? \Carbon\Carbon::parse($task->target_date)->format('M d, Y') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex items-center gap-3 px-5 py-6 text-gray-400">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-300 shrink-0" />
                        <p class="text-sm">No tasks for this project.</p>
                    </div>
                @endif
            </section>

        </div>

    </main>
@endsection
