@props(['project', 'campaigns'])

<!-- Add Contributor Modal -->
<div id="addContributorModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 my-8">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-primary">Add Contributor</h2>
                <p class="text-sm text-gray-600">Add a campaign as contributor</p>
            </div>
            <button id="closeContributorModal" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addContributorForm" class="p-6 space-y-5">
            @csrf

            <!-- Campaign Selection -->
            <div>
                <label class="text-sm font-medium text-foreground">Select Campaign</label>
                <select name="campaign_id" 
                        id="campaignSelect"
                        required
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20">
                    <option value="">Choose a campaign...</option>
                    @foreach ($campaigns as $campaign)
                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-red-500 hidden" id="campaignError"></span>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button" 
                    id="cancelContributorBtn"
                    class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">
                Cancel
            </button>
            <button type="submit" 
                    form="addContributorForm"
                    id="submitContributorBtn"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <span id="submitBtnText">Add Contributor</span>
                <svg id="submitBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6m0-6h6m0 0h6"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
