<div id="editCampaignModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
    <div class="bg-white w-full max-w-lg rounded-lg shadow-xl">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <div>
                <p class="text-xs text-gray-500">Edit campaign</p>
                <h2 class="text-base font-semibold text-foreground" id="editCampaignTitle">Campaign</h2>
            </div>
            <button id="editCampaignCloseBtn" class="p-2 rounded hover:bg-gray-100" aria-label="Close">
                <x-heroicon-o-x-mark class="w-5 h-5 text-gray-600" />
            </button>
        </div>
        <form id="editCampaignForm" class="p-4">
            <input type="hidden" id="editCampaignUpdateUrl" value="">
            <input type="hidden" id="editCampaignId" value="">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="editCampaignName" class="text-xs font-medium text-gray-700 mb-1 block">Name</label>
                    <input type="text" id="editCampaignName" name="name" maxlength="100" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" placeholder="Campaign name" />
                    <span class="text-xs text-red-500 hidden" id="editCampaignNameError"></span>
                </div>
                <div>
                    <label for="editCampaignDescription" class="text-xs font-medium text-gray-700 mb-1 block">Description</label>
                    <textarea id="editCampaignDescription" name="description" rows="3" class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" placeholder="Optional description"></textarea>
                    <span class="text-xs text-red-500 hidden" id="editCampaignDescriptionError"></span>
                </div>
            </div>
            <p id="editCampaignFeedback" class="text-sm text-red-600 hidden mt-2"></p>
            <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" id="editCampaignCancelBtn" class="px-4 py-2 text-sm font-medium text-secondary border border-secondary/50 bg-white rounded hover:bg-white/80 transition">Cancel</button>
                <button type="submit" id="editCampaignSaveBtn" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded hover:bg-primary/90 transition">Save Changes</button>
            </div>
        </form>
    </div>
</div>
