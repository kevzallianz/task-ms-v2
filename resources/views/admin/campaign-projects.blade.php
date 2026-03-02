@extends('layouts.user-layout')

@section('user-content')
<main class="flex flex-col gap-6">

    {{-- Page Header --}}
    <article class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
                <x-heroicon-o-folder-open class="w-6 h-6" />
                Projects
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">All projects across your campaigns</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <x-heroicon-o-folder-open class="w-4 h-4 text-secondary" />
            <span class="font-medium">
                {{ $campaigns->sum(fn($c) => $c->projects->count()) }} Project(s)
            </span>
        </div>
    </article>

    {{-- Filter Bar --}}
    <div class="px-4 py-3 bg-white border border-gray-200 rounded-xl flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1 min-w-40">
            <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Campaign</label>
            <select id="filter-campaign"
                class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-white text-foreground focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                <option value="">All Campaigns</option>
                @foreach ($campaigns as $campaign)
                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-36">
            <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Status</label>
            <select id="filter-status"
                class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-white text-foreground focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                <option value="">All Statuses</option>
                <option value="planning">Planning</option>
                <option value="ongoing">Ongoing</option>
                <option value="on_hold">On Hold</option>
                <option value="accomplished">Accomplished</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <button id="filter-reset"
            class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 bg-white rounded-lg hover:bg-gray-50 transition flex items-center gap-1.5">
            <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
            Reset
        </button>
        <span id="filter-count" class="ml-auto text-xs text-gray-400 font-medium self-end pb-0.5 hidden">
            <span id="filter-count-num">0</span> result(s)
        </span>
    </div>

    {{-- Projects Table --}}
    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @php $allProjects = $campaigns->flatMap(fn($c) => $c->projects->map(fn($p) => ['campaign' => $c, 'project' => $p])); @endphp

        @if ($allProjects->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-5 py-2.5">Project</th>
                        <th class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-4 py-2.5">Campaign</th>
                        <th class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-4 py-2.5">Status</th>
                        <th class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-4 py-2.5">Start Date</th>
                        <th class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-4 py-2.5">Target Date</th>
                        <th class="text-left text-xs font-medium text-foreground/50 uppercase tracking-wider px-4 py-2.5">Created</th>
                        <th class="px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody id="projects-tbody" class="divide-y divide-gray-100">
                    @foreach ($allProjects as $row)
                    @php
                        $project  = $row['project'];
                        $campaign = $row['campaign'];
                        $statusMap = [
                            'planning'     => ['bg-secondary/10 text-secondary', 'Planning'],
                            'ongoing'      => ['bg-green-100 text-green-700', 'Ongoing'],
                            'on_hold'      => ['bg-accent/10 text-accent', 'On Hold'],
                            'accomplished' => ['bg-gray-100 text-gray-500', 'Accomplished'],
                            'cancelled'    => ['bg-red-50 text-red-500', 'Cancelled'],
                        ];
                        [$badgeClass, $badgeText] = $statusMap[$project->status] ?? ['bg-gray-100 text-gray-500', ucfirst(str_replace('_', ' ', $project->status))];
                    @endphp
                    <tr class="project-row hover:bg-gray-50/60 transition-colors"
                        data-campaign="{{ $campaign->id }}"
                        data-status="{{ $project->status }}">
                        <td class="px-5 py-2.5">
                            <p class="text-xs font-medium text-foreground">{{ $project->title }}</p>
                            @if ($project->description)
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $project->description }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-2.5">
                            <a href="{{ route('admin.campaigns.show', $campaign) }}"
                               class="text-xs font-medium text-primary hover:underline">{{ $campaign->name }}</a>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $badgeText }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-xs text-gray-500">
                            {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-xs text-gray-500">
                            {{ $project->target_date ? \Carbon\Carbon::parse($project->target_date)->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-xs text-gray-400">
                            {{ $project->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-2.5">
                            <a href="{{ route('admin.campaigns.projects.show', [$campaign, $project]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-primary border border-primary/30 bg-primary/5 rounded-lg hover:bg-primary/10 transition whitespace-nowrap">
                                <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="projects-empty" class="hidden px-5 py-6 flex items-center gap-3 text-gray-400">
                <x-heroicon-o-funnel class="w-5 h-5 text-gray-300 shrink-0" />
                <p class="text-sm">No projects match the selected filters.</p>
            </div>
        </div>
        @else
        <div class="flex flex-col items-center gap-2 py-16 text-gray-400">
            <x-heroicon-o-folder-open class="w-10 h-10 text-gray-200" />
            <p class="text-sm font-medium text-gray-500">No projects found</p>
            <p class="text-xs text-gray-400">There are no projects across your campaigns yet.</p>
        </div>
        @endif
    </section>

</main>

<script>
(function () {
    const campaignEl = document.getElementById('filter-campaign');
    const statusEl   = document.getElementById('filter-status');
    const resetBtn   = document.getElementById('filter-reset');
    const countEl    = document.getElementById('filter-count');
    const countNum   = document.getElementById('filter-count-num');
    const emptyEl    = document.getElementById('projects-empty');

    if (!campaignEl) return;

    function applyFilters() {
        const campaign   = campaignEl.value;
        const status     = statusEl.value;
        const isFiltered = campaign || status;

        const rows = document.querySelectorAll('#projects-tbody .project-row');
        let visible = 0;

        rows.forEach(row => {
            const matchCampaign = !campaign || row.dataset.campaign === campaign;
            const matchStatus   = !status   || row.dataset.status   === status;
            const show = matchCampaign && matchStatus;
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        if (isFiltered) {
            countNum.textContent = visible;
            countEl.classList.remove('hidden');
        } else {
            countEl.classList.add('hidden');
        }

        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0 || !isFiltered);
    }

    campaignEl.addEventListener('input', applyFilters);
    statusEl.addEventListener('input', applyFilters);

    resetBtn.addEventListener('click', () => {
        campaignEl.value = '';
        statusEl.value   = '';
        applyFilters();
    });
})();
</script>
@endsection