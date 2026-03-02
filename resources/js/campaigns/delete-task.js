$(document).ready(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const $modal = $('#deleteCampaignTaskModal');
    const $taskId = $('#deleteCampaignTaskId');
    const $campaignId = $('#deleteCampaignId');
    const $taskTitle = $('#deleteCampaignTaskTitle');
    const $confirmBtn = $('#confirmDeleteCampaignTaskBtn');
    const $confirmText = $('#confirmDeleteCampaignTaskBtnText');
    const $spinner = $('#confirmDeleteCampaignTaskBtnSpinner');

    function openModal(data) {
        $taskId.val(data.task_id || '');
        $campaignId.val(data.campaign_id || '');
        $taskTitle.text(data.task_title || 'this task');
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
        $taskId.val('');
        $campaignId.val('');
        $taskTitle.text('');
    }

    function setLoading(isLoading) {
        $confirmBtn.prop('disabled', isLoading);
        $confirmText.text(isLoading ? 'Deleting...' : 'Delete Task');
        isLoading ? $spinner.removeClass('hidden') : $spinner.addClass('hidden');
    }

    // Open modal on delete button click
    $(document).on('click', '.campaignTaskDeleteBtn', function () {
        const $btn = $(this);
        openModal({
            task_id: $btn.data('task-id'),
            campaign_id: $btn.data('campaign-id'),
            task_title: $btn.data('task-title'),
        });
    });

    // Close modal handlers
    $('#closeDeleteCampaignTaskModal, #cancelDeleteCampaignTaskBtn').on('click', closeModal);
    $modal.on('click', function (e) {
        if ($(e.target).is(this)) closeModal();
    });

    // Confirm delete
    $confirmBtn.on('click', function () {
        const taskId = $taskId.val();
        const campaignId = $campaignId.val();

        if (!taskId || !campaignId) {
            showToast && showToast('error', 'Invalid task or campaign');
            return;
        }

        setLoading(true);

        $.ajax({
            url: `/campaigns/${campaignId}/tasks/${taskId}`,
            method: 'POST',
            data: {
                _method: 'DELETE',
                _token: csrfToken,
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: function (response) {
                showToast && showToast('success', response.message || 'Task deleted successfully');
                closeModal();
                setTimeout(() => window.location.reload(), 400);
            },
            error: function (xhr) {
                const res = xhr.responseJSON || {};
                showToast && showToast('error', res.message || 'Failed to delete task');
                setLoading(false);
            },
        });
    });
});
