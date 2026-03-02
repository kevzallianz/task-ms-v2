<div id="userRoleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-4 py-3 border-b border-secondary/20">
            <div>
                <p class="text-xs text-gray-500">Update role</p>
                <h4 class="text-base font-semibold text-foreground" id="userRoleName">User</h4>
            </div>
            <button type="button" id="userRoleClose" class="p-2 rounded hover:bg-secondary/10" aria-label="Close role modal">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <form id="userRoleForm" class="px-4 py-4 space-y-4">
            <input type="hidden" id="userRoleUpdateUrl" value="">
            <input type="hidden" id="userRoleUserId" value="">

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700">Select role</legend>
                <label class="flex items-center gap-3 rounded-md border border-secondary/30 px-3 py-2 hover:border-primary/50 cursor-pointer">
                    <input type="radio" name="role" value="user" class="h-4 w-4 text-primary border-secondary/40 focus:ring-primary/30">
                    <div>
                        <p class="text-sm font-semibold text-foreground">User</p>
                        <p class="text-xs text-gray-500">Standard access.</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 rounded-md border border-secondary/30 px-3 py-2 hover:border-primary/50 cursor-pointer">
                    <input type="radio" name="role" value="admin" class="h-4 w-4 text-primary border-secondary/40 focus:ring-primary/30">
                    <div>
                        <p class="text-sm font-semibold text-foreground">Admin</p>
                        <p class="text-xs text-gray-500">Manage projects and campaigns.</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 rounded-md border border-secondary/30 px-3 py-2 hover:border-primary/50 cursor-pointer">
                    <input type="radio" name="role" value="superadmin" class="h-4 w-4 text-primary border-secondary/40 focus:ring-primary/30">
                    <div>
                        <p class="text-sm font-semibold text-foreground">Superadmin</p>
                        <p class="text-xs text-gray-500">Full platform control.</p>
                    </div>
                </label>
            </fieldset>

            <p id="userRoleFeedback" class="text-sm text-red-600 hidden"></p>

            <div class="flex justify-end gap-3">
                <button type="button" id="userRoleCancel" class="px-4 py-2 text-sm font-medium text-secondary border border-secondary/40 rounded-md hover:bg-secondary/10">
                    Cancel
                </button>
                <button type="submit" id="userRoleSave" class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-md hover:bg-primary/90">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
