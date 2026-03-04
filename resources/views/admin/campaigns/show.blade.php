@extends('layouts.user-layout')

@section('user-content')
    <main class="flex flex-col gap-6">

        {{-- Page Header --}}
        <article class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <a href="{{ route('admin.campaigns') }}"
                    class="mt-1 p-1.5 rounded-lg border border-gray-200 text-gray-400 hover:text-primary hover:border-primary/40 transition">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
                        <x-heroicon-o-rectangle-stack class="w-6 h-6" />
                        {{ $campaign->name }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $campaign->description ?? 'No description provided.' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0 mt-1">
                <div
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-primary/5 border border-primary/20 rounded-lg text-xs font-medium text-primary">
                    <x-heroicon-o-users class="w-3.5 h-3.5" />
                    {{ $campaign->campaignMembers->count() }} Members
                </div>
                <div
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-secondary/5 border border-secondary/20 rounded-lg text-xs font-medium text-secondary">
                    <x-heroicon-o-folder-open class="w-3.5 h-3.5" />
                    {{ $campaign->projects->count() }} Projects
                </div>
                <div
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-accent/5 border border-accent/20 rounded-lg text-xs font-medium text-accent">
                    <x-heroicon-o-clipboard-document-list class="w-3.5 h-3.5" />
                    {{ $campaign->tasks->count() }} Tasks
                </div>
            </div>
        </article>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- LEFT: Projects + Tasks --}}
            <div class="lg:col-span-2 flex flex-col gap-6">
                {{-- Projects --}}
                <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-base font-semibold text-foreground flex items-center gap-2">
                            <x-heroicon-o-folder-open class="w-4 h-4 text-secondary" />
                            Projects
                        </h2>
                        <span class="text-xs text-foreground font-medium">{{ $campaign->projects->count() }} total</span>
                    </div>

                    @if ($campaign->projects->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th
                                            class="text-left text-sm font-medium text-foreground uppercase px-5 py-2.5">
                                            Title</th>
                                        <th
                                            class="text-left text-sm font-medium text-foreground uppercase px-4 py-2.5">
                                            Status</th>
                                        <th
                                            class="text-left text-sm font-medium text-foreground uppercase px-4 py-2.5">
                                            Start Date</th>
                                        <th
                                            class="text-left text-sm font-medium text-foreground uppercase px-4 py-2.5">
                                            Target Date</th>
                                        <th
                                            class="text-left text-sm font-medium text-foreground uppercase px-4 py-2.5">
                                            Created</th>
                                        <th class="px-4 py-2.5"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($campaign->projects as $project)
                                        @php
                                            $statusMap = [
                                                'planning' => ['bg-secondary/10 text-secondary', 'Planning'],
                                                'ongoing' => ['bg-green-100 text-green-700', 'Ongoing'],
                                                'on_hold' => ['bg-accent/10 text-accent', 'On Hold'],
                                                'accomplished' => ['bg-gray-100 text-gray-500', 'Accomplished'],
                                                'cancelled' => ['bg-red-50 text-red-500', 'Cancelled'],
                                            ];
                                            [$badgeClass, $badgeText] = $statusMap[$project->status] ?? [
                                                'bg-gray-100 text-gray-500',
                                                ucfirst(str_replace('_', ' ', $project->status)),
                                            ];
                                        @endphp
                                        <tr class="hover:bg-gray-50/60 transition-colors">
                                            <td class="px-5 py-2.5">
                                                <p class="text-xs font-medium text-foreground">{{ $project->title }}</p>
                                                @if ($project->description)
                                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">
                                                        {{ $project->description }}</p>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                                    {{ $badgeText }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2.5 text-sm text-foreground">
                                                {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : '—' }}
                                            </td>
                                            <td class="px-4 py-2.5 text-sm text-foreground">
                                                {{ $project->target_date ? \Carbon\Carbon::parse($project->target_date)->format('M d, Y') : '—' }}
                                            </td>
                                            <td class="px-4 py-2.5 text-sm text-foreground">
                                                {{ $project->created_at->diffForHumans() }}
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <a href="{{ route('admin.campaigns.projects.show', [$campaign, $project]) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary border border-primary/30 bg-primary/5 rounded-lg hover:bg-primary/10 transition whitespace-nowrap">
                                                    <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex items-center gap-3 px-5 py-6 text-gray-400">
                            <x-heroicon-o-folder-open class="w-5 h-5 text-gray-300 shrink-0" />
                            <p class="text-sm">No projects in this campaign.</p>
                        </div>
                    @endif
                </section>


            </div>

            {{-- RIGHT: Members --}}
            <div class="lg:col-span-1">
                <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-foreground flex items-center gap-2">
                            <x-heroicon-o-users class="w-4 h-4 text-primary" />
                            Members
                        </h2>
                        <span class="text-xs text-gray-400 font-medium">{{ $campaign->campaignMembers->count() }}
                            total</span>
                    </div>

                    @if ($campaign->campaignMembers->isNotEmpty())
                        <ul class="divide-y divide-gray-100">
                            @foreach ($campaign->campaignMembers as $member)
                                <li class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/60 transition-colors">
                                    <div
                                        class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center shrink-0">
                                        <span class="text-xs font-bold text-primary uppercase">
                                            {{ substr($member->user->name ?? '?', 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-foreground truncate">
                                            {{ $member->user->name ?? '—' }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $member->user->email ?? '' }}</p>
                                    </div>
                                    @if ($member->access_level)
                                        <span
                                            class="shrink-0 text-xs px-2 py-0.5 rounded-full font-medium
                                            {{ $member->access_level === 'admin' ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-500' }}">
                                            {{ ucfirst($member->access_level) }}
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex items-center gap-3 px-5 py-6 text-gray-400">
                            <x-heroicon-o-users class="w-5 h-5 text-gray-300 shrink-0" />
                            <p class="text-sm">No members in this campaign.</p>
                        </div>
                    @endif
                </section>
            </div>

            {{-- Tasks --}}
            <section class="bg-white rounded-xl col-span-3 border border-gray-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-foreground flex items-center gap-2">
                        <x-heroicon-o-clipboard-document-list class="w-4 h-4 text-accent" />
                        Tasks
                    </h2>
                    <span class="text-xs text-gray-400 font-medium">{{ $campaign->tasks->count() }} total</span>
                </div>

                {{-- Filters --}}
                <div class="px-5 py-3 bg-gray-50/70 border-b border-gray-100 flex flex-wrap items-end gap-3">
                    {{-- Status Filter --}}
                    <div class="flex flex-col gap-1 min-w-36">
                        <label class="text-[10px] font-semibold text-foreground uppercase tracking-wider">Status</label>
                        <select id="filter-status"
                            class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-white text-foreground focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                            <option value="">All Statuses</option>
                            <option value="planning">Planning</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="on_hold">On Hold</option>
                            <option value="accomplished">Done</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    {{-- Project Filter --}}
                    <div class="flex flex-col gap-1 min-w-44">
                        <label class="text-[10px] font-semibold text-foreground uppercase tracking-wider">Project</label>
                        <select id="filter-project"
                            class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-white text-foreground focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                            <option value="">All Projects</option>
                            @foreach ($campaign->projects as $proj)
                                <option value="{{ $proj->id }}">{{ $proj->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Member Filter --}}
                    <div class="flex flex-col gap-1 min-w-44">
                        <label class="text-[10px] font-semibold text-foreground uppercase tracking-wider">Assigned To</label>
                        <select id="filter-member"
                            class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-white text-foreground focus:border-primary focus:ring-1 focus:ring-primary/20 transition">
                            <option value="">All Members</option>
                            @foreach ($campaign->campaignMembers as $cm)
                                <option value="{{ $cm->id }}">{{ $cm->user->name ?? '—' }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Reset --}}
                    <button id="filter-reset"
                        class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 bg-white rounded-lg hover:bg-gray-50 transition flex items-center gap-1.5">
                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                        Reset
                    </button>

                    <span id="filter-count" class="ml-auto text-xs text-gray-400 font-medium self-end pb-0.5 hidden">
                        <span id="filter-count-num">0</span> result(s)
                    </span>
                </div>

                @if ($campaign->tasks->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th
                                        class="text-left text-sm font-medium text-foreground uppercase px-5 py-2.5">
                                        Task</th>
                                    <th
                                        class="text-left text-sm font-medium text-foreground uppercase px-4 py-2.5">
                                        Project</th>
                                    <th
                                        class="text-left text-sm font-medium text-foreground uppercase px-4 py-2.5">
                                        Assigned To</th>
                                    <th
                                        class="text-left text-sm font-medium text-foreground uppercase px-4 py-2.5">
                                        Status</th>
                                    <th
                                        class="text-left text-sm font-medium text-foreground uppercase px-4 py-2.5">
                                        Target</th>
                                </tr>
                            </thead>
                            <tbody id="tasks-tbody" class="divide-y divide-gray-100">
                                @foreach ($campaign->tasks as $task)
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
                                        $memberIds = $task->assignedMembers->pluck('id')->join(',');
                                    @endphp
                                    <tr class="task-row hover:bg-gray-50/60 transition-colors"
                                        data-status="{{ $task->status }}"
                                        data-project="{{ $task->campaign_project_id ?? '' }}"
                                        data-members="{{ $memberIds }}">
                                        <td class="px-5 py-2.5">
                                            <p class="text-sm font-medium text-foreground">{{ $task->title }}</p>
                                            @if ($task->description)
                                                <p class="text-sm text-gray-400 line-clamp-1 mt-0.5">
                                                    {{ $task->description }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5">
                                            @if ($task->project)
                                                <span
                                                    class="text-sm truncate max-w-30 block">{{ $task->project->title }}</span>
                                            @else
                                                <span class="text-sm text-gray-300">—</span>
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
                                                                class="text-sm text-primary font-medium whitespace-nowrap">{{ $assigned->user->name ?? '—' }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-300">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-sm font-semibold {{ $taskBadge }}">
                                                {{ $taskLabel }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5 text-sm text-foreground">
                                            {{ $task->target_date ? \Carbon\Carbon::parse($task->target_date)->format('M d, Y') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="tasks-empty" class="hidden flex items-center gap-3 px-5 py-6 text-gray-400">
                            <x-heroicon-o-funnel class="w-5 h-5 text-gray-300 shrink-0" />
                            <p class="text-sm">No tasks match the selected filters.</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 px-5 py-6 text-gray-400">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-300 shrink-0" />
                        <p class="text-sm">No tasks in this campaign.</p>
                    </div>
                @endif
            </section>

        </div>
    </main>

    <script>
        (function () {
            const statusEl  = document.getElementById('filter-status');
            const projectEl = document.getElementById('filter-project');
            const memberEl  = document.getElementById('filter-member');
            const resetBtn  = document.getElementById('filter-reset');
            const countEl   = document.getElementById('filter-count');
            const countNum  = document.getElementById('filter-count-num');
            const emptyEl   = document.getElementById('tasks-empty');

            if (!statusEl) return;

            function applyFilters() {
                const status  = statusEl.value;
                const project = projectEl.value;
                const member  = memberEl.value;
                const isFiltered = status || project || member;

                const rows = document.querySelectorAll('#tasks-tbody .task-row');
                let visible = 0;

                rows.forEach(row => {
                    const rowStatus  = row.dataset.status  ?? '';
                    const rowProject = row.dataset.project ?? '';
                    const rowMembers = (row.dataset.members ?? '').split(',');

                    const matchStatus  = !status  || rowStatus === status;
                    const matchProject = !project || rowProject === project;
                    const matchMember  = !member  || rowMembers.includes(member);

                    const show = matchStatus && matchProject && matchMember;
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

            statusEl.addEventListener('input', applyFilters);
            projectEl.addEventListener('input', applyFilters);
            memberEl.addEventListener('input', applyFilters);

            resetBtn.addEventListener('click', () => {
                statusEl.value  = '';
                projectEl.value = '';
                memberEl.value  = '';
                applyFilters();
            });
        })();
    </script>
@endsection