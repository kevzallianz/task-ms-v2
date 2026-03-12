<div id="userDeleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm">
        <div class="flex items-center justify-between px-4 py-3 border-b border-secondary/20">
            <div>
                <p class="text-xs text-gray-500">Confirm deletion</p>
                <h4 class="text-base font-semibold text-foreground" id="userDeleteName">User</h4>
            </div>
            <button type="button" id="userDeleteClose" class="p-2 rounded hover:bg-secondary/10" aria-label="Close delete modal">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <div class="px-4 py-4 space-y-4">
            <input type="hidden" id="userDeleteUrl" value="">
            <input type="hidden" id="userDeleteUserId" value="">

            <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-200">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                <p class="text-sm text-red-700">
                    This action is <strong>permanent</strong>. The user and all associated data will be removed and cannot be recovered.
                </p>
            </div>

            <p id="userDeleteFeedback" class="text-sm text-red-600 hidden"></p>

            <div class="flex justify-end gap-3">
                <button type="button" id="userDeleteCancel" class="px-4 py-2 text-sm font-medium text-secondary border border-secondary/40 rounded-md hover:bg-secondary/10">
                    Cancel
                </button>
                <button type="button" id="userDeleteConfirm" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Delete User
                </button>
            </div>
        </div>
    </div>
</div>
