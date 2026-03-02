<div id="memberAccessModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-4 py-3 border-b border-secondary/20">
            <div>
                <p class="text-xs text-gray-500">Update access level</p>
                <h4 class="text-base font-semibold text-foreground" id="memberAccessName">Member</h4>
            </div>
            <button type="button" id="memberAccessClose" class="p-2 rounded hover:bg-secondary/10" aria-label="Close access level modal">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <form id="memberAccessForm" class="px-4 py-4 space-y-4">
            <input type="hidden" id="memberAccessUpdateUrl" value="">
            <input type="hidden" id="memberAccessMemberId" value="">

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700">Access level</legend>
                <label class="flex items-center gap-3 rounded-md border border-secondary/30 px-3 py-2 hover:border-primary/50 cursor-pointer">
                    <input type="radio" name="access_level" value="viewer" class="h-4 w-4 text-primary border-secondary/40 focus:ring-primary/30">
                    <div>
                        <p class="text-sm font-semibold text-foreground">View</p>
                        <p class="text-xs text-gray-500">Can view tasks and members.</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 rounded-md border border-secondary/30 px-3 py-2 hover:border-primary/50 cursor-pointer">
                    <input type="radio" name="access_level" value="editor" class="h-4 w-4 text-primary border-secondary/40 focus:ring-primary/30">
                    <div>
                        <p class="text-sm font-semibold text-foreground">Editor</p>
                        <p class="text-xs text-gray-500">Can create and update tasks.</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 rounded-md border border-secondary/30 px-3 py-2 hover:border-primary/50 cursor-pointer">
                    <input type="radio" name="access_level" value="all" class="h-4 w-4 text-primary border-secondary/40 focus:ring-primary/30">
                    <div>
                        <p class="text-sm font-semibold text-foreground">All</p>
                        <p class="text-xs text-gray-500">Full control including member permissions.</p>
                    </div>
                </label>
            </fieldset>

            <p id="memberAccessFeedback" class="text-sm text-red-600 hidden"></p>

            <div class="flex justify-end gap-3">
                <button type="button" id="memberAccessCancel" class="px-4 py-2 text-sm font-medium text-secondary border border-secondary/40 rounded-md hover:bg-secondary/10">
                    Cancel
                </button>
                <button type="submit" id="memberAccessSave" class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-md hover:bg-primary/90">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
