<div id="addTaskModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-primary">Create Campaign Task</h2>
                <p class="text-sm text-gray-600">Add a new task for your campaign members</p>
            </div>
            <button type="button" id="closeAddTaskModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="addTaskForm" class="p-6 space-y-5" @if($currentUser) data-current-user-id="{{ $currentUser->id }}" @endif>
            @csrf

            {{-- Campaign (Hidden) --}}
            <input type="hidden" id="taskCampaignId" name="campaign_id" value="">

            <div class="grid grid-cols-2 gap-4">
                {{-- Title --}}
                <div class="col-span-2">
                    <label for="taskTitle" class="text-sm font-medium text-foreground">Title <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        id="taskTitle"
                        name="title"
                        placeholder="Enter task title"
                        maxlength="50"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20"
                        required />
                    <span class="text-xs text-red-500 hidden" id="taskTitleError"></span>
                </div>

                {{-- Start Date --}}
                <div>
                    <label for="taskStartDate" class="text-sm font-medium text-foreground">Start Date</label>
                    <input
                        type="date"
                        id="taskStartDate"
                        name="start_date"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="taskStartDateError"></span>
                </div>

                {{-- Target Date --}}
                <div>
                    <label for="taskTargetDate" class="text-sm font-medium text-foreground">Target Date</label>
                    <input
                        type="date"
                        id="taskTargetDate"
                        name="target_date"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="taskTargetDateError"></span>
                </div>

                {{-- Assigned Members --}}
                <div class="col-span-2 flex flex-col gap-2">
                    <div>
                        <label for="taskAssignedMembers" class="text-sm font-medium text-foreground">Assign Members</label>

                        {{-- Selected Members (Badges) --}}
                        <div id="selectedMemberBadges" class="flex flex-wrap gap-2 mb-2 min-h-8 p-2 border border-secondary/30 rounded-lg bg-white">
                            <span class="text-xs text-gray-400 italic" id="noSelectionText">No members selected</span>
                        </div>

                        {{-- Available Members (Clickable List) --}}
                        <div class="text-xs text-gray-600 mb-1">Click to add:</div>
                        <div id="taskAssignedMembers" class="space-y-3 max-h-48 overflow-y-auto border border-secondary/30 rounded-lg p-3 bg-gray-50">
                            <!-- Members will be populated here as clickable items -->
                        </div>
                    </div>
                    <span class="text-xs text-red-500 hidden" id="taskAssignedMemberIdsError"></span>
                </div>

                {{-- Status --}}
                <div class="col-span-2">
                    <label for="taskStatus" class="text-sm font-medium text-foreground">Status <span class="text-red-500">*</span></label>
                    <select
                        id="taskStatus"
                        name="status"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20">
                        <option value="planning" selected>Planning</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="on_hold">On Hold</option>
                        <option value="accomplished">Accomplished</option>
                    </select>
                    <span class="text-xs text-red-500 hidden" id="taskStatusError"></span>
                </div>

                {{-- Description --}}
                <div class="col-span-2">
                    <label for="taskDescription" class="text-sm font-medium text-foreground">Description (optional)</label>
                    <textarea
                        id="taskDescription"
                        name="description"
                        rows="3"
                        placeholder="Add task details and notes"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none"></textarea>
                    <span class="text-xs text-red-500 hidden" id="taskDescriptionError"></span>
                </div>
            </div>
        </form>

        {{-- Modal Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50">
            <button
                type="button"
                id="cancelAddTaskBtn"
                class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">
                Cancel
            </button>
            <button
                type="submit"
                form="addTaskForm"
                class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <x-heroicon-o-plus class="w-4 h-4" />
                Create Task
            </button>
        </div>
    </div>
</div>