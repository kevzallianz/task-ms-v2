@extends('layouts.user-layout')

@section('user-content')
@php
$currentUser = auth()->user();
@endphp
<main class="flex flex-col gap-6">

    <style>
        /* Highlight selected task rows for bulk assign (darker blue) */
        .campaign-task-row.selected-for-assign {
            background-color: rgba(37, 99, 235, 0.12);
            /* darker blue */
            transition: background-color 120ms ease-in-out;
        }

        .campaign-task-row.selected-for-assign td {
            background-color: transparent;
        }
    </style>

    {{-- Page Header --}}
    <article class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
                <x-heroicon-o-users class="w-6 h-6" />
                <span>{{ auth()->user()->campaignMember?->campaign?->name ?? 'No Campaign' }}</span>
                {{ auth()->user()->campaignMember?->campaign?->name ? 'Campaign' : '' }}
            </h1>
            <p class="text-sm text-gray-600">Manage your team campaigns and tasks</p>
        </div>
        {{-- Primary Action --}}
        <div class="flex items-center gap-2">
            <button id="addTaskBtn" class="flex cursor-pointer items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition">
                <x-heroicon-o-plus class="w-4 h-4 text-white" />
                Add Task
            </button>
            <button type="button" id="createProjectBtn" class="flex cursor-pointer items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
                <x-heroicon-o-folder-plus class="w-4 h-4 text-white" />
                Create Project
            </button>
            <button id="bulkAssignBtn" class="flex cursor-pointer items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-secondary rounded-lg hover:bg-secondary/90 transition">
                <x-heroicon-o-user-group class="w-4 h-4 text-white" />
                Assign Members
            </button>
            <!-- <button id="importBtn" class="cursor-pointer flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition">
                <x-heroicon-c-paper-clip class="w-4 h-4 text-white" />
                Import Tasks
            </button> -->
        </div>
    </article>

    {{-- Content Grid --}}
    @if ($campaigns->count() > 0)
    @foreach ($campaigns as $index => $campaign)
    @php $isActive = $index === 0; @endphp
    <div data-campaign-panel="{{ $campaign->id }}" class="{{ !$isActive ? 'hidden' : '' }} space-y-6">
        {{-- Top Row: Projects and Members --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Projects Section --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-secondary/30 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                            <x-heroicon-o-folder-plus class="w-5 h-5" />
                            Projects
                        </h3>
                        <span class="text-sm text-gray-500">{{ $campaign->projects->count() }} project(s)</span>
                    </div>
                    @if($campaign->projects->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($campaign->projects as $project)
                        <div class="bg-gray-50 border border-secondary/20 rounded-lg p-4 hover:border-primary/40 hover:shadow-sm transition-all">
                            <div class="flex flex-col h-full">
                                <h4 class="text-base font-medium text-primary mb-2 truncate">{{ $project->title }}</h4>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2 grow">{{ $project->description ?? 'No description' }}</p>
                                <div class="flex items-center justify-between pt-3 border-t border-secondary/10">
                                    <span class="text-xs text-gray-500">{{ $project->created_at?->format('M d, Y') }}</span>
                                    <a href="{{ route('campaigns.projects.view', ['campaign' => $campaign->id, 'project' => $project->id]) }}" class="text-sm text-primary hover:text-primary/80 font-medium">View →</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <x-heroicon-o-folder-plus class="w-12 h-12 text-gray-300 mb-2" />
                        <p class="text-sm text-gray-500">No projects yet. Create your first project to get started.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Campaign Members Sidebar --}}
            <aside class="lg:col-span-1">
                @php
                $currentMember = $campaign->members->firstWhere('id', $currentUser->id);
                $currentAccessLevel = $currentMember?->pivot->access_level ?? 'viewer';
                $canManageAccess = $currentAccessLevel === 'all';
                @endphp
                <section data-campaign-panel-members="{{ $campaign->id }}" class="bg-white border border-secondary/30 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                            <x-heroicon-o-user-group class="w-5 h-5" />
                            Members
                        </h3>
                        <span class="text-sm text-gray-500">{{ $campaign->members->count() }}</span>
                    </div>

                    <div class="flex flex-col gap-3">
                        @forelse ($campaign->members as $member)
                        @php
                        $accessLabel = match($member->pivot->access_level) {
                        'viewer' => 'View',
                        'editor' => 'Editor',
                        'all' => 'All',
                        default => ucfirst($member->pivot->access_level),
                        };
                        $accessBadgeClass = match($member->pivot->access_level) {
                        'viewer' => 'bg-gray-100 text-gray-700',
                        'editor' => 'bg-blue-100 text-blue-700',
                        'all' => 'bg-indigo-100 text-indigo-700',
                        default => 'bg-gray-100 text-gray-700',
                        };
                        @endphp
                        <div class="flex items-center justify-between p-3 border border-secondary/20 rounded-lg hover:border-secondary/40 hover:shadow-md transition-all" data-campaign-member-id="{{ $member->pivot->id }}" data-user-id="{{ $member->id }}">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold text-primary shrink-0">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-foreground truncate">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-2">
                                <span class="member-access-badge inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap {{ $accessBadgeClass }}" data-access-level="{{ $member->pivot->access_level }}">
                                    {{ $accessLabel }} Access
                                </span>

                                @if ($canManageAccess && $member->id !== $currentUser->id)
                                <button
                                    type="button"
                                    class="member-access-open p-2 rounded-md hover:bg-secondary/10 text-secondary"
                                    aria-label="Update access level for {{ $member->name }}"
                                    data-update-url="{{ route('campaigns.members.update-access', ['campaign' => $campaign->id, 'campaignMember' => $member->pivot->id]) }}"
                                    data-current-level="{{ $member->pivot->access_level }}"
                                    data-member-name="{{ $member->name }}"
                                    data-member-id="{{ $member->pivot->id }}">
                                    <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                                </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <x-heroicon-o-user-group class="w-12 h-12 text-gray-300 mb-2" />
                            <p class="text-sm text-gray-500">No members yet.</p>
                        </div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>

        {{-- All Tasks Section (Full Width) --}}
        <div class="bg-white border border-secondary/30 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                    All Tasks
                </h2>
                <span class="text-sm text-gray-500">{{ $campaign->allTasks->count() }} task(s)</span>
            </div>

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('user.campaign') }}" class="mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <label class="text-xs font-medium text-gray-700 mb-1 block">Filter by Status</label>
                        <select name="campaign_{{ $campaign->id }}_status" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm border border-secondary/30 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20">
                            <option value="">All Statuses</option>
                            <option value="planning" {{ ($campaign->filterStatus ?? '') === 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="ongoing" {{ ($campaign->filterStatus ?? '') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="on_hold" {{ ($campaign->filterStatus ?? '') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="accomplished" {{ ($campaign->filterStatus ?? '') === 'accomplished' ? 'selected' : '' }}>Accomplished</option>
                        </select>
                    </div>
                    @if($campaign->filterStatus)
                    <div class="pt-5">
                        <a href="{{ route('user.campaign') }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs text-gray-600 hover:text-gray-800 font-medium bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Clear
                        </a>
                    </div>
                    @endif
                </div>
            </form>

            @if ($campaign->allTasks->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-secondary/20">
                        <tr>
                            <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Task</th>
                            <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Project</th>
                            <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Status</th>
                            <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Start Date</th>
                            <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Target Date</th>
                            <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Completed At</th>
                            <th class="px-4 text-nowrap py-3 text-left font-semibold text-gray-700">Members</th>
                            <th class="px-4 text-nowrap py-3 text-right font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($campaign->allTasks as $task)
                        <x-campaigns.task-card :isProject="true" :task="$task" :campaign="$campaign" />
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <x-heroicon-o-clipboard-document-list class="w-12 h-12 text-gray-300 mb-2" />
                <p class="text-sm text-gray-500">No tasks found. {{ $campaign->filterStatus ? 'Try removing the filter or create a new task.' : 'Create your first task to get started.' }}</p>
            </div>
            @endif
        </div>
    </div>
    @endforeach
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-16 text-center bg-white border border-secondary/30 rounded-lg">
        <x-heroicon-o-users class="w-16 h-16 text-gray-300 mb-4" />
        <h2 class="text-lg font-semibold text-gray-600 mb-2">No campaigns yet</h2>
        <p class="text-sm text-gray-500">Join or create a campaign to get started.</p>
    </div>
    @endif

    <x-campaigns.add-task-modal :currentUser="auth()->user()" />
    <x-campaigns.edit-task-modal />
    <x-campaigns.task-status-modal />
    <x-campaigns.delete-task-modal />
    <x-campaigns.member-access-modal />
    <x-campaigns.task-remarks-modal />

    <!-- Create Campaign Project Modal -->
    <div id="createProjectModal" class="fixed flex inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-primary">Create Campaign Project</h2>
                    <p class="text-sm text-gray-600">Create a project within this campaign to group related tasks.</p>
                </div>
                <button type="button" id="createProjectModalClose" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="create-project-form" class="p-6 space-y-5" action="{{ route('campaigns.projects.store', ['campaign' => $campaigns->first()->id ?? 0]) }}" method="POST">
                @csrf
                <input type="hidden" name="campaign_id" value="{{ $campaigns->first()?->id ?? '' }}">

                <div>
                    <label for="projectTitle" class="text-sm font-medium text-foreground">Title <span class="text-red-500">*</span></label>
                    <input id="projectTitle" type="text" name="title" required maxlength="150" placeholder="Project title" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="projectTitleError"></span>
                </div>

                <div>
                    <label for="projectDescription" class="text-sm font-medium text-foreground">Description</label>
                    <textarea id="projectDescription" name="description" rows="3" placeholder="Optional description" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none"></textarea>
                    <span class="text-xs text-red-500 hidden" id="projectDescriptionError"></span>
                </div>

                <div>
                    <label for="projectStatus" class="text-sm font-medium text-foreground">Status</label>
                    <select id="projectStatus" name="status" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20">
                        <option value="planning" selected>Planning</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="on_hold">On Hold</option>
                        <option value="accomplished">Accomplished</option>
                    </select>
                    <span class="text-xs text-red-500 hidden" id="projectStatusError"></span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="projectStartDate" class="text-sm font-medium text-foreground">Start Date</label>
                        <input id="projectStartDate" type="date" name="start_date" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                        <span class="text-xs text-red-500 hidden" id="projectStartDateError"></span>
                    </div>

                    <div>
                        <label for="projectTargetDate" class="text-sm font-medium text-foreground">Target Date</label>
                        <input id="projectTargetDate" type="date" name="target_date" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                        <span class="text-xs text-red-500 hidden" id="projectTargetDateError"></span>
                    </div>
                </div>
            </form>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50">
                <button type="button" id="createProjectCancel" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">Cancel</button>
                <button type="submit" form="create-project-form" id="createProjectSubmit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition">Create Project</button>
            </div>
        </div>
    </div>

    <!-- Import Tasks Modal -->
    <div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" aria-hidden="true">
        <div id="importModalBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 w-full max-w-3xl bg-white rounded-lg shadow-lg overflow-hidden" role="dialog" aria-modal="true" aria-labelledby="importModalTitle">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 id="importModalTitle" class="text-lg font-semibold">Import Tasks</h3>
                <button id="importModalClose" class="text-secondary p-2" aria-label="Close import modal">✕</button>
            </div>

            <div class="p-4 space-y-4">
                <p class="text-sm text-gray-600">Upload a CSV or XLSX file with columns: <strong>assigned_members, title, description, start_date, target_date, status, completed_at</strong>. Assigned members can be comma-separated names; the system will search campaign members by name.</p>

                <div class="flex items-center gap-3">
                    <input id="importFileInput" type="file" accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="w-full" />
                    <button id="importPreviewBtn" class="px-4 py-2 tex-sm bg-primary text-white rounded">Preview</button>
                </div>

                <div id="importPreviewContainer" class="hidden">
                    <h4 class="text-sm font-medium mb-2">Task Preview</h4>
                    <div class="overflow-auto max-h-64 border rounded">
                        <table id="importPreviewTable" class="w-full text-sm"></table>
                    </div>
                    <div class="flex items-center justify-end gap-2 mt-3">
                        <button id="importCancelBtn" class="px-4 py-2 text-sm bg-white border rounded">Cancel</button>
                        <button id="importConfirmBtn" class="px-4 py-2 text-sm bg-primary text-white rounded">Import File</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Loading overlay inside modal -->
        <div id="importLoadingOverlay" class="hidden absolute inset-0 z-20 flex items-center justify-center bg-white/70">
            <div class="flex items-center gap-3 bg-white/0 p-4 rounded">
                <svg class="animate-spin w-5 h-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span class="text-sm text-gray-700">Processing…</span>
            </div>
        </div>
    </div>

    <!-- SheetJS CDN (used for client-side parsing of CSV/XLSX) -->
</main>
@endsection

{{-- Include Scripts --}}

<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
@vite(['resources/js/campaigns/index.js'])
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('[data-campaign-tab]');
        const taskPanels = document.querySelectorAll('[data-campaign-panel]');
        const memberPanels = document.querySelectorAll('[data-campaign-panel-members]');

        function activate(campaignId) {
            tabs.forEach(btn => {
                const isActive = btn.getAttribute('data-campaign-tab') === campaignId;
                btn.classList.toggle('bg-primary', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('border-primary', isActive);
                btn.classList.toggle('border-secondary/30', !isActive);
                btn.classList.toggle('text-foreground', !isActive);
            });

            taskPanels.forEach(panel => {
                panel.classList.toggle('hidden', panel.getAttribute('data-campaign-panel') !== campaignId);
            });

            memberPanels.forEach(panel => {
                panel.classList.toggle('hidden', panel.getAttribute('data-campaign-panel-members') !== campaignId);
            });
        }

        tabs.forEach(btn => {
            btn.addEventListener('click', () => activate(btn.getAttribute('data-campaign-tab')));
        });

        if (tabs.length) {
            activate(tabs[0].getAttribute('data-campaign-tab'));
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create Project modal handling
        const openBtn = document.getElementById('createProjectBtn');
        const modal = document.getElementById('createProjectModal');
        const closeBtn = document.getElementById('createProjectModalClose');
        const cancelBtn = document.getElementById('createProjectCancel');
        const form = document.getElementById('create-project-form');

        if (!openBtn || !modal) return;

        function open() {
            modal.classList.remove('hidden');
            modal.querySelector('input,textarea,button')?.focus();
        }

        function close() {
            modal.classList.add('hidden');
        }

        openBtn.addEventListener('click', open);
        if (closeBtn) closeBtn.addEventListener('click', close);
        if (cancelBtn) cancelBtn.addEventListener('click', close);

        // close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') close();
        });

        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = form.getAttribute('action');
            const data = $(form).serialize();
            const submitBtn = document.getElementById('createProjectSubmit');
            const originalText = submitBtn ? submitBtn.textContent : 'Create';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Creating...';
            }

            $.ajax({
                url,
                method: 'POST',
                data,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success(resp) {
                    showToast('success', resp.message || 'Project created');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                    close();
                    setTimeout(() => location.reload(), 700);
                },
                error(xhr) {
                    const msg = (xhr.responseJSON && (xhr.responseJSON.message || (xhr.responseJSON.errors && Object.values(xhr.responseJSON.errors).flat().join(' ')))) || 'Failed to create project';
                    showToast('error', msg);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                }
            });
        });
    });
</script>