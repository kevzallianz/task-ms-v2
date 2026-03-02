// Project Task Modals Handler

document.addEventListener('DOMContentLoaded', function () {
    const campaignId = window.location.pathname.split('/')[2];
    const projectId = document.querySelector('[data-project-id]')?.getAttribute('data-project-id');

    // ============= Edit Task Modal =============
    const editModal = document.getElementById('editProjectTaskModal');
    const editForm = document.getElementById('editProjectTaskForm');
    
    document.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.campaignTaskEditBtn');
        if (editBtn) {
            const taskId = editBtn.getAttribute('data-task-id');
            const title = editBtn.getAttribute('data-task-title');
            const description = editBtn.getAttribute('data-task-description') || '';
            const startDate = editBtn.getAttribute('data-task-start-date') || '';
            const targetDate = editBtn.getAttribute('data-task-target-date') || '';
            const status = editBtn.getAttribute('data-task-status');
            const assignedMembers = JSON.parse(editBtn.getAttribute('data-assigned-members') || '[]');

            document.getElementById('editProjectTaskId').value = taskId;
            document.getElementById('editProjectTaskTitle').value = title;
            document.getElementById('editProjectTaskDescription').value = description;
            document.getElementById('editProjectTaskStartDate').value = startDate;
            document.getElementById('editProjectTaskTargetDate').value = targetDate;
            document.getElementById('editProjectTaskStatus').value = status;

            editModal?.classList.remove('hidden');
            editModal?.classList.add('flex');
        }
    });

    function closeEditModal() {
        editModal?.classList.add('hidden');
        editModal?.classList.remove('flex');
        document.querySelectorAll('#editProjectTaskForm .text-red-500').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }

    document.getElementById('closeEditProjectTaskModal')?.addEventListener('click', closeEditModal);
    document.getElementById('cancelEditProjectTaskBtn')?.addEventListener('click', closeEditModal);
    editModal?.addEventListener('click', (e) => { if (e.target === editModal) closeEditModal(); });

    editForm?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const taskId = document.getElementById('editProjectTaskId').value;
        const submitBtn = document.getElementById('submitEditProjectTaskBtn');
        const spinner = document.getElementById('submitEditProjectTaskBtnSpinner');
        const text = document.getElementById('submitEditProjectTaskBtnText');

        submitBtn.disabled = true;
        spinner?.classList.remove('hidden');
        text.textContent = 'Updating...';

        document.querySelectorAll('#editProjectTaskForm .text-red-500').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });

        try {
            const formData = new FormData(editForm);
            const response = await fetch(`/campaigns/${campaignId}/tasks/${taskId}`, {
                method: 'PUT',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    title: formData.get('title'),
                    description: formData.get('description'),
                    start_date: formData.get('start_date'),
                    target_date: formData.get('target_date'),
                    status: formData.get('status')
                })
            });

            const data = await response.json();

            if (response.ok && data.message) {
                window.location.reload();
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorEl = document.getElementById(`editProjectTask${field.charAt(0).toUpperCase() + field.slice(1)}Error`);
                        if (errorEl) {
                            errorEl.textContent = data.errors[field][0];
                            errorEl.classList.remove('hidden');
                        }
                    });
                }
                alert(data.message || 'Failed to update task');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            spinner?.classList.add('hidden');
            text.textContent = 'Update Task';
        }
    });

    // ============= Update Task Status Modal =============
    const statusModal = document.getElementById('updateProjectTaskStatusModal');
    const statusForm = document.getElementById('updateProjectTaskStatusForm');

    document.addEventListener('click', function (e) {
        const statusBtn = e.target.closest('.campaignTaskStatusBtn');
        if (statusBtn) {
            const taskId = statusBtn.getAttribute('data-task-id');
            const title = statusBtn.getAttribute('data-task-title');
            const currentStatus = statusBtn.getAttribute('data-task-status');

            document.getElementById('updateProjectTaskStatusId').value = taskId;
            document.getElementById('updateProjectTaskStatusCampaignId').value = campaignId;
            document.getElementById('updateProjectTaskStatusName').textContent = title;
            document.getElementById('updateProjectTaskCurrentStatus').textContent = `Current status: ${currentStatus.replace('_', ' ')}`;

            document.querySelectorAll('#updateProjectTaskStatusForm input[name="status"]').forEach(radio => {
                radio.checked = radio.value === currentStatus;
            });

            statusModal?.classList.remove('hidden');
            statusModal?.classList.add('flex');
        }
    });

    function closeStatusModal() {
        statusModal?.classList.add('hidden');
        statusModal?.classList.remove('flex');
    }

    document.getElementById('closeUpdateProjectTaskStatusModal')?.addEventListener('click', closeStatusModal);
    document.getElementById('cancelUpdateProjectTaskStatusBtn')?.addEventListener('click', closeStatusModal);
    statusModal?.addEventListener('click', (e) => { if (e.target === statusModal) closeStatusModal(); });

    statusForm?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const taskId = document.getElementById('updateProjectTaskStatusId').value;
        const submitBtn = document.getElementById('submitUpdateProjectTaskStatusBtn');
        const spinner = document.getElementById('submitUpdateProjectTaskStatusBtnSpinner');
        const text = document.getElementById('submitUpdateProjectTaskStatusBtnText');

        submitBtn.disabled = true;
        spinner?.classList.remove('hidden');
        text.textContent = 'Updating...';

        try {
            const formData = new FormData(statusForm);
            const response = await fetch(`/campaigns/${campaignId}/tasks/${taskId}/status`, {
                method: 'PUT',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: formData.get('status')
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to update status');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            spinner?.classList.add('hidden');
            text.textContent = 'Update Status';
        }
    });

    // ============= Delete Task Modal =============
    const deleteModal = document.getElementById('deleteProjectTaskModal');

    document.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.campaignTaskDeleteBtn');
        if (deleteBtn) {
            const taskId = deleteBtn.getAttribute('data-task-id');
            const title = deleteBtn.getAttribute('data-task-title');

            document.getElementById('deleteProjectTaskId').value = taskId;
            document.getElementById('deleteProjectTaskCampaignId').value = campaignId;
            document.getElementById('deleteProjectTaskTitle').textContent = title;

            deleteModal?.classList.remove('hidden');
            deleteModal?.classList.add('flex');
        }
    });

    function closeDeleteModal() {
        deleteModal?.classList.add('hidden');
        deleteModal?.classList.remove('flex');
    }

    document.getElementById('closeDeleteProjectTaskModal')?.addEventListener('click', closeDeleteModal);
    document.getElementById('cancelDeleteProjectTaskBtn')?.addEventListener('click', closeDeleteModal);
    deleteModal?.addEventListener('click', (e) => { if (e.target === deleteModal) closeDeleteModal(); });

    document.getElementById('confirmDeleteProjectTaskBtn')?.addEventListener('click', async function () {
        const taskId = document.getElementById('deleteProjectTaskId').value;
        const confirmBtn = this;
        const spinner = document.getElementById('confirmDeleteProjectTaskBtnSpinner');
        const text = document.getElementById('confirmDeleteProjectTaskBtnText');

        confirmBtn.disabled = true;
        spinner?.classList.remove('hidden');
        text.textContent = 'Deleting...';

        try {
            const response = await fetch(`/campaigns/${campaignId}/tasks/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to delete task');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            confirmBtn.disabled = false;
            spinner?.classList.add('hidden');
            text.textContent = 'Delete Task';
        }
    });

    // ============= Task Remarks Modal =============
    const remarksModal = document.getElementById('projectTaskRemarksModal');
    const remarksForm = document.getElementById('addProjectTaskRemarkForm');
    const remarkInput = document.getElementById('projectTaskRemarkInput');
    const charCount = document.getElementById('projectTaskRemarkCharCount');

    remarkInput?.addEventListener('input', function () {
        if (charCount) charCount.textContent = this.value.length;
    });

    document.addEventListener('click', function (e) {
        const remarksBtn = e.target.closest('.taskActionBtn[data-action="remarks"]');
        if (remarksBtn) {
            const taskId = remarksBtn.getAttribute('data-task-id');
            const title = remarksBtn.getAttribute('data-task-title');

            document.getElementById('projectTaskRemarksTaskId').value = taskId;
            document.getElementById('projectTaskRemarksCampaignId').value = campaignId;
            document.getElementById('projectTaskRemarksTaskTitle').textContent = title;

            loadRemarks(taskId);

            remarksModal?.classList.remove('hidden');
            remarksModal?.classList.add('flex');
        }
    });

    async function loadRemarks(taskId) {
        const remarksList = document.getElementById('projectTaskRemarksList');
        remarksList.innerHTML = '<div class="flex items-center justify-center py-8 text-gray-400"><svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="ml-2 text-sm">Loading remarks...</span></div>';

        try {
            const response = await fetch(`/campaigns/${campaignId}/tasks/${taskId}/remarks`);
            const data = await response.json();

            if (data.remarks && data.remarks.length > 0) {
                remarksList.innerHTML = data.remarks.map(remark => `
                    <div class="flex gap-3 p-3 bg-gray-50 rounded-lg border border-secondary/20">
                        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold text-primary shrink-0">
                            ${remark.user.name.charAt(0)}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-foreground">${remark.user.name}</span>
                                <span class="text-xs text-gray-500">${new Date(remark.created_at).toLocaleString()}</span>
                            </div>
                            <p class="text-sm text-gray-600">${remark.remarks}</p>
                        </div>
                    </div>
                `).join('');
            } else {
                remarksList.innerHTML = '<div class="flex flex-col items-center justify-center py-8 text-center"><svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg><p class="text-sm text-gray-500">No remarks yet. Be the first to add one!</p></div>';
            }
        } catch (error) {
            console.error('Error loading remarks:', error);
            remarksList.innerHTML = '<div class="text-center text-red-500 text-sm py-4">Failed to load remarks</div>';
        }
    }

    function closeRemarksModal() {
        remarksModal?.classList.add('hidden');
        remarksModal?.classList.remove('flex');
        remarkInput.value = '';
        if (charCount) charCount.textContent = '0';
    }

    document.getElementById('closeProjectTaskRemarksModal')?.addEventListener('click', closeRemarksModal);
    remarksModal?.addEventListener('click', (e) => { if (e.target === remarksModal) closeRemarksModal(); });

    remarksForm?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const taskId = document.getElementById('projectTaskRemarksTaskId').value;
        const submitBtn = document.getElementById('submitProjectTaskRemarkBtn');
        const spinner = document.getElementById('submitProjectTaskRemarkBtnSpinner');
        const text = document.getElementById('submitProjectTaskRemarkBtnText');

        if (!remarkInput.value.trim()) return;

        submitBtn.disabled = true;
        spinner?.classList.remove('hidden');
        text.textContent = 'Adding...';

        try {
            const formData = new FormData(remarksForm);
            const response = await fetch(`/campaigns/${campaignId}/tasks/${taskId}/remarks`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await response.json();

            if (response.ok || response.status === 201) {
                remarkInput.value = '';
                if (charCount) charCount.textContent = '0';
                loadRemarks(taskId);
            } else {
                alert(data.message || 'Failed to add remark');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            spinner?.classList.add('hidden');
            text.textContent = 'Add Remark';
        }
    });
});
