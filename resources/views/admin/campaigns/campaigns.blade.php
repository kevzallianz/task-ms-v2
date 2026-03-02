@extends('layouts.user-layout')

@section('user-content')
    <main class="flex flex-col gap-6">
        {{-- Page Header --}}
        <article class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
                    <x-heroicon-o-rectangle-stack class="w-6 h-6" />
                    Campaigns
                </h1>
                <p class="text-sm text-gray-600">Overview of all campaigns and their active projects</p>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <x-heroicon-o-rectangle-stack class="w-4 h-4 text-secondary" />
                <span class="font-medium">{{ $campaigns->count() }} Campaign{{ $campaigns->count() !== 1 ? 's' : '' }}</span>
            </div>
        </article>

        {{-- Campaign Cards --}}
        <section class="grid grid-cols-2 gap-4">
            @forelse($campaigns as $campaign)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                    {{-- Card Header --}}
                    <div class="bg-primary px-5 py-3.5 flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-rectangle-group class="w-4 h-4 text-accent shrink-0" />
                                <h2 class="text-sm font-bold text-white truncate">{{ $campaign->name }}</h2>
                            </div>
                            <p class="text-xs text-white/60 line-clamp-1 pl-6 mt-0.5">
                                {{ $campaign->description ?? 'No description provided.' }}
                            </p>
                        </div>
                        <div class="shrink-0 flex items-center gap-2">
                            <div class="bg-white/10 border border-white/20 rounded-lg px-3 py-1.5 text-center">
                                <p class="text-base font-bold text-white leading-none">{{ $campaign->members_count }}</p>
                                <p class="text-xs text-white/60 font-medium uppercase tracking-wider">Members</p>
                            </div>
                            <div class="bg-white/10 border border-white/20 rounded-lg px-3 py-1.5 text-center">
                                <p class="text-base font-bold text-accent leading-none">{{ $campaign->projects->count() }}
                                </p>
                                <p class="text-xs text-white/60 font-medium uppercase tracking-wider">Active</p>
                            </div>
                        </div>
                    </div>

                    {{-- Active Projects Section --}}
                    <div class="px-5 py-3">
                        <h3
                            class="text-sm font-semibold text-foreground uppercase tracking-wide mb-2 flex items-center gap-1.5">
                            <x-heroicon-o-folder-open class="w-4.5 h-4.5 text-secondary" />
                            Active Projects
                        </h3>

                        @if ($campaign->projects->isNotEmpty())
                            <div class="rounded-lg border border-gray-200 overflow-hidden">
                                <table class="min-w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th
                                                class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-3 py-2">
                                                Project Title</th>
                                            <th
                                                class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-3 py-2">
                                                Status</th>
                                            <th
                                                class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-3 py-2">
                                                Start Date</th>
                                            <th
                                                class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-3 py-2">
                                                Target Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($campaign->projects as $project)
                                            <tr class="hover:bg-gray-50/70 transition-colors">
                                                <td class="px-3 py-2">
                                                    <p class="font-medium text-foreground text-xs">{{ $project->title }}</p>
                                                    @if ($project->description)
                                                        <p class="text-xs text-gray-400 line-clamp-1">
                                                            {{ $project->description }}</p>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2">
                                                    @php
                                                        $statusMap = [
                                                            'planning' => 'bg-secondary/10 text-secondary',
                                                            'ongoing' => 'bg-green-100 text-green-700',
                                                            'on_hold' => 'bg-accent/10 text-accent',
                                                        ];
                                                        $statusLabel = [
                                                            'planning' => 'Planning',
                                                            'ongoing' => 'Ongoing',
                                                            'on_hold' => 'On Hold',
                                                        ];
                                                        $badgeClass =
                                                            $statusMap[$project->status] ?? 'bg-gray-100 text-gray-600';
                                                        $badgeText =
                                                            $statusLabel[$project->status] ??
                                                            ucfirst(str_replace('_', ' ', $project->status));
                                                    @endphp
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                                        {{ $badgeText }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-gray-500">
                                                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : '—' }}
                                                </td>
                                                <td class="px-3 py-2 text-xs text-gray-500">
                                                    {{ $project->target_date ? \Carbon\Carbon::parse($project->target_date)->format('M d, Y') : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div
                                class="flex items-center gap-3 py-4 px-4 text-black bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                <x-heroicon-o-folder-open class="w-5 h-5 text-gray-300 shrink-0" />
                                <p class="text-sm text-black">No active projects for this campaign.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Card Footer --}}
                    <div class="px-5 py-2.5 border-t border-gray-100 bg-gray-50/80 flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-sm text-black">
                            <x-heroicon-o-clock class="w-3.5 h-3.5" />
                            <span>Created {{ $campaign->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('admin.campaigns.show', $campaign->id) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-all duration-200 shadow-sm">
                            <x-heroicon-o-eye class="w-3.5 h-3.5" />
                            View Campaign
                        </a>
                    </div>

                </div>
            @empty
                <div
                    class="flex flex-col items-center gap-3 py-24 text-gray-400 bg-white rounded-xl border border-dashed border-gray-200">
                    <x-heroicon-o-rectangle-stack class="w-14 h-14 text-gray-200" />
                    <p class="text-base font-semibold text-gray-500">No campaigns available</p>
                    <p class="text-sm text-gray-400">There are currently no campaigns assigned to you.</p>
                </div>
            @endforelse
        </section>

    </main>
@endsection
