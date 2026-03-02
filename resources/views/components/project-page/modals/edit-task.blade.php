@props(['campaign'])

<!-- Edit Project Task Modal -->
<div id="editProjectTaskModal" class="fixed flex inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 my-8">
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-primary">Edit Task</h2>
                <p class="text-sm text-gray-600">Update task details and assignments.</p>
            </div>
            <button id="closeEditProjectTaskModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editProjectTaskForm" class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="editProjectTaskId" name="task_id" value="">

            <div>
                <label for="editProjectTaskTitle" class="text-sm font-medium text-foreground">Title <span class="text-red-500">*</span></label>
                <input 
                    id="editProjectTaskTitle" 
                    type="text" 
                    name="title" 
                    required 
                    maxlength="100" 
                    placeholder="Task title" 
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 mt-1" 
                />
                <span class="text-xs text-red-500 hidden" id="editProjectTaskTitleError"></span>
            </div>

            <div>
                <label for="editProjectTaskDescription" class="text-sm font-medium text-foreground">Description</label>
                <textarea 
                    id="editProjectTaskDescription" 
                    name="description" 
                    rows="3" 
                    placeholder="Optional task description" 
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none mt-1"
                ></textarea>
                <span class="text-xs text-red-500 hidden" id="editProjectTaskDescriptionError"></span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="editProjectTaskStartDate" class="text-sm font-medium text-foreground">Start Date</label>
                    <input 
                        id="editProjectTaskStartDate" 
                        type="date" 
                        name="start_date" 
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 mt-1" 
                    />
                    <span class="text-xs text-red-500 hidden" id="editProjectTaskStartDateError"></span>
                </div>

                <div>
                    <label for="editProjectTaskTargetDate" class="text-sm font-medium text-foreground">Target Date</label>
                    <input 
                        id="editProjectTaskTargetDate" 
                        type="date" 
                        name="target_date" 
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 mt-1" 
                    />
                    <span class="text-xs text-red-500 hidden" id="editProjectTaskTargetDateError"></span>
                </div>
            </div>

            <div>
                <label for="editProjectTaskStatus" class="text-sm font-medium text-foreground">Status <span class="text-red-500">*</span></label>
                <select 
                    id="editProjectTaskStatus" 
                    name="status" 
                    required 
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 mt-1"
                >
                    <option value="planning">Planning</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="accomplished">Accomplished</option>
                    <option value="on_hold">On Hold</option>
                </select>
                <span class="text-xs text-red-500 hidden" id="editProjectTaskStatusError"></span>
            </div>

            {{-- Assigned Members --}}
            <div class="flex flex-col gap-2">
                <div>
                    <label for="editProjectTaskAssignedMembers" class="text-sm font-medium text-foreground">Assign Members</label>

                    {{-- Selected Members (Badges) --}}
                    <div id="editProjectTaskSelectedMemberBadges" class="flex flex-wrap gap-2 mb-2 min-h-8 p-2 border border-secondary/30 rounded-lg bg-white">
                        <span class="text-xs text-gray-400 italic" id="editProjectTaskNoSelectionText">No members selected</span>
                    </div>

                    {{-- Available Members (Clickable List) --}}
                    <div class="text-xs text-gray-600 mb-1">Click to add/remove:</div>
                    <div id="editProjectTaskAssignedMembers" class="space-y-3 max-h-48 overflow-y-auto border border-secondary/30 rounded-lg p-3 bg-gray-50">
                        @foreach ($campaign->members as $member)
                        <div class="editProjectTaskMemberItem flex items-center justify-between p-2 rounded-lg hover:bg-white cursor-pointer transition" 
                             data-member-id="{{ $member->pivot->id }}" 
                             data-member-name="{{ $member->name }}">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold text-primary">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-foreground">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                </div>
                            </div>
                            <div class="editProjectTaskMemberCheckmark w-5 h-5 rounded border-2 border-secondary/30 hidden">
                                <svg class="w-full h-full text-primary" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <span class="text-xs text-red-500 hidden" id="editProjectTaskAssignedMemberIdsError"></span>
            </div>
        </form>

        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button" id="cancelEditProjectTaskBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">Cancel</button>
            <button type="submit" form="editProjectTaskForm" id="submitEditProjectTaskBtn" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <span id="submitEditProjectTaskBtnText">Update Task</span>
                <svg id="submitEditProjectTaskBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
