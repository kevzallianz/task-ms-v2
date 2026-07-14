<div id="userPasswordResetModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm" role="dialog" aria-modal="true" aria-labelledby="userPasswordResetTitle">
        <div class="flex items-center justify-between px-4 py-3 border-b border-secondary/20">
            <div>
                <p class="text-xs text-gray-500">Password reset</p>
                <h4 class="text-base font-semibold text-foreground" id="userPasswordResetTitle">Send reset link</h4>
            </div>
            <button type="button" id="userPasswordResetClose" class="p-2 rounded hover:bg-secondary/10" aria-label="Close password reset modal">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <div class="px-4 py-4 space-y-4">
            <input type="hidden" id="userPasswordResetUrl" value="">

            <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                <x-heroicon-o-envelope class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <p class="text-sm text-amber-800">
                    Send a secure password reset link to <strong id="userPasswordResetName">this user</strong>
                    at <span id="userPasswordResetEmail"></span>?
                </p>
            </div>

            <p id="userPasswordResetFeedback" class="text-sm hidden" role="status"></p>

            <div class="flex justify-end gap-3">
                <button type="button" id="userPasswordResetCancel" class="px-4 py-2 text-sm font-medium text-secondary border border-secondary/40 rounded-md hover:bg-secondary/10">
                    Cancel
                </button>
                <button type="button" id="userPasswordResetConfirm" class="px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-md hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Send Reset Link
                </button>
            </div>
        </div>
    </div>
</div>
