<!-- Delete Project Task Modal -->
<div id="deleteProjectTaskModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600" />
                </div>
                <h2 class="text-lg font-semibold text-foreground">Delete Task</h2>
            </div>
            <button id="closeDeleteProjectTaskModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <div class="p-6">
            <p class="text-sm text-gray-600 mb-2">Are you sure you want to delete this task?</p>
            <p class="text-sm font-semibold text-foreground" id="deleteProjectTaskTitle"></p>
            <p class="text-xs text-gray-500 mt-3">This action cannot be undone. All task data and assignments will be permanently removed.</p>
            
            <input type="hidden" id="deleteProjectTaskId" />
            <input type="hidden" id="deleteProjectTaskCampaignId" />
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button" id="cancelDeleteProjectTaskBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">
                Cancel
            </button>
            <button type="button" id="confirmDeleteProjectTaskBtn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <span id="confirmDeleteProjectTaskBtnText">Delete Task</span>
                <svg id="confirmDeleteProjectTaskBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
