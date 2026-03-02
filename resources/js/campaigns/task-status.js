$(document).ready(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const $modal = $('#campaignTaskStatusModal');
    const $form = $('#campaignTaskStatusForm');
    const $radios = $('#campaignStatusRadios input[name="status"]');
    const $error = $('#campaignStatusError');
    const $taskIdInput = $('#campaignTaskId');
    const $campaignIdInput = $('#campaignId');
    const $title = $('#campaignTaskStatusTitle');
    const $current = $('#campaignTaskCurrentStatus');
    const $submitBtn = $('#submitCampaignTaskStatusBtn');
    const $submitText = $('#submitCampaignTaskStatusBtnText');
    const $spinner = $('#submitCampaignTaskStatusBtnSpinner');

    function openModal({ campaignId, taskId, title, status }) {
        $taskIdInput.val(taskId);
        $campaignIdInput.val(campaignId);
        $title.text(title || 'Task');
        $current.text(`Current status: ${status}`);
        $radios.prop('checked', false);
        $radios.filter(`[value="${status}"]`).prop('checked', true);
        $modal.removeClass('hidden').addClass('flex');
        clearErrors();
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
        $form[0].reset();
        clearErrors();
    }

    function clearErrors() {
        $error.text('').addClass('hidden');
    }

    function setLoading(isLoading) {
        $submitBtn.prop('disabled', isLoading);
        $submitText.text(isLoading ? 'Updating...' : 'Update Status');
        isLoading ? $spinner.removeClass('hidden') : $spinner.addClass('hidden');
    }

    $(document).on('click', '.campaignTaskStatusBtn', function () {
        const $btn = $(this);
        openModal({
            campaignId: $btn.data('campaign-id'),
            taskId: $btn.data('task-id'),
            title: $btn.data('task-title'),
            status: $btn.data('task-status') || 'planning',
        });
    });

    $('#closeCampaignTaskStatusModal, #cancelCampaignTaskStatusBtn').on('click', closeModal);
    $modal.on('click', function (e) {
        if ($(e.target).is(this)) closeModal();
    });
-
    $form.on('submit', function (e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const taskId = $taskIdInput.val();
        const campaignId = $campaignIdInput.val();
        const newStatus = $radios.filter(':checked').val();

        $.ajax({
            url: `/campaigns/${campaignId}/tasks/${taskId}/status`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            data: { status: newStatus },
            success: function (response) {
                window.location.reload();
                showToast && showToast('success', response.message || 'Task status updated');
                closeModal();
            },
            error: function (xhr) {
                const res = xhr.responseJSON || {};
                if (res.errors && res.errors.status) {
                    $error.text(res.errors.status[0]).removeClass('hidden');
                }
                showToast && showToast('error', res.message || res.error || 'Failed to update status');
            },
            complete: function () {
                setLoading(false);
            },
        });
    });
});
