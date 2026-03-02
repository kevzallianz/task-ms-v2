$(document).ready(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const $modal = $('#editCampaignTaskModal');
    const $form = $('#editCampaignTaskForm');
    const $taskId = $('#editCampaignTaskId');
    const $campaignId = $('#editCampaignId');
    const $title = $('#editCampaignTaskName');
    const $description = $('#editCampaignTaskDescription');
    const $start = $('#editCampaignTaskStartDate');
    const $target = $('#editCampaignTaskTargetDate');
    const $status = $('#editCampaignTaskStatus');
    const $titleLabel = $('#editCampaignTaskTitle');
    const $membersContainer = $('#editCampaignTaskAssignedMembers');
    const $selectedBadges = $('#editSelectedMemberBadges');
    const $noSelectionText = $('#editNoSelectionText');

    const $submitBtn = $('#submitEditCampaignTaskBtn');
    const $submitText = $('#submitEditCampaignTaskBtnText');
    const $spinner = $('#submitEditCampaignTaskBtnSpinner');
    const $accomplished = $('#editCampaignTaskAccomplishedDate');

    let selectedMembers = [];

    const errorMap = {
        title: $('#editCampaignTaskNameError'),
        start_date: $('#editCampaignTaskStartDateError'),
        target_date: $('#editCampaignTaskTargetDateError'),
        completed_at: $('#editCampaignTaskAccomplishedDateError'),
        status: $('#editCampaignTaskStatusError'),
        description: $('#editCampaignTaskDescriptionError'),
        assigned_member_ids: $('#editCampaignTaskAssignedMembersError'),
    };

    function openModal(data) {
        $taskId.val(data.id || '');
        $campaignId.val(data.campaign_id || '');
        $title.val(data.title || '');
        $description.val(data.description || '');
        $start.val(data.start_date || '');
        $target.val(data.target_date || '');
        
        // Format completed_at date if it exists
        if (data.completed_at) {
            const accomplishedDate = new Date(data.completed_at);
            const formattedDate = accomplishedDate.toISOString().split('T')[0];
            $accomplished.val(formattedDate);
        } else {
            $accomplished.val('');
        }
        
        $status.val(data.status || 'planning');
        $titleLabel.text(data.title || 'Edit task');
        clearErrors();
        selectedMembers = [];
        populateMembersCheckboxes(data.campaign_id);
        
        // Load existing assigned members
        if (data.assigned_members && data.assigned_members.length > 0) {
            const $membersPanel = $(`[data-campaign-panel-members="${data.campaign_id}"]`);
            data.assigned_members.forEach(memberId => {
                const $memberElement = $membersPanel.find(`[data-campaign-member-id="${memberId}"]`);
                if ($memberElement.length) {
                    const memberName = $memberElement.find('.font-medium.text-foreground').text().trim();
                    addMemberSelection(memberId, memberName);
                }
            });
        }
        
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
        $form[0].reset();
        clearErrors();
        selectedMembers = [];
    }

    function clearErrors() {
        Object.values(errorMap).forEach($el => $el.text('').addClass('hidden'));
    }

    function setLoading(isLoading) {
        $submitBtn.prop('disabled', isLoading);
        $submitText.text(isLoading ? 'Saving...' : 'Save Changes');
        isLoading ? $spinner.removeClass('hidden') : $spinner.addClass('hidden');
    }

    function populateMembersCheckboxes(campaignId) {
        $membersContainer.html('');
        selectedMembers = [];
        updateSelectedBadges();

        const $membersPanel = $(`[data-campaign-panel-members="${campaignId}"]`);
        const $memberElements = $membersPanel.find('[data-campaign-member-id]');

        if ($memberElements.length === 0) {
            $membersContainer.html('<p class="text-xs text-gray-600 py-2">No members found</p>');
            return;
        }

        $memberElements.each(function() {
            const $this = $(this);
            const memberName = $this.find('.font-medium.text-foreground').text().trim();
            const campaignMemberId = $this.data('campaign-member-id');

            if (campaignMemberId) {
                const memberHtml = `
                    <div class="flex items-center gap-2 p-2 rounded cursor-pointer hover:bg-white transition edit-member-item"
                         data-member-id="${campaignMemberId}"
                         data-member-name="${escapeHtml(memberName)}">
                        <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold text-primary shrink-0">
                            ${memberName.charAt(0).toUpperCase()}
                        </div>
                        <span class="text-sm text-foreground">${escapeHtml(memberName)}</span>
                    </div>
                `;
                $membersContainer.append(memberHtml);
            }
        });

        // Add click handlers
        $('.edit-member-item').on('click', function() {
            const memberId = $(this).data('member-id');
            const memberName = $(this).data('member-name');
            addMemberSelection(memberId, memberName);
        });
    }

    function addMemberSelection(memberId, memberName) {
        if (selectedMembers.some(m => m.id === memberId)) {
            return;
        }
        selectedMembers.push({ id: memberId, name: memberName });
        updateSelectedBadges();
    }

    function removeMemberSelection(memberId) {
        selectedMembers = selectedMembers.filter(m => m.id !== memberId);
        updateSelectedBadges();
    }

    function updateSelectedBadges() {
        $selectedBadges.html('');
        if (selectedMembers.length === 0) {
            $selectedBadges.html('<span class="text-xs text-gray-400 italic" id="editNoSelectionText">No members selected</span>');
            return;
        }
        selectedMembers.forEach(member => {
            const badge = `
                <div class="flex items-center gap-1 px-2 py-1 bg-primary/10 rounded-full text-xs border border-primary/20">
                    <span class="text-foreground">${escapeHtml(member.name)}</span>
                    <button type="button" class="edit-remove-member ml-1 text-primary/60 hover:text-primary transition"
                            data-member-id="${member.id}" title="Remove">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            $selectedBadges.append(badge);
        });

        $(document).off('click', '.edit-remove-member').on('click', '.edit-remove-member', function(e) {
            e.preventDefault();
            const memberId = $(this).data('member-id');
            removeMemberSelection(memberId);
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    $(document).on('click', '.campaignTaskEditBtn', function () {
        const $btn = $(this);
        const assignedMembersJson = $btn.data('assigned-members') || '[]';
        const assignedMembers = typeof assignedMembersJson === 'string' ? JSON.parse(assignedMembersJson) : assignedMembersJson;
        
        openModal({
            id: $btn.data('task-id'),
            campaign_id: $btn.data('campaign-id'),
            title: $btn.data('task-title'),
            description: $btn.data('task-description'),
            start_date: $btn.data('task-start-date'),
            target_date: $btn.data('task-target-date'),
            status: $btn.data('task-status'),
            completed_at: $btn.data('completed-at'),
            assigned_members: assignedMembers,
        });
    });

    $('#closeEditCampaignTaskModal, #cancelEditCampaignTaskBtn').on('click', closeModal);
    $modal.on('click', function (e) {
        if ($(e.target).is(this)) closeModal();
    });

    $form.on('submit', function (e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const taskId = $taskId.val();
        const campaignId = $campaignId.val();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        // Add selected member IDs
        selectedMembers.forEach((member, index) => {
            formData.append(`assigned_member_ids[${index}]`, member.id);
        });

        $.ajax({
            url: `/campaigns/${campaignId}/tasks/${taskId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: function (response) {
                showToast && showToast('success', response.message || 'Task updated successfully');
                closeModal();
                setTimeout(() => window.location.reload(), 400);
            },
            error: function (xhr) {
                const res = xhr.responseJSON || {};
                const errors = res.errors || {};
                Object.entries(errors).forEach(([key, messages]) => {
                    const $el = errorMap[key];
                    if ($el) {
                        $el.text(messages[0]).removeClass('hidden');
                    }
                });
                showToast && showToast('error', res.message || 'Failed to update task');
            },
            complete: function () {
                setLoading(false);
            },
        });
    });
});
