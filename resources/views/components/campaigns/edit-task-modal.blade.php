<!-- Edit Campaign Task Modal -->
<div id="editCampaignTaskModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 my-8">
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-primary">Edit Task</h2>
                <p class="text-sm text-gray-600" id="editCampaignTaskTitle">Update task details</p>
            </div>
            <button id="closeEditCampaignTaskModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <form id="editCampaignTaskForm" class="p-6 space-y-5 w-full">
            @csrf
            @method('PUT')
            <input type="hidden" id="editCampaignTaskId" name="task_id" />
            <input type="hidden" id="editCampaignId" name="campaign_id" />

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label for="editCampaignTaskName" class="text-sm font-medium text-foreground">Title</label>
                    <input type="text" id="editCampaignTaskName" name="title" maxlength="100" required
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="editCampaignTaskNameError"></span>
                </div>

                <div>
                    <label for="editCampaignTaskStartDate" class="text-sm font-medium text-foreground">Start Date</label>
                    <input type="date" id="editCampaignTaskStartDate" name="start_date"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="editCampaignTaskStartDateError"></span>
                </div>

                <div>
                    <label for="editCampaignTaskTargetDate" class="text-sm font-medium text-foreground">Target Date</label>
                    <input type="date" id="editCampaignTaskTargetDate" name="target_date"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="editCampaignTaskTargetDateError"></span>
                </div>

                <div class="col-span-2">
                    <label for="editCampaignTaskAccomplishedDate" class="text-sm font-medium text-foreground">Accomplished Date</label>
                    <input type="date" id="editCampaignTaskAccomplishedDate" name="completed_at"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="editCampaignTaskAccomplishedDateError"></span>
                </div>

                <div class="col-span-2 w-full">
                    <label for="editCampaignTaskStatus" class="text-sm font-medium text-foreground">Status</label>
                    <select id="editCampaignTaskStatus" name="status" required
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20">
                        <option value="planning">Planning</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="on_hold">On Hold</option>
                        <option value="accomplished">Accomplished</option>
                    </select>
                    <span class="text-xs text-red-500 hidden" id="editCampaignTaskStatusError"></span>
                </div>

                <div class="col-span-2">
                    <label for="editCampaignTaskDescription" class="text-sm font-medium text-foreground">Description</label>
                    <textarea id="editCampaignTaskDescription" name="description" rows="3"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20"
                        placeholder="Add details, acceptance criteria, or notes"></textarea>
                    <span class="text-xs text-red-500 hidden" id="editCampaignTaskDescriptionError"></span>
                </div>

                {{-- Assigned Members --}}
                <div class="col-span-2 flex flex-col gap-2">
                    <div>
                        <label for="editCampaignTaskAssignedMembers" class="text-sm font-medium text-foreground">Assign Members</label>

                        {{-- Selected Members (Badges) --}}
                        <div id="editSelectedMemberBadges" class="flex flex-wrap gap-2 mb-2 min-h-8 p-2 border border-secondary/30 rounded-lg bg-white">
                            <span class="text-xs text-gray-400 italic" id="editNoSelectionText">No members selected</span>
                        </div>

                        {{-- Available Members (Clickable List) --}}
                        <div class="text-xs text-gray-600 mb-1">Click to add:</div>
                        <div id="editCampaignTaskAssignedMembers" class="space-y-3 max-h-48 overflow-y-auto border border-secondary/30 rounded-lg p-3 bg-gray-50">
                            <!-- Members will be populated here as clickable items -->
                        </div>
                    </div>
                    <span class="text-xs text-red-500 hidden" id="editCampaignTaskAssignedMembersError"></span>
                </div>
            </div>
        </form>

        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button" id="cancelEditCampaignTaskBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">Cancel</button>
            <button type="submit" form="editCampaignTaskForm" id="submitEditCampaignTaskBtn" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <span id="submitEditCampaignTaskBtnText">Save Changes</span>
                <svg id="submitEditCampaignTaskBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v4m0 0v4m0-4h4m-4 0H8m4-4v4" />
                </svg>
            </button>
        </div>
    </div>
</div>
