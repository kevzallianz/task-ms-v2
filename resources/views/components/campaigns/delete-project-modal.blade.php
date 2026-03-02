<!-- Delete Campaign Project Modal -->
<div id="deleteCampaignProjectModal" class="fixed flex inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600" />
                </div>
                <h2 class="text-lg font-semibold text-foreground">Delete Project</h2>
            </div>
            <button id="closeDeleteCampaignProjectModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <div class="p-6">
            <p class="text-sm text-gray-600 mb-2">Are you sure you want to delete this project?</p>
            <p class="text-sm font-semibold text-foreground" id="deleteCampaignProjectTitle"></p>
            <p class="text-xs text-red-600 font-medium mt-3">⚠️ Warning: This will also delete all tasks associated with this project.</p>
            <p class="text-xs text-gray-500 mt-2">This action cannot be undone. All project data, tasks, and their assignments will be permanently removed.</p>
            
            <input type="hidden" id="deleteCampaignProjectId" />
            <input type="hidden" id="deleteCampaignProjectCampaignId" />
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button" id="cancelDeleteCampaignProjectBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">
                Cancel
            </button>
            <button type="button" id="confirmDeleteCampaignProjectBtn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <span id="confirmDeleteCampaignProjectBtnText">Delete Project</span>
                <svg id="confirmDeleteCampaignProjectBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('deleteCampaignProjectModal');
        const openBtn = document.getElementById('deleteProjectBtn');
        const closeBtn = document.getElementById('closeDeleteCampaignProjectModal');
        const cancelBtn = document.getElementById('cancelDeleteCampaignProjectBtn');
        const confirmBtn = document.getElementById('confirmDeleteCampaignProjectBtn');
        const confirmSpinner = document.getElementById('confirmDeleteCampaignProjectBtnSpinner');
        const confirmText = document.getElementById('confirmDeleteCampaignProjectBtnText');

        const projectIdField = document.getElementById('deleteCampaignProjectId');
        const campaignIdField = document.getElementById('deleteCampaignProjectCampaignId');
        const projectTitleEl = document.getElementById('deleteCampaignProjectTitle');

        function openModal() {
            if (!modal) return;
            
            // Get project data from the page
            const projectId = document.querySelector('[data-project-id]')?.getAttribute('data-project-id');
            const projectTitle = document.querySelector('main p.text-lg.font-semibold.text-primary')?.textContent?.trim() || 'this project';
            const campaignId = window.location.pathname.split('/')[2]; // Extract from URL /campaigns/{id}/...
            
            if (projectIdField) projectIdField.value = projectId;
            if (campaignIdField) campaignIdField.value = campaignId;
            if (projectTitleEl) projectTitleEl.textContent = projectTitle;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Event listeners
        if (openBtn) openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        // Close on outside click
        modal?.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });

        // Confirm deletion
        confirmBtn?.addEventListener('click', async function () {
            const projectId = projectIdField?.value;
            const campaignId = campaignIdField?.value;

            if (!projectId || !campaignId) {
                alert('Project information is missing. Please refresh the page and try again.');
                return;
            }

            // Show loading state
            confirmBtn.disabled = true;
            confirmSpinner?.classList.remove('hidden');
            confirmText.textContent = 'Deleting...';

            try {
                const response = await fetch(`/campaigns/${campaignId}/projects/${projectId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Redirect to campaign page
                    window.location.href = '/campaigns';
                } else {
                    alert(data.message || 'An error occurred while deleting the project.');
                    confirmBtn.disabled = false;
                    confirmSpinner?.classList.add('hidden');
                    confirmText.textContent = 'Delete Project';
                }
            } catch (error) {
                console.error('Error deleting project:', error);
                alert('An error occurred. Please try again.');
                confirmBtn.disabled = false;
                confirmSpinner?.classList.add('hidden');
                confirmText.textContent = 'Delete Project';
            }
        });
    });
</script>
