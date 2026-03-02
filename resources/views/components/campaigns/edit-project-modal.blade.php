<!-- Edit Campaign Project Modal -->
<div id="editCampaignProjectModal" class="fixed flex inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 my-8">
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-primary">Edit Campaign Project</h2>
                <p class="text-sm text-gray-600">Update project details and settings.</p>
            </div>
            <button id="closeEditCampaignProjectModal" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editCampaignProjectForm" class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="editProjectId" name="project_id" value="">

            <div>
                <label for="editProjectTitle" class="text-sm font-medium text-foreground">Title <span class="text-red-500">*</span></label>
                <input 
                    id="editProjectTitle" 
                    type="text" 
                    name="title" 
                    required 
                    maxlength="150" 
                    placeholder="Project title" 
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 mt-1" 
                />
                <span class="text-xs text-red-500 hidden" id="editProjectTitleError"></span>
            </div>

            <div>
                <label for="editProjectDescription" class="text-sm font-medium text-foreground">Description</label>
                <textarea 
                    id="editProjectDescription" 
                    name="description" 
                    rows="4" 
                    placeholder="Optional project description" 
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none mt-1"
                ></textarea>
                <span class="text-xs text-red-500 hidden" id="editProjectDescriptionError"></span>
            </div>

            <div>
                <label for="editProjectStartDate" class="text-sm font-medium text-foreground">Start Date</label>
                <input 
                    id="editProjectStartDate" 
                    type="date" 
                    name="start_date" 
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 mt-1" 
                />
                <span class="text-xs text-red-500 hidden" id="editProjectStartDateError"></span>
            </div>

            <div>
                <label for="editProjectTargetDate" class="text-sm font-medium text-foreground">Target Date</label>
                <input 
                    id="editProjectTargetDate" 
                    type="date" 
                    name="target_date" 
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 mt-1" 
                />
                <span class="text-xs text-red-500 hidden" id="editProjectTargetDateError"></span>
            </div>

            <div>
                <label for="editProjectStatus" class="text-sm font-medium text-foreground">Status</label>
                <select 
                    id="editProjectStatus" 
                    name="status" 
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 mt-1"
                >
                    <option value="planning">Planning</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="accomplished">Accomplished</option>
                    <option value="on_hold">On Hold</option>
                </select>
                <span class="text-xs text-red-500 hidden" id="editProjectStatusError"></span>
            </div>
        </form>

        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button" id="cancelEditCampaignProjectBtn" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">Cancel</button>
            <button type="submit" form="editCampaignProjectForm" id="submitEditCampaignProjectBtn" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <span id="submitEditCampaignProjectBtnText">Update Project</span>
                <svg id="submitEditCampaignProjectBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editCampaignProjectModal');
        const openBtn = document.getElementById('editProjectBtn');
        const closeBtn = document.getElementById('closeEditCampaignProjectModal');
        const cancelBtn = document.getElementById('cancelEditCampaignProjectBtn');
        const form = document.getElementById('editCampaignProjectForm');
        const submitBtn = document.getElementById('submitEditCampaignProjectBtn');
        const submitSpinner = document.getElementById('submitEditCampaignProjectBtnSpinner');
        const submitText = document.getElementById('submitEditCampaignProjectBtnText');

        const projectIdField = document.getElementById('editProjectId');
        const titleField = document.getElementById('editProjectTitle');
        const descriptionField = document.getElementById('editProjectDescription');
        const startDateField = document.getElementById('editProjectStartDate');
        const targetDateField = document.getElementById('editProjectTargetDate');
        const statusField = document.getElementById('editProjectStatus');

        function openModal() {
            if (!modal) return;
            
            // Get project data from the page
            const projectId = document.querySelector('[data-project-id]')?.getAttribute('data-project-id');
            const projectTitle = document.querySelector('main p.text-lg.font-semibold.text-primary')?.textContent?.trim() || '';
            const projectDescription = document.querySelector('main p.text-sm.text-gray-600.leading-relaxed')?.textContent?.trim() || '';
            
            // Get dates from the page
            const startDateText = document.querySelectorAll('main span.text-sm.text-gray-600')[0]?.textContent?.trim();
            const targetDateText = document.querySelectorAll('main span.text-sm.text-gray-600')[1]?.textContent?.trim();
            
            // Convert "M d, Y" to "Y-m-d" format for date input
            const formatDateForInput = (dateText) => {
                if (!dateText || dateText === 'Not set') return '';
                try {
                    const date = new Date(dateText);
                    return date.toISOString().split('T')[0];
                } catch (e) {
                    return '';
                }
            };
            
            // Get status badge element
            const statusBadge = document.querySelector('main span.inline-flex.items-center.gap-1.px-3.py-1');
            let projectStatus = 'planning';
            if (statusBadge) {
                const statusClasses = statusBadge.className;
                if (statusClasses.includes('bg-blue-100')) projectStatus = 'planning';
                else if (statusClasses.includes('bg-yellow-100')) projectStatus = 'ongoing';
                else if (statusClasses.includes('bg-green-100')) projectStatus = 'accomplished';
                else if (statusClasses.includes('bg-red-100')) projectStatus = 'on_hold';
            }
            
            // Populate form fields
            if (projectIdField) projectIdField.value = projectId;
            if (titleField) titleField.value = projectTitle;
            if (descriptionField) descriptionField.value = projectDescription === 'No description provided.' ? '' : projectDescription;
            if (startDateField) startDateField.value = formatDateForInput(startDateText);
            if (targetDateField) targetDateField.value = formatDateForInput(targetDateText);
            if (statusField) statusField.value = projectStatus;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            
            // Clear error messages
            document.querySelectorAll('#editCampaignProjectForm .text-red-500').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
        }

        // Event listeners
        if (openBtn) openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        // Close on outside click
        modal?.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });

        // Form submission
        form?.addEventListener('submit', async function (e) {
            e.preventDefault();

            const projectId = projectIdField.value;
            const campaignId = window.location.pathname.split('/')[2]; // Extract from URL

            // Show loading state
            submitBtn.disabled = true;
            submitSpinner?.classList.remove('hidden');
            submitText.textContent = 'Updating...';

            // Clear previous errors
            document.querySelectorAll('#editCampaignProjectForm .text-red-500').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });

            try {
                const formData = new FormData(form);
                const response = await fetch(`/campaigns/${campaignId}/projects/${projectId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Success - reload the page
                    window.location.reload();
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorEl = document.getElementById(`editProject${field.charAt(0).toUpperCase() + field.slice(1)}Error`);
                            if (errorEl) {
                                errorEl.textContent = data.errors[field][0];
                                errorEl.classList.remove('hidden');
                            }
                        });
                    } else {
                        alert(data.message || 'An error occurred while updating the project.');
                    }
                }
            } catch (error) {
                console.error('Error updating project:', error);
                alert('An error occurred. Please try again.');
            } finally {
                // Reset loading state
                submitBtn.disabled = false;
                submitSpinner?.classList.add('hidden');
                submitText.textContent = 'Update Project';
            }
        });
    });
</script>
