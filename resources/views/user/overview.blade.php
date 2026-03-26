@extends('layouts.user-layout')

@section('user-content')
    <main class="flex flex-col gap-6">
        @php

            $statistics = [
                1 => [
                    'label' => 'Accomplished Tasks',
                    'value' => $totalAccomplishedAssigned,
                    'icon' => 'check-circle',
                    'color' => 'green',
                ],
                2 => [
                    'label' => 'Total Projects',
                    'value' => $totalProjects,
                    'icon' => 'folder',
                    'color' => 'secondary',
                ],
                3 => [
                    'label' => 'Campaign Tasks',
                    'value' => $totalCampaignTasks,
                    'icon' => 'clipboard-document-list',
                    'color' => 'blue',
                ],
                4 => [
                    'label' => 'Project Tasks',
                    'value' => $totalProjectTasks,
                    'icon' => 'clipboard-document-check',
                    'color' => 'purple',
                ],
            ];

        @endphp
        {{-- Page Header --}}
        <article class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Welcome Back, <span class="text-primary">{{ auth()->user()->name }}</span>
                </h1>
                <p class="text-sm text-gray-600">Here's what's happening with your campaigns and projects</p>
            </div>
            <div class="text-sm text-gray-500">
                <span class="font-medium">{{ now()->format('l, F j, Y') }}</span>
            </div>
        </article>

        {{-- Statistics Cards --}}
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($statistics as $statistic)
                <div class="bg-white rounded-lg p-6 border border-gray-200 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ $statistic['label'] }}</p>
                            <p class="text-xl font-bold text-{{ $statistic['color'] }} mt-2">{{ $statistic['value'] }}</p>
                        </div>
                        <div class="bg-{{ $statistic['color'] }}/10 p-3 rounded-lg">
                            <x-heroicon-o-{{ $statistic['icon'] }} class="w-5 h-5 text-{{ $statistic['color'] }}" />
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Status Overview & Alerts --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Campaign Tasks Status --}}
            <div class="bg-white rounded-lg p-6 border border-gray-200 col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <x-heroicon-o-chart-bar class="w-5 h-5 text-primary" />
                    <h2 class="text-base font-semibold text-primary">Campaign Tasks</h2>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Planning</span>
                        <span class="text-lg font-bold text-blue-600">{{ $campaignTasksStatus->get('planning', 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Ongoing</span>
                        <span class="text-lg font-bold text-yellow-600">{{ $campaignTasksStatus->get('ongoing', 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">On Hold</span>
                        <span
                            class="text-lg font-bold text-orange-600">{{ $campaignTasksStatus->get('on_hold', 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Accomplished</span>
                        <span
                            class="text-lg font-bold text-green-600">{{ $campaignTasksStatus->get('accomplished', 0) }}</span>
                    </div>
                </div>
            </div>

            {{-- Alerts & Notifications --}}
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="flex items-center gap-2 mb-4">
                    <x-heroicon-o-bell class="w-5 h-5 text-red-600" />
                    <h2 class="text-base font-semibold text-primary">Alerts</h2>
                </div>
                <div class="space-y-4">
                    @if ($overdueCampaignTasks > 0)
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-red-800 text-sm">Overdue Tasks</p>
                                    <p class="text-sm text-red-700 mt-1">You have <span
                                            class="font-bold">{{ $overdueCampaignTasks }}</span> overdue campaign task(s)
                                    </p>
                                    <ul class="mt-2 space-y-1">
                                        @foreach ($overdueCampaignTasksList as $overdueTask)
                                            <li>
                                                <a href="{{ $overdueTask->project ? route('campaigns.projects.view', [$overdueTask->campaign_id, $overdueTask->campaign_project_id]) : route('user.campaign') }}"
                                                    class="flex flex-col hover:bg-red-100 rounded p-2 transition">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="text-xs text-red-800 truncate font-medium">{{ $overdueTask->title }}</span>
                                                        <span class="text-xs font-medium text-red-600 shrink-0">
                                                            {{ \Carbon\Carbon::parse($overdueTask->target_date)->format('M d') }}
                                                        </span>
                                                    </div>
                                                    @if ($overdueTask->project)
                                                        <span class="text-xs text-red-500">{{ $overdueTask->project->title }}</span>
                                                    @elseif ($overdueTask->campaign)
                                                        <span class="text-xs text-red-500">{{ $overdueTask->campaign->name }}</span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($upcomingCampaignTasks->count() > 0)
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-clock class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-blue-800 text-sm">Upcoming Deadlines</p>
                                    <p class="text-sm text-blue-700 mt-1">{{ $upcomingCampaignTasks->count() }} task(s) due
                                        soon</p>
                                    <ul class="mt-2 space-y-1">
                                        @foreach ($upcomingCampaignTasks as $upcomingTask)
                                            @php
                                                $secs = now()->diffInSeconds($upcomingTask->target_date, false);
                                                $days = $secs > 0 ? (int) ceil($secs / 86400) : 0;
                                            @endphp
                                            <li>
                                                <a href="{{ $upcomingTask->project ? route('campaigns.projects.view', [$upcomingTask->campaign_id, $upcomingTask->campaign_project_id]) : route('user.campaign') }}"
                                                    class="flex flex-col hover:bg-blue-100 rounded p-2 transition">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="text-xs text-blue-800 truncate font-medium">{{ $upcomingTask->title }}</span>
                                                        <span class="text-xs font-medium text-blue-600 shrink-0">
                                                            {{ \Carbon\Carbon::parse($upcomingTask->target_date)->format('M d') }}
                                                            &middot;
                                                            @if ($days >= 1)
                                                                in {{ $days }}d
                                                            @else
                                                                today
                                                            @endif
                                                        </span>
                                                    </div>
                                                    @if ($upcomingTask->project)
                                                        <span class="text-xs text-blue-500">{{ $upcomingTask->project->title }}</span>
                                                    @elseif ($upcomingTask->campaign)
                                                        <span class="text-xs text-blue-500">{{ $upcomingTask->campaign->name }}</span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($overdueCampaignTasks == 0 && $upcomingCampaignTasks->count() == 0)
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
                                <div>
                                    <p class="font-semibold text-green-800 text-sm">All Clear!</p>
                                    <p class="text-sm text-green-700 mt-1">No overdue tasks or urgent deadlines</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Recent Activity --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Upcoming Tasks --}}
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-5 h-5 text-primary" />
                            <h2 class="text-base font-semibold text-primary">Upcoming Campaign Tasks</h2>
                        </div>
                        <a href="{{ route('user.campaign') }}" class="text-sm text-primary hover:underline">View All</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($upcomingCampaignTasks as $task)
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-900 truncate">{{ $task->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Campaign:</span> {{ $task->campaign->name }}
                                    </p>
                                    @if ($task->taskMembers->count() > 0)
                                        <div class="flex items-center gap-2 mt-2">
                                            <x-heroicon-o-user class="w-4 h-4 text-gray-400" />
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($task->taskMembers as $taskMember)
                                                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                                        {{ $taskMember->campaignMember->user->name }}
                                                    </span>
                                                @endforeach
                                                @if ($task->taskMembers->count() > 3)
                                                    <span
                                                        class="text-xs text-gray-500">+{{ $task->taskMembers->count() - 3 }}
                                                        more</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    @php
                                        $secondsUntil = now()->diffInSeconds($task->target_date, false);
                                        $daysUntil = $secondsUntil > 0 ? (int) ceil($secondsUntil / 86400) : 0;
                                        $isUrgent = $secondsUntil >= 0 && $secondsUntil <= 3 * 86400;
                                    @endphp
                                    <span class="text-xs font-medium {{ $isUrgent ? 'text-red-600' : 'text-gray-600' }}">
                                        {{ now()->parse($task->target_date)->format('M d, Y') }}
                                    </span>
                                    @if ($isUrgent)
                                        <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">
                                            @if ($daysUntil >= 1)
                                                Due in {{ $daysUntil }} {{ $daysUntil == 1 ? 'day' : 'days' }}
                                            @else
                                                Due today
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <x-heroicon-o-calendar-days class="w-12 h-12 text-gray-300 mx-auto mb-2" />
                            <p class="text-sm text-gray-500">No upcoming campaign tasks</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Projects --}}
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-folder class="w-5 h-5 text-primary" />
                            <h2 class="text-base font-semibold text-primary">Recent Projects</h2>
                        </div>
                        <a href="{{ route('user.projects') }}" class="text-sm text-primary hover:underline">View All</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recentProjects as $project)
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-900 truncate">{{ $project->name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                        {{ $project->description ?? 'No description' }}
                                    </p>
                                    @if ($project->campaign)
                                        <p class="text-xs text-gray-500 mt-2">
                                            <span class="font-medium">Campaign:</span> {{ $project->campaign->name }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    @php
                                        $statusColors = [
                                            'planning' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'ongoing' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'on_hold' => 'bg-orange-100 text-orange-700 border-orange-200',
                                            'accomplished' => 'bg-green-100 text-green-700 border-green-200',
                                        ];
                                    @endphp
                                    <span
                                        class="text-xs px-2 py-1 rounded border {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $project->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <x-heroicon-o-folder-open class="w-12 h-12 text-gray-300 mx-auto mb-2" />
                            <p class="text-sm text-gray-500">No recent projects</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Task Calendar --}}
        <section class="bg-white rounded-lg border border-gray-200 p-6">
            {{-- Header & Legend --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-calendar-days class="w-5 h-5 text-primary" />
                    <h2 class="text-base font-semibold text-primary">Campaign Task Calendar</h2>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                        <span class="text-gray-600">Planning</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-yellow-500 inline-block"></span>
                        <span class="text-gray-600">Ongoing</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-orange-500 inline-block"></span>
                        <span class="text-gray-600">On Hold</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                        <span class="text-gray-600">Accomplished / Completed</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span>
                        <span class="text-gray-600">Pending</span>
                    </div>
                    <span class="w-px h-4 bg-gray-200"></span>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-block w-1 h-3 rounded-sm bg-purple-400"></span>
                        <span class="text-gray-600">Project Task</span>
                    </div>
                </div>
            </div>

            {{-- Month Navigation --}}
            <div class="flex items-center justify-between mb-4">
                <button id="cal-prev" class="p-2 rounded-lg hover:bg-gray-100 transition">
                    <x-heroicon-o-chevron-left class="w-4 h-4 text-gray-600" />
                </button>
                <h3 id="cal-month-label" class="text-sm font-semibold text-gray-800"></h3>
                <button id="cal-next" class="p-2 rounded-lg hover:bg-gray-100 transition">
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-600" />
                </button>
            </div>

            {{-- Day-of-week headers --}}
            <div class="grid grid-cols-7 mb-1">
                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dow)
                    <div class="text-center text-xs font-medium text-gray-500 py-1">{{ $dow }}</div>
                @endforeach
            </div>

            {{-- Calendar Grid --}}
            <div id="cal-grid" class="grid grid-cols-7 gap-1"></div>

            {{-- Task Detail Panel --}}
            <div id="cal-detail" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <h4 id="cal-detail-date" class="text-sm font-semibold text-gray-800"></h4>
                    <button id="cal-detail-close" class="text-gray-400 hover:text-gray-600 transition">
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                    </button>
                </div>
                <div id="cal-detail-tasks" class="space-y-2 max-h-56 overflow-y-auto"></div>
            </div>
        </section>

        <script>
            (function() {
                const tasks = @json($calendarTasks);

                // Index tasks by target_date (deadline day)
                const tasksByDate = {};
                tasks.forEach(function(task) {
                    const d = task.target_date;
                    if (!tasksByDate[d]) tasksByDate[d] = [];
                    tasksByDate[d].push(task);
                });

                // Index by every day in the start→end span (for bar highlighting)
                const spansByDate = {};
                tasks.forEach(function(task) {
                    if (!task.start_date) return;
                    const start = new Date(task.start_date + 'T00:00:00');
                    const end = new Date(task.target_date + 'T00:00:00');
                    for (let cur = new Date(start); cur <= end; cur.setDate(cur.getDate() + 1)) {
                        const key = cur.getFullYear() + '-' + pad(cur.getMonth() + 1) + '-' + pad(cur.getDate());
                        if (!spansByDate[key]) spansByDate[key] = [];
                        spansByDate[key].push(task);
                    }
                });

                const statusConfig = {
                    planning: {
                        color: '#3b82f6',
                        textClass: 'text-blue-700',
                        bgClass: 'bg-blue-100',
                        dot: 'bg-blue-500',
                        badge: 'bg-blue-100 text-blue-700 border-blue-200',
                        label: 'Planning'
                    },
                    ongoing: {
                        color: '#eab308',
                        textClass: 'text-yellow-700',
                        bgClass: 'bg-yellow-100',
                        dot: 'bg-yellow-500',
                        badge: 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        label: 'Ongoing'
                    },
                    on_hold: {
                        color: '#f97316',
                        textClass: 'text-orange-700',
                        bgClass: 'bg-orange-100',
                        dot: 'bg-orange-500',
                        badge: 'bg-orange-100 text-orange-700 border-orange-200',
                        label: 'On Hold'
                    },
                    accomplished: {
                        color: '#22c55e',
                        textClass: 'text-green-700',
                        bgClass: 'bg-green-100',
                        dot: 'bg-green-500',
                        badge: 'bg-green-100 text-green-700 border-green-200',
                        label: 'Accomplished'
                    },
                    pending: {
                        color: '#9ca3af',
                        textClass: 'text-gray-600',
                        bgClass: 'bg-gray-100',
                        dot: 'bg-gray-400',
                        badge: 'bg-gray-100 text-gray-600 border-gray-200',
                        label: 'Pending'
                    },
                    completed: {
                        color: '#22c55e',
                        textClass: 'text-green-700',
                        bgClass: 'bg-green-100',
                        dot: 'bg-green-500',
                        badge: 'bg-green-100 text-green-700 border-green-200',
                        label: 'Completed'
                    },
                };

                const isDone = (task) => task.status === 'accomplished' || task.status === 'completed';

                const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];

                const todayRaw = new Date();
                todayRaw.setHours(0, 0, 0, 0);
                let viewYear = todayRaw.getFullYear();
                let viewMonth = todayRaw.getMonth();

                function pad(n) {
                    return String(n).padStart(2, '0');
                }

                function todayStr() {
                    return `${todayRaw.getFullYear()}-${pad(todayRaw.getMonth() + 1)}-${pad(todayRaw.getDate())}`;
                }

                function fmtShort(dateStr) {
                    return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric'
                    });
                }

                function fmtLong(dateStr) {
                    return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', {
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric'
                    });
                }

                function escHtml(str) {
                    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                        '&quot;');
                }

                function renderCalendar() {
                    const grid = document.getElementById('cal-grid');
                    const label = document.getElementById('cal-month-label');
                    grid.innerHTML = '';
                    document.getElementById('cal-detail').classList.add('hidden');

                    label.textContent = MONTHS[viewMonth] + ' ' + viewYear;

                    const firstDay = new Date(viewYear, viewMonth, 1).getDay();
                    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
                    const tStr = todayStr();
                    const prefix = `${viewYear}-${pad(viewMonth + 1)}-`;

                    // Empty leading cells
                    for (let i = 0; i < firstDay; i++) {
                        const empty = document.createElement('div');
                        empty.className = 'rounded-lg min-h-28';
                        grid.appendChild(empty);
                    }

                    for (let d = 1; d <= daysInMonth; d++) {
                        const dateStr = prefix + pad(d);
                        const deadlines = tasksByDate[dateStr] || []; // tasks due on this day
                        const spanning = spansByDate[dateStr] || []; // tasks active across this day
                        const isToday = dateStr === tStr;
                        const hasActivity = deadlines.length > 0 || spanning.length > 0;

                        // Merge for detail panel
                        const allMap = new Map();
                        spanning.forEach(t => allMap.set(t.title + '|' + t.target_date, {
                            ...t,
                            isDue: false
                        }));
                        deadlines.forEach(t => {
                            const k = t.title + '|' + t.target_date;
                            if (allMap.has(k)) allMap.get(k).isDue = true;
                            else allMap.set(k, {
                                ...t,
                                isDue: true
                            });
                        });
                        const allDay = [...allMap.values()];

                        const cell = document.createElement('div');
                        cell.className = [
                            'min-h-28 rounded-lg border flex flex-col transition select-none overflow-hidden',
                            isToday ? 'border-primary bg-blue-50' : 'border-gray-100 hover:bg-gray-50',
                            hasActivity ? 'cursor-pointer' : '',
                        ].join(' ');

                        // ── Top: date number + "due today" indicator ──
                        const header = document.createElement('div');
                        header.className = 'flex items-start justify-between px-2 pt-2 pb-1';

                        const num = document.createElement('span');
                        num.className = 'text-sm font-bold leading-none ' + (isToday ? 'text-primary' : 'text-gray-700');
                        num.textContent = d;
                        header.appendChild(num);

                        const nonAccomplishedDeadlines = deadlines.filter(t => !isDone(t));
                        if (nonAccomplishedDeadlines.length > 0 && dateStr === tStr) {
                            const dueLabel = document.createElement('span');
                            dueLabel.className = 'text-red-500 font-bold leading-none text-xs';
                            dueLabel.textContent = '● DUE';
                            header.appendChild(dueLabel);
                        }
                        cell.appendChild(header);

                        // ── Task chips: show tasks whose deadline is THIS day (with name + due date) ──
                        if (deadlines.length > 0) {
                            const chipWrap = document.createElement('div');
                            chipWrap.className = 'flex flex-col gap-1 px-1.5';

                            const visible = deadlines.slice(0, 2);
                            visible.forEach(function(task) {
                                const cfg = statusConfig[task.status] || {
                                    color: '#9ca3af',
                                    textClass: 'text-gray-700',
                                    bgClass: 'bg-gray-100'
                                };
                                const chip = document.createElement('div');
                                chip.className = cfg.bgClass +
                                    ' rounded-md px-1.5 py-1 border border-white/60 hover:opacity-80 transition';
                                chip.style.borderLeft = task.type === 'project' ? '3px solid #a855f7' : '';
                                chip.style.cursor = 'pointer';
                                chip.addEventListener('click', function(e) {
                                    e.stopPropagation();
                                    if (task.url) window.location.href = task.url;
                                });

                                const titleEl = document.createElement('p');
                                titleEl.className = cfg.textClass + ' font-semibold truncate text-xs leading-snug';
                                titleEl.textContent = task.title;

                                const dateEl = document.createElement('p');
                                dateEl.className = 'text-gray-500 truncate text-xs leading-snug';
                                if (task.status === 'accomplished' && task.completed_at) {
                                    dateEl.textContent = 'Done: ' + fmtShort(task.completed_at);
                                } else if (task.status === 'completed') {
                                    dateEl.textContent = 'Completed';
                                } else {
                                    dateEl.textContent = 'Due: ' + fmtShort(task.target_date);
                                }

                                chip.appendChild(titleEl);
                                chip.appendChild(dateEl);
                                chipWrap.appendChild(chip);
                            });

                            if (deadlines.length > 2) {
                                const more = document.createElement('p');
                                more.className = 'text-gray-400 px-1 text-xs';
                                more.textContent = '+' + (deadlines.length - 2) + ' more';
                                chipWrap.appendChild(more);
                            }

                            cell.appendChild(chipWrap);
                        }

                        // ── Duration bars at the bottom for spanning tasks ──
                        if (spanning.length > 0) {
                            const barsWrap = document.createElement('div');
                            barsWrap.className = 'mt-auto flex flex-col';
                            barsWrap.style.gap = '1px';

                            const visibleBars = spanning.slice(0, 3);
                            visibleBars.forEach(function(task) {
                                const cfg = statusConfig[task.status] || {
                                    color: '#9ca3af'
                                };
                                const isStartDay = task.start_date === dateStr;
                                const isEndDay = task.target_date === dateStr;

                                const bar = document.createElement('div');
                                bar.style.height = '7px';
                                bar.style.backgroundColor = cfg.color;
                                bar.style.opacity = '0.65';
                                bar.style.marginLeft = isStartDay ? '4px' : '0';
                                bar.style.marginRight = isEndDay ? '4px' : '0';
                                bar.style.borderRadius = isStartDay && isEndDay ? '3px' :
                                    isStartDay ? '3px 0 0 3px' :
                                    isEndDay ? '0 3px 3px 0' :
                                    '0';
                                bar.title = task.title + ' (' + (task.start_date ? fmtShort(task.start_date) +
                                    ' → ' : '') + fmtShort(task.target_date) + ')';
                                barsWrap.appendChild(bar);
                            });

                            if (spanning.length > 3) {
                                const more = document.createElement('span');
                                more.className = 'text-gray-400 text-right pr-1 text-xs';
                                more.textContent = '+' + (spanning.length - 3);
                                barsWrap.appendChild(more);
                            }

                            cell.appendChild(barsWrap);
                        }

                        if (hasActivity) {
                            cell.addEventListener('click', function() {
                                showDetail(dateStr, allDay);
                            });
                        }

                        grid.appendChild(cell);
                    }
                }

                function showDetail(dateStr, allDay) {
                    const panel = document.getElementById('cal-detail');
                    const panelDate = document.getElementById('cal-detail-date');
                    const panelTasks = document.getElementById('cal-detail-tasks');

                    panelDate.textContent = new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    panelTasks.innerHTML = '';
                    allDay.forEach(function(task) {
                        const cfg = statusConfig[task.status] || {
                            badge: 'bg-gray-100 text-gray-700 border-gray-200',
                            label: task.status
                        };

                        let dateRange;
                        if (task.status === 'accomplished' && task.completed_at) {
                            const rangeStart = task.start_date ? fmtLong(task.start_date) + ' &rarr; ' : '';
                            dateRange = rangeStart + 'Accomplished: ' + fmtLong(task.completed_at);
                        } else if (task.status === 'completed') {
                            const rangeStart = task.start_date ? fmtLong(task.start_date) + ' &rarr; ' : '';
                            dateRange = rangeStart + fmtLong(task.target_date) +
                                ' <span class="text-green-600 font-medium">(Completed)</span>';
                        } else if (task.start_date && task.start_date !== task.target_date) {
                            dateRange = fmtLong(task.start_date) + ' &rarr; ' + fmtLong(task.target_date);
                        } else {
                            dateRange = 'Due: ' + fmtLong(task.target_date);
                        }

                        const typeTag = task.type === 'project' ?
                            '<span class="shrink-0 text-xs px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded border border-purple-200 font-medium">Project Task</span>' :
                            '';

                        const dueTag = (task.isDue && !isDone(task) && dateStr === todayStr()) ?
                            '<span class="shrink-0 text-xs px-1.5 py-0.5 bg-red-100 text-red-700 rounded border border-red-200 font-medium">Due today</span>' :
                            '';

                        const sourceLabel = task.type === 'project' ? 'Project' : 'Campaign';

                        const row = document.createElement('a');
                        row.href = task.url || '#';
                        row.className =
                            'flex items-start justify-between p-2.5 bg-white rounded-lg border border-gray-200 gap-3 hover:bg-gray-50 transition cursor-pointer no-underline';
                        row.style.cssText = 'text-decoration:none;color:inherit;' + (task.type === 'project' ?
                            'border-left:3px solid #a855f7;' : '');
                        row.innerHTML =
                            '<div class="flex-1 min-w-0">' +
                            '<p class="text-sm font-medium text-gray-800 truncate">' + escHtml(task.title) +
                            '</p>' +
                            '<p class="text-xs text-gray-500 mt-0.5"><span class="font-medium">' + sourceLabel +
                            ':</span> ' + escHtml(task.source || '') + '</p>' +
                            '<p class="text-xs text-gray-400 mt-1">' + dateRange + '</p>' +
                            '</div>' +
                            '<div class="flex flex-col items-end gap-1 shrink-0">' +
                            '<span class="text-xs px-2 py-0.5 rounded border ' + cfg.badge + '">' + cfg.label +
                            '</span>' +
                            typeTag +
                            dueTag +
                            '</div>';
                        panelTasks.appendChild(row);
                    });

                    panel.classList.remove('hidden');
                    panel.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }

                document.getElementById('cal-prev').addEventListener('click', function() {
                    viewMonth--;
                    if (viewMonth < 0) {
                        viewMonth = 11;
                        viewYear--;
                    }
                    renderCalendar();
                });

                document.getElementById('cal-next').addEventListener('click', function() {
                    viewMonth++;
                    if (viewMonth > 11) {
                        viewMonth = 0;
                        viewYear++;
                    }
                    renderCalendar();
                });

                document.getElementById('cal-detail-close').addEventListener('click', function() {
                    document.getElementById('cal-detail').classList.add('hidden');
                });

                renderCalendar();
            }());
        </script>

        {{-- Quick Actions --}}
        <section class="bg-linear-to-r from-primary to-secondary rounded-lg p-6 text-white">
            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('user.campaign') }}"
                    class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg p-4 transition flex items-center gap-3 group">
                    <div class="bg-white/20 p-3 rounded-lg">
                        <x-heroicon-o-users class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="font-semibold">View Campaigns</p>
                        <p class="text-sm text-white/80">Manage your team campaigns</p>
                    </div>
                </a>
                <a href="{{ route('user.projects') }}"
                    class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg p-4 transition flex items-center gap-3 group">
                    <div class="bg-white/20 p-3 rounded-lg">
                        <x-heroicon-o-folder class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="font-semibold">View Projects</p>
                        <p class="text-sm text-white/80">Browse all your projects</p>
                    </div>
                </a>
            </div>
        </section>
    </main>
@endsection
