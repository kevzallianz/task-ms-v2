@extends('layouts.user-layout')

@section('user-content')
@php
$userAccessLevel = request()->user()->campaignMember?->access_level ?? 'viewer';
$canReopen = in_array($userAccessLevel, ['editor', 'all']);
@endphp
<main class="flex flex-col gap-5">

    {{-- Page Header --}}
    <article class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.campaign') }}"
                class="flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                <x-heroicon-o-arrow-left class="w-4 h-4 text-gray-600" />
            </a>
            <div>
                <h1 class="text-xl font-semibold text-primary flex items-center gap-2">
                    <x-heroicon-o-archive-box class="w-5 h-5" />
                    Accomplished Projects
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">All completed and archived campaign projects</p>
            </div>
        </div>
        @php
        $total = $campaigns->sum(fn($c) => $c->projects->count());
        @endphp
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('campaigns.accomplished') }}" class="flex items-center gap-2">
                <label for="accomplishedDate" class="text-xs font-medium text-gray-500">Accomplished on</label>
                <input type="date" id="accomplishedDate" name="accomplished_date" value="{{ $accomplishedDate }}"
                    class="px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    onchange="this.form.submit()">
                @if ($accomplishedDate)
                <a href="{{ route('campaigns.accomplished') }}" class="text-xs font-medium text-gray-500 hover:text-primary transition">
                    Clear
                </a>
                @endif
            </form>
            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full">
                {{ $total }} project{{ $total !== 1 ? 's' : '' }}
            </span>
        </div>
    </article>

    @if ($campaigns->count() > 0)
        @foreach ($campaigns as $campaign)
        <section class="flex flex-col gap-3">

            {{-- Campaign Label --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-primary uppercase tracking-wide">{{ $campaign->name }}</span>
                <span class="h-px flex-1 bg-gray-200"></span>
                <span class="text-xs text-gray-400">{{ $campaign->projects->count() }} accomplished</span>
            </div>

            {{-- Project Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($campaign->projects as $project)
                @php
                $priority = $project->priority ?? 'LOW';
                $priorityClass = match($priority) {
                    'LOW'    => 'bg-gray-100 text-gray-600',
                    'MEDIUM' => 'bg-blue-50 text-blue-600',
                    'HIGH'   => 'bg-orange-50 text-orange-600',
                    'URGENT' => 'bg-red-50 text-red-600',
                    default  => 'bg-gray-100 text-gray-600',
                };
                $taskCount = $project->tasks_count ?? 0;
                @endphp
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col">

                    {{-- Card top accent --}}
                    <div class="h-1 bg-green-500"></div>

                    <div class="p-4 flex flex-col flex-1">
                        {{-- Title + Priority --}}
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="text-sm font-semibold text-foreground leading-snug">{{ $project->title }}</h3>
                            <span class="inline-flex items-center shrink-0 px-1.5 py-0.5 text-xs font-medium rounded {{ $priorityClass }}">
                                {{ ucfirst(strtolower($priority)) }}
                            </span>
                        </div>

                        {{-- Description --}}
                        <p class="text-xs text-gray-500 line-clamp-2 mb-4 flex-1">
                            {{ $project->description ?? 'No description provided.' }}
                        </p>

                        {{-- Meta --}}
                        <div class="flex flex-col gap-1.5 mb-4">
                            @if ($project->start_date || $project->target_date)
                            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                                <x-heroicon-o-calendar class="w-3.5 h-3.5 shrink-0" />
                                <span>
                                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : '—' }}
                                    →
                                    {{ $project->target_date ? \Carbon\Carbon::parse($project->target_date)->format('M d, Y') : '—' }}
                                </span>
                            </div>
                            @endif
                            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                                <x-heroicon-o-clipboard-document-list class="w-3.5 h-3.5 shrink-0" />
                                <span>{{ $taskCount }} task{{ $taskCount !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>

                        {{-- Accomplished badge --}}
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full" title="Accomplished on {{ \Carbon\Carbon::parse($project->updated_at)->format('M d, Y') }}">
                                <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                                {{ \Carbon\Carbon::parse($project->updated_at)->format('M d, Y') }}
                            </span>
                            <div class="flex items-center gap-2">
                                @if ($canReopen)
                                <button
                                    type="button"
                                    class="project-reopen-btn text-xs font-medium text-gray-400 hover:text-secondary transition"
                                    title="Re-open project"
                                    data-reopen-url="{{ route('campaigns.projects.update-status', ['campaign' => $campaign->id, 'project' => $project->id]) }}"
                                    data-project-title="{{ $project->title }}">
                                    Re-open
                                </button>
                                @endif
                                <a href="{{ route('campaigns.projects.view', ['campaign' => $campaign->id, 'project' => $project->id]) }}"
                                    class="text-xs font-medium text-primary hover:underline">
                                    View →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </section>
        @endforeach
    @else
    <div class="flex flex-col items-center justify-center py-20 text-center bg-white border border-gray-200 rounded-xl">
        <x-heroicon-o-archive-box class="w-14 h-14 text-gray-200 mb-3" />
        @if ($accomplishedDate)
        <h2 class="text-base font-semibold text-gray-600 mb-1">No projects accomplished on this date</h2>
        <p class="text-sm text-gray-400 mb-4">Try a different date or clear the filter.</p>
        <a href="{{ route('campaigns.accomplished') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-primary border border-primary/30 rounded-lg hover:bg-primary/5 transition">
            <x-heroicon-o-x-mark class="w-4 h-4" />
            Clear Filter
        </a>
        @else
        <h2 class="text-base font-semibold text-gray-600 mb-1">No accomplished projects yet</h2>
        <p class="text-sm text-gray-400 mb-4">Projects marked as accomplished will appear here.</p>
        <a href="{{ route('user.campaign') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-primary border border-primary/30 rounded-lg hover:bg-primary/5 transition">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            Back to Campaigns
        </a>
        @endif
    </div>
    @endif

</main>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.project-reopen-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const title = btn.dataset.projectTitle;
                const url = btn.dataset.reopenUrl;
                if (!confirm('Re-open "' + title + '" and set it back to planning?')) return;

                btn.disabled = true;
                btn.textContent = '…';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        status: 'planning',
                        _token: document.querySelector('meta[name="csrf-token"]').content,
                    },
                    success: function (resp) {
                        showToast('success', resp.message || 'Project re-opened');
                        setTimeout(function () { location.reload(); }, 700);
                    },
                    error: function (xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to re-open project';
                        showToast('error', msg);
                        btn.disabled = false;
                        btn.textContent = 'Re-open';
                    },
                });
            });
        });
    });
</script>
