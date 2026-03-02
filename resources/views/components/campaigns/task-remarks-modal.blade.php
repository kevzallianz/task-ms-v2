<!-- Campaign Task Remarks Modal -->
<div id="campaignTaskRemarksModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 my-8 flex flex-col max-h-[80vh]">
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4 shrink-0">
            <div class="flex items-center gap-3">
                <x-heroicon-o-chat-bubble-left class="w-5 h-5 text-primary" />
                <div>
                    <h2 class="text-lg font-semibold text-primary">Task Remarks</h2>
                    <p class="text-sm text-gray-600" id="campaignRemarksTaskTitle">Viewing remarks</p>
                </div>
            </div>
            <button id="closeCampaignRemarksModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Remarks List --}}
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3 bg-gray-50">
            <input type="hidden" id="campaignRemarksTaskId" />
            
            {{-- Loading state --}}
            <div id="campaignRemarksLoading" class="flex items-center justify-center py-12">
                <div class="text-center">
                    <svg class="w-8 h-8 animate-spin text-primary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v4m0 0v4m0-4h4m-4 0H8m4-4v4" />
                    </svg>
                    <p class="text-sm text-gray-500">Loading remarks...</p>
                </div>
            </div>

            {{-- Remarks container --}}
            <div id="campaignRemarksContainer" class="hidden space-y-3">
            </div>

            {{-- Empty state --}}
            <div id="campaignRemarksEmpty" class="hidden flex flex-col items-center justify-center py-12 text-center">
                <x-heroicon-o-chat-bubble-left-right class="w-12 h-12 text-gray-300 mb-3" />
                <p class="text-sm text-gray-500 font-medium">No remarks yet</p>
                <p class="text-xs text-gray-400">Be the first to add a remark below</p>
            </div>
        </div>

        {{-- Add Remark Section --}}
        <div class="border-t border-secondary/20 bg-white px-6 py-4 space-y-3 shrink-0">
            <form id="campaignAddRemarkForm" class="space-y-3">
                @csrf
                <div>
                    <label for="campaignRemarkText" class="text-xs font-medium text-gray-700 mb-2 block">Add Your Remark</label>
                    <textarea id="campaignRemarkText" name="remark" rows="2" maxlength="500" required
                              placeholder="Share your feedback or notes..." 
                              class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none"></textarea>
                    <span class="text-xs text-red-500 hidden" id="campaignRemarkError"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">
                        <span id="campaignRemarkCharCount">0</span>/500 characters
                    </span>
                    <button type="submit" id="campaignSubmitRemarkBtn" class="px-3 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                        <x-heroicon-o-paper-airplane class="w-4 h-4" />
                        <span id="campaignSubmitRemarkBtnText">Post Remark</span>
                        <svg id="campaignSubmitRemarkBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v4m0 0v4m0-4h4m-4 0H8m4-4v4" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="border-t border-secondary/20 px-6 py-3 bg-gray-50 flex items-center justify-end">
            <button type="button" id="campaignCloseRemarksBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">Close</button>
        </div>
    </div>
</div>
