<!-- Update Project Status Modal -->
<div id="updateProjectStatusModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 my-8">
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-primary">Update Project Status</h2>
                <p class="text-sm text-gray-600" id="updateProjectName">Project</p>
            </div>
            <button id="closeUpdateProjectStatusModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="updateProjectStatusForm" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span id="updateProjectCurrentStatus">Current status: planning</span>
                    <span class="inline-flex items-center gap-1 text-[11px] text-gray-500">
                        <x-heroicon-o-sparkles class="w-3 h-3" /> Choose new status
                    </span>
                </div>

                <div class="space-y-2" id="updateProjectStatusRadios">
                    <label class="flex items-center gap-3 p-3 border border-secondary/30 rounded-lg hover:border-primary/70 transition cursor-pointer">
                        <input type="radio" name="status" value="planning" class="text-primary focus:ring-primary cursor-pointer" />
                        <div class="text-sm">
                            <div class="font-medium text-foreground">Planning</div>
                            <p class="text-xs text-gray-500">Project is in planning phase.</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-secondary/30 rounded-lg hover:border-primary/70 transition cursor-pointer">
                        <input type="radio" name="status" value="in_progress" class="text-primary focus:ring-primary cursor-pointer" />
                        <div class="text-sm">
                            <div class="font-medium text-foreground">In Progress</div>
                            <p class="text-xs text-gray-500">Project is currently being executed.</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-secondary/30 rounded-lg hover:border-primary/70 transition cursor-pointer">
                        <input type="radio" name="status" value="completed" class="text-primary focus:ring-primary cursor-pointer" />
                        <div class="text-sm">
                            <div class="font-medium text-foreground">Completed</div>
                            <p class="text-xs text-gray-500">Project has been finished.</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-secondary/30 rounded-lg hover:border-primary/70 transition cursor-pointer">
                        <input type="radio" name="status" value="on_hold" class="text-primary focus:ring-primary cursor-pointer" />
                        <div class="text-sm">
                            <div class="font-medium text-foreground">On Hold</div>
                            <p class="text-xs text-gray-500">Project is temporarily paused.</p>
                        </div>
                    </label>
                </div>
                <span class="text-xs text-red-500 hidden" id="updateProjectStatusError"></span>
            </div>
        </form>

        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button" id="cancelUpdateProjectStatusBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">Cancel</button>
            <button type="submit" form="updateProjectStatusForm" id="submitUpdateProjectStatusBtn" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <span id="submitUpdateProjectStatusBtnText">Update Status</span>
                <svg id="submitUpdateProjectStatusBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>
</div>
