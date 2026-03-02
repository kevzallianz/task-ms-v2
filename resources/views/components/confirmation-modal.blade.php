<!-- Reusable Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 my-8">
        <div class="flex items-center justify-between px-6 py-4">
            <h2 class="text-lg font-semibold text-foreground" id="confirmationTitle">Confirm Action</h2>
            <button id="closeConfirmationModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="px-6 py-4 space-y-4">
            <div class="flex items-start gap-4">
                <div class="shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600" />
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-600" id="confirmationMessage">Are you sure you want to proceed?</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50">
            <button type="button" id="cancelConfirmationBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">
                Cancel
            </button>
            <button type="button" id="confirmActionBtn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <span id="confirmActionBtnText">Confirm</span>
                <svg id="confirmActionBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>
</div>
