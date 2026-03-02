@props(['project', 'campaigns'])

<!-- Add Task Modal -->
<div id="addTaskModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 my-8">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-primary">Create Task</h2>
                <p class="text-sm text-gray-600">Add a new task and assign it to a campaign</p>
            </div>
            <button id="closeTaskModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addTaskForm" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <!-- Title -->
                <div class="col-span-2">
                    <label for="taskTitle" class="text-sm font-medium text-foreground">Title</label>
                    <input type="text" name="title" id="taskTitle" maxlength="50" required
                           class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="taskTitleError"></span>
                </div>

                <!-- Assigned Campaign -->
                <div>
                    <label for="taskCampaign" class="text-sm font-medium text-foreground">Assign to Campaign</label>
                    <select name="assigned_campaign_id" id="taskCampaign" required
                            class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20">
                        <option value="">Choose a campaign...</option>
                        @foreach ($campaigns as $campaign)
                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                    <span class="text-xs text-red-500 hidden" id="taskCampaignError"></span>
                </div>

                <!-- Status -->
                <div>
                    <label for="taskStatus" class="text-sm font-medium text-foreground">Status</label>
                    <select name="status" id="taskStatus" required
                            class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20">
                        <option value="pending" selected>Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                    <span class="text-xs text-red-500 hidden" id="taskStatusError"></span>
                </div>

                <!-- Start Date -->
                <div>
                    <label for="taskStartDate" class="text-sm font-medium text-foreground">Start Date</label>
                    <input type="date" name="start_date" id="taskStartDate"
                           class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="taskStartDateError"></span>
                </div>

                <!-- Target Date -->
                <div>
                    <label for="taskTargetDate" class="text-sm font-medium text-foreground">Target Date</label>
                    <input type="date" name="target_date" id="taskTargetDate"
                           class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="taskTargetDateError"></span>
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label for="taskDescription" class="text-sm font-medium text-foreground">Description (optional)</label>
                    <textarea name="description" id="taskDescription" rows="3"
                              class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20"
                              placeholder="Add details, acceptance criteria, or notes"></textarea>
                    <span class="text-xs text-red-500 hidden" id="taskDescriptionError"></span>
                </div>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button"
                    id="cancelTaskBtn"
                    class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">
                Cancel
            </button>
            <button type="submit"
                    form="addTaskForm"
                    id="submitTaskBtn"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <span id="submitTaskBtnText">Create Task</span>
                <svg id="submitTaskBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v4m0 0v4m0-4h4m-4 0H8m4-4v4" />
                </svg>
            </button>
        </div>
    </div>
</div>
