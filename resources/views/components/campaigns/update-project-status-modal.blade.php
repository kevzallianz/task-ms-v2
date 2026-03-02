<!-- Update Campaign Project Status Modal -->
<div id="updateCampaignProjectStatusModal" class="fixed flex inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 my-8">
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-primary">Update Project Status</h2>
                <p class="text-sm text-gray-600" id="updateCampaignProjectName">Project</p>
            </div>
            <button id="closeUpdateCampaignProjectStatusModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="updateCampaignProjectStatusForm" class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="campaignProjectIdField" name="project_id" value="">

            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span id="updateCampaignProjectCurrentStatus">Current status: planning</span>
                    <span class="inline-flex items-center gap-1 text-[11px] text-gray-500">
                        <x-heroicon-o-sparkles class="w-3 h-3" /> Choose new status
                    </span>
                </div>

                <div class="space-y-2" id="updateCampaignProjectStatusRadios">
                    <label class="flex items-center gap-3 p-3 border border-secondary/30 rounded-lg hover:border-primary/70 transition cursor-pointer">
                        <input type="radio" name="status" value="planning" class="text-primary focus:ring-primary cursor-pointer" />
                        <div class="text-sm">
                            <div class="font-medium text-foreground">Planning</div>
                            <p class="text-xs text-gray-500">Project is in planning phase.</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-secondary/30 rounded-lg hover:border-primary/70 transition cursor-pointer">
                        <input type="radio" name="status" value="ongoing" class="text-primary focus:ring-primary cursor-pointer" />
                        <div class="text-sm">
                            <div class="font-medium text-foreground">Ongoing</div>
                            <p class="text-xs text-gray-500">Project is currently being executed.</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-secondary/30 rounded-lg hover:border-primary/70 transition cursor-pointer">
                        <input type="radio" name="status" value="accomplished" class="text-primary focus:ring-primary cursor-pointer" />
                        <div class="text-sm">
                            <div class="font-medium text-foreground">Accomplished</div>
                            <p class="text-xs text-gray-500">Project has been finished.</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-secondary/30 rounded-lg hover:border-primary/70 transition cursor-pointer">
                        <input type="radio" name="status" value="on_hold" class="text-primary focus:ring-primary cursor-pointer" />
                        <div class="text-sm">
                            <div class="font-medium text-foreground">On Hold</div>
                            <p class="text-xs text-gray-500">Project is temporarily paused.</p>
                        </div>
                    </label>
                </div>
                <span class="text-xs text-red-500 hidden" id="updateCampaignProjectStatusError"></span>
            </div>
        </form>

        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button" id="cancelUpdateCampaignProjectStatusBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">Cancel</button>
            <button type="submit" form="updateCampaignProjectStatusForm" id="submitUpdateCampaignProjectStatusBtn" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <span id="submitUpdateCampaignProjectStatusBtnText">Update Status</span>
                <svg id="submitUpdateCampaignProjectStatusBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('updateCampaignProjectStatusModal');
        const openBtn = document.getElementById('updateProjectStatusBtn');
        const closeBtn = document.getElementById('closeUpdateCampaignProjectStatusModal');
        const cancelBtn = document.getElementById('cancelUpdateCampaignProjectStatusBtn');
        const form = document.getElementById('updateCampaignProjectStatusForm');
        const submitBtn = document.getElementById('submitUpdateCampaignProjectStatusBtn');
        const submitSpinner = document.getElementById('submitUpdateCampaignProjectStatusBtnSpinner');
        const submitText = document.getElementById('submitUpdateCampaignProjectStatusBtnText');

        const projectIdField = document.getElementById('campaignProjectIdField');
        const projectNameEl = document.getElementById('updateCampaignProjectName');
        const currentStatusEl = document.getElementById('updateCampaignProjectCurrentStatus');

        function openModal() {
            if (!modal) return;
            
            // Get project data from the page
            const projectId = document.querySelector('[data-project-id]')?.getAttribute('data-project-id');
            const projectTitle = document.querySelector('main p.text-lg.font-semibold.text-primary')?.textContent?.trim() || 'Project';
            const projectStatus = document.querySelector('main span.inline-flex.items-center')?.textContent?.trim().toLowerCase().replace(/\s+/g, '_') || 'planning';
            
            if (projectIdField) projectIdField.value = projectId;
            if (projectNameEl) projectNameEl.textContent = projectTitle;
            if (currentStatusEl) currentStatusEl.textContent = 'Current status: ' + (projectStatus.replace(/_/g, ' '));

            // Pre-select current status radio
            const radios = form.querySelectorAll('input[name="status"]');
            radios.forEach(radio => {
                if (radio.value === projectStatus) {
                    radio.checked = true;
                }
            });

            modal.classList.remove('hidden');
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            if (form) form.reset();
        }

        if (openBtn) openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
        });

        // Ajax form submit
        if (form && submitBtn) {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                submitBtn.disabled = true;
                submitSpinner.classList.remove('hidden');
                submitText.textContent = 'Updating...';

                try {
                    const campaignId = window.location.pathname.split('/')[2];
                    const projectId = projectIdField.value;
                    const formData = new FormData(form);

                    const res = await fetch(`/campaigns/${campaignId}/projects/${projectId}/status`, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok) {
                        window.location.reload();
                        return;
                    }

                    if (data && data.errors) {
                        const first = Object.values(data.errors)[0];
                        alert(first ? first[0] : 'Validation error');
                    } else if (data && data.message) {
                        alert(data.message);
                    } else {
                        alert('Failed to update status.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('An unexpected error occurred.');
                } finally {
                    submitBtn.disabled = false;
                    submitSpinner.classList.add('hidden');
                    submitText.textContent = 'Update Status';
                }
            });
        }
    });
</script>
