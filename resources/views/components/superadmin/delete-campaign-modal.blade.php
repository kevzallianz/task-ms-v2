<div id="deleteCampaignModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
    <div class="bg-white w-full max-w-sm rounded-lg shadow-xl">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <div>
                <p class="text-xs text-gray-500">Confirm deletion</p>
                <h2 class="text-base font-semibold text-foreground" id="deleteCampaignName">Campaign</h2>
            </div>
            <button id="deleteCampaignCloseBtn" class="p-2 rounded hover:bg-gray-100" aria-label="Close">
                <x-heroicon-o-x-mark class="w-5 h-5 text-gray-600" />
            </button>
        </div>
        <div class="p-4 space-y-4">
            <input type="hidden" id="deleteCampaignUrl" value="">
            <input type="hidden" id="deleteCampaignId" value="">

            <div id="deleteCampaignMembersAlert" class="hidden flex items-start gap-3 p-3 bg-amber-50 rounded-lg border border-amber-300">
                <x-heroicon-o-user-group class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <p class="text-sm text-amber-800">
                    This campaign has <strong id="deleteCampaignMembersCount">0</strong> member(s). They will be unassigned from the campaign upon deletion.
                </p>
            </div>

            <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-200">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                <p class="text-sm text-red-700">
                    This action is <strong>permanent</strong>. The campaign and all associated tasks and projects will be removed.
                </p>
            </div>

            <p id="deleteCampaignFeedback" class="text-sm text-red-600 hidden"></p>

            <div class="flex justify-end gap-3">
                <button type="button" id="deleteCampaignCancelBtn" class="px-4 py-2 text-sm font-medium text-secondary border border-secondary/40 rounded-md hover:bg-secondary/10">
                    Cancel
                </button>
                <button type="button" id="deleteCampaignConfirmBtn" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Delete Campaign
                </button>
            </div>
        </div>
    </div>
</div>
