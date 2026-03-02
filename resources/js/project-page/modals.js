// Project Page Modals Handler

document.addEventListener('DOMContentLoaded', function () {
    const campaignId = window.location.pathname.split('/')[2];
    const projectId = document.querySelector('[data-project-id]')?.getAttribute('data-project-id');

    // ============= Edit Task Modal =============
    const editModal = document.getElementById('editProjectTaskModal');
    const editForm = document.getElementById('editProjectTaskForm');
    const editSelectedMemberIds = new Set();

    // Handle member selection for edit task modal
    function updateEditMemberUI() {
        const badgesContainer = document.getElementById('editProjectTaskSelectedMemberBadges');
        const noSelectionText = document.getElementById('editProjectTaskNoSelectionText');
        
        badgesContainer.innerHTML = '';
        
        if (editSelectedMemberIds.size === 0) {
            noSelectionText.classList.remove('hidden');
            badgesContainer.appendChild(noSelectionText);
        } else {
            editSelectedMemberIds.forEach(memberId => {
                const memberItem = document.querySelector(`.editProjectTaskMemberItem[data-member-id="${memberId}"]`);
                if (memberItem) {
                    const memberName = memberItem.getAttribute('data-member-name');
                    const badge = document.createElement('span');
                    badge.className = 'inline-flex items-center gap-1 px-2 py-1 bg-primary/10 text-primary rounded-full text-xs border border-primary/20';
                    badge.innerHTML = `
                        ${memberName}
                        <button type="button" class="hover:bg-primary/20 rounded-full p-0.5" data-remove-member="${memberId}">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    `;
                    badgesContainer.appendChild(badge);
                }
            });
        }

        // Update checkmarks
        document.querySelectorAll('.editProjectTaskMemberItem').forEach(item => {
            const memberId = item.getAttribute('data-member-id');
            const checkmark = item.querySelector('.editProjectTaskMemberCheckmark');
            if (editSelectedMemberIds.has(memberId)) {
                checkmark?.classList.remove('hidden');
                item.classList.add('bg-primary/5');
            } else {
                checkmark?.classList.add('hidden');
                item.classList.remove('bg-primary/5');
            }
        });
    }

    // Toggle member selection
    document.addEventListener('click', function (e) {
        const memberItem = e.target.closest('.editProjectTaskMemberItem');
        if (memberItem) {
            const memberId = memberItem.getAttribute('data-member-id');
            if (editSelectedMemberIds.has(memberId)) {
                editSelectedMemberIds.delete(memberId);
            } else {
                editSelectedMemberIds.add(memberId);
            }
            updateEditMemberUI();
        }

        // Handle badge remove button
        const removeBtn = e.target.closest('[data-remove-member]');
        if (removeBtn) {
            const memberId = removeBtn.getAttribute('data-remove-member');
            editSelectedMemberIds.delete(memberId);
            updateEditMemberUI();
        }
    });
    
    document.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.campaignTaskEditBtn');
        if (editBtn) {
            const taskId = editBtn.getAttribute('data-task-id');
            const title = editBtn.getAttribute('data-task-title');
            const description = editBtn.getAttribute('data-task-description') || '';
            const startDate = editBtn.getAttribute('data-task-start-date') || '';
            const targetDate = editBtn.getAttribute('data-task-target-date') || '';
            const status = editBtn.getAttribute('data-task-status');
            const assignedMembersStr = editBtn.getAttribute('data-assigned-members') || '[]';
            
            let assignedMembers = [];
            try {
                assignedMembers = JSON.parse(assignedMembersStr);
            } catch (e) {
                assignedMembers = [];
            }

            document.getElementById('editProjectTaskId').value = taskId;
            document.getElementById('editProjectTaskTitle').value = title;
            document.getElementById('editProjectTaskDescription').value = description;
            document.getElementById('editProjectTaskStartDate').value = startDate;
            document.getElementById('editProjectTaskTargetDate').value = targetDate;
            document.getElementById('editProjectTaskStatus').value = status;

            // Set assigned members
            editSelectedMemberIds.clear();
            assignedMembers.forEach(memberId => {
                editSelectedMemberIds.add(memberId.toString());
            });
            updateEditMemberUI();

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
                    status: formData.get('status'),
                    assigned_member_ids: Array.from(editSelectedMemberIds)
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
            text.textContent = 'Post Remark';
        }
    });
});
