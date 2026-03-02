@props(['campaigns'])

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
                <label for="editProjectTaskCampaignId" class="text-sm font-medium text-foreground">Campaign <span class="text-red-500">*</span></label>
                <select 
                    id="editProjectTaskCampaignId" 
                    name="campaign_id" 
                    required 
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 mt-1"
                >
                    <option value="">Select a campaign</option>
                    @foreach ($campaigns as $campaign)
                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-red-500 hidden" id="editProjectTaskCampaignIdError"></span>
            </div>

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
                    <option value="pending">Pending</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                </select>
                <span class="text-xs text-red-500 hidden" id="editProjectTaskStatusError"></span>
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
