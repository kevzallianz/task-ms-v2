@props(['campaign', 'project'])

<div id="addTaskModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/50">
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-2xl">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-primary">Create Project Task</h2>
                <p class="text-sm text-gray-600">Add a new task for this project and assign campaign members</p>
            </div>
            <button type="button" id="closeAddTaskModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="addTaskForm" method="POST" action="{{ route('user.campaign.project.tasks.store', ['campaign' => $campaign->id, 'project' => $project->id]) }}" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="status" value="planning" />

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label for="taskTitle" class="text-sm font-medium text-foreground">Title <span class="text-red-500">*</span></label>
                    <input id="taskTitle" name="title" type="text" maxlength="100" placeholder="Enter task title" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" required />
                </div>

                <div>
                    <label for="taskStartDate" class="text-sm font-medium text-foreground">Start Date</label>
                    <input id="taskStartDate" name="start_date" type="date" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                </div>

                <div>
                    <label for="taskTargetDate" class="text-sm font-medium text-foreground">Target Date</label>
                    <input id="taskTargetDate" name="target_date" type="date" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                </div>

                <div class="col-span-2">
                    <label class="text-sm font-medium text-foreground">Assign Members</label>

                    <div id="taskAssignedMembersContainer" class="border border-secondary/30 rounded-lg p-3 bg-gray-50">
                        <div id="selectedMemberBadges" class="flex flex-wrap gap-2 mb-2 min-h-8 p-2 bg-white">
                            <span class="text-xs text-gray-400 italic" id="noSelectionText">No members selected</span>
                        </div>

                        <div class="text-xs text-gray-600 mb-1">Click to add:</div>
                        <div id="taskAssignedMembers" class="space-y-2 max-h-48 overflow-y-auto">
                        @php
                            $cmembers = $campaign->campaignMembers ?? $campaign->campaignMembers()->with('user')->get();
                        @endphp
                        @if(isset($campaign) && $cmembers && $cmembers->count() > 0)
                            @foreach($cmembers as $campaignMember)
                                @php $user = optional($campaignMember->user); @endphp
                                <button type="button" data-member-id="{{ $campaignMember->id }}" class="w-full text-left px-3 py-2 rounded-lg hover:bg-white/50 text-sm flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-600">{{ strtoupper(substr($user->name ?? 'U',0,1)) }}</div>
                                    <div>
                                        <div class="font-medium text-foreground">{{ $user->name ?? 'Member #' . $campaignMember->user_id }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email ?? '' }}</div>
                                    </div>
                                </button>
                            @endforeach
                        @else
                            <p class="text-xs text-gray-500">No campaign members available for assignment.</p>
                        @endif
                        </div>
                    </div>

                    {{-- Hidden inputs container for selected members --}}
                    <div id="assignedMemberInputs" class="hidden"></div>
                </div>

                <div class="col-span-2">
                    <label for="taskDescription" class="text-sm font-medium text-foreground">Description (optional)</label>
                    <textarea id="taskDescription" name="description" rows="3" placeholder="Add task details and notes" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none"></textarea>
                </div>
            </div>
        </form>

        {{-- Modal Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50">
            <button type="button" id="cancelAddTaskBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">Cancel</button>
                <button type="submit" id="submitAddTaskBtn" form="addTaskForm" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                    <span id="submitAddTaskSpinner" class="hidden mr-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                    <x-heroicon-o-plus class="w-4 h-4" />
                    <span id="submitAddTaskText">Create Task</span>
                </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('addTaskModal');
        const closeBtn = document.getElementById('closeAddTaskModal');
        const cancelBtn = document.getElementById('cancelAddTaskBtn');
        const form = document.getElementById('addTaskForm');

        // Support different ways the opener might be rendered
        const openBtn = document.getElementById('createTaskBtn') || document.querySelector('[data-open-create-task]');

        function openModal() {
            if (!modal) return;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            if (form) form.reset();
            // clear selected member inputs/badges if present
            const inputs = modal.querySelectorAll('#assignedMemberInputs input');
            inputs.forEach(i => i.remove());
            const badges = modal.querySelector('#selectedMemberBadges');
            if (badges) badges.innerHTML = '<span class="text-xs text-gray-400 italic" id="noSelectionText">No members selected</span>';
        }

        if (openBtn) openBtn.addEventListener('click', function (e) { e.preventDefault(); openModal(); });
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
        });

        // Member selection: toggle hidden inputs and badges
        const memberList = document.getElementById('taskAssignedMembers');
        const badges = document.getElementById('selectedMemberBadges');
        const inputsContainer = document.getElementById('assignedMemberInputs');

        if (memberList && badges && inputsContainer) {
            memberList.addEventListener('click', function (e) {
                const btn = e.target.closest('button[data-member-id]');
                if (!btn) return;
                const id = btn.getAttribute('data-member-id');
                const name = btn.querySelector('.font-medium')?.textContent?.trim() || btn.textContent.trim();

                // if already selected, remove
                const existing = inputsContainer.querySelector('input[value="' + id + '"]');
                if (existing) {
                    existing.remove();
                    const badge = badges.querySelector('[data-badge-id="' + id + '"]');
                    if (badge) badge.remove();
                    btn.classList.remove('bg-primary/10');
                    return;
                }

                // add hidden input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'assigned_member_ids[]';
                input.value = id;
                inputsContainer.appendChild(input);

                // remove 'no selection' helper
                const noSel = badges.querySelector('#noSelectionText');
                if (noSel) noSel.remove();

                // add badge
                const span = document.createElement('span');
                span.className = 'inline-flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full text-xs';
                span.setAttribute('data-badge-id', id);
                span.innerHTML = '<span class="font-medium">' + (name) + '</span> <button type="button" aria-label="remove" class="ml-2 text-gray-500">✕</button>';
                badges.appendChild(span);
                btn.classList.add('bg-primary/10');
            });


            // Ajax form submit with loading state and redirect on success
            const submitBtn = document.getElementById('submitAddTaskBtn');
            const submitSpinner = document.getElementById('submitAddTaskSpinner');
            const submitText = document.getElementById('submitAddTaskText');

            if (form && submitBtn) {
                form.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    submitBtn.disabled = true;
                    submitSpinner.classList.remove('hidden');
                    submitText.textContent = 'Creating...';

                    try {
                        const action = form.getAttribute('action');
                        const formData = new FormData(form);

                        const res = await fetch(action, {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json().catch(() => ({}));

                        if (res.ok) {
                            // success -> reload to show the new task
                            window.location.reload();
                            return;
                        }

                        // show validation errors if present
                        if (data && data.errors) {
                            // simple alert for now
                            const first = Object.values(data.errors)[0];
                            alert(first ? first[0] : 'Validation error');
                        } else if (data && data.message) {
                            alert(data.message);
                        } else {
                            alert('Failed to create task.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('An unexpected error occurred.');
                    } finally {
                        submitBtn.disabled = false;
                        submitSpinner.classList.add('hidden');
                        submitText.textContent = 'Create Task';
                    }
                });
            }
            // delegate badge remove
            badges.addEventListener('click', function (e) {
                const removeBtn = e.target.closest('button');
                if (!removeBtn) return;
                const span = removeBtn.closest('[data-badge-id]');
                if (!span) return;
                const id = span.getAttribute('data-badge-id');
                // remove input and badge
                const input = inputsContainer.querySelector('input[value="' + id + '"]');
                if (input) input.remove();
                span.remove();
                // remove active class from member button
                const memberBtn = memberList.querySelector('button[data-member-id="' + id + '"]');
                if (memberBtn) memberBtn.classList.remove('bg-primary/10');
                if (badges.children.length === 0) {
                    badges.innerHTML = '<span class="text-xs text-gray-400 italic" id="noSelectionText">No members selected</span>';
                }
            });
        }
    });
</script>
