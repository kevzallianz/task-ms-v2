/**
 * Edit task modal handler
 */

$(document).ready(function() {
    const projectId = parseInt($('#projectId').val()) || null;
    if (!projectId) return;

    const $modal = $('#editProjectTaskModal');
    const $form = $('#editProjectTaskForm');
    const $taskId = $('#editProjectTaskId');
    const $title = $('#editProjectTaskTitle');
    const $description = $('#editProjectTaskDescription');
    const $start = $('#editProjectTaskStartDate');
    const $target = $('#editProjectTaskTargetDate');
    const $status = $('#editProjectTaskStatus');
    const $campaign = $('#editProjectTaskCampaignId');
    const $titleLabel = $('#editProjectTaskTitle');

    const $submitBtn = $('#submitEditProjectTaskBtn');
    const $submitText = $('#submitEditProjectTaskBtnText');
    const $submitSpinner = $('#submitEditProjectTaskBtnSpinner');

    const errorMap = {
        title: $('#editProjectTaskTitleError'),
        assigned_campaign_id: $('#editProjectTaskCampaignIdError'),
        status: $('#editProjectTaskStatusError'),
        start_date: $('#editProjectTaskStartDateError'),
        target_date: $('#editProjectTaskTargetDateError'),
        description: $('#editProjectTaskDescriptionError'),
    };

    function openModal(data) {
        $taskId.val(data.id || '');
        $title.val(data.title || '');
        $description.val(data.description || '');
        $start.val(data.start_date || '');
        $target.val(data.target_date || '');
        $status.val(data.status || 'pending');
        $campaign.val(data.campaign_id || '');
        $titleLabel.text(data.title || 'Edit task');
        clearErrors();
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
        $form[0].reset();
        clearErrors();
    }

    function clearErrors() {
        Object.values(errorMap).forEach($el => $el.text('').addClass('hidden'));
    }

    function setLoading(isLoading) {
        $submitBtn.prop('disabled', isLoading);
        $submitText.text(isLoading ? 'Saving...' : 'Save Changes');
        isLoading ? $submitSpinner.removeClass('hidden') : $submitSpinner.addClass('hidden');
    }

    $(document).on('click', '.taskActionBtn[data-action="edit"]', function() {
        const $btn = $(this);
        openModal({
            id: $btn.data('task-id'),
            title: $btn.data('task-title'),
            description: $btn.data('task-description'),
            start_date: $btn.data('task-start-date'),
            target_date: $btn.data('task-target-date'),
            status: $btn.data('task-status'),
            campaign_id: $btn.data('task-campaign-id'),
        });
    });

    $('#closeEditProjectTaskModal, #cancelEditProjectTaskBtn').on('click', closeModal);
    $modal.on('click', function(e) {
        if ($(e.target).is(this)) closeModal();
    });

    $form.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const taskId = $taskId.val();
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
        const formData = new FormData(this);
        
        // Rename campaign_id to assigned_campaign_id for backend validation
        const campaignId = formData.get('campaign_id');
        formData.delete('campaign_id');
        if (campaignId) {
            formData.append('assigned_campaign_id', campaignId);
        }
        
        formData.append('_method', 'PUT');

        $.ajax({
            url: `/projects/${projectId}/tasks/${taskId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: handleSuccess,
            error: handleError,
            complete: () => setLoading(false),
        });
    });

    function handleSuccess(response) {
        showToast('success', response.message || 'Task updated successfully');
        closeModal();
        setTimeout(() => window.location.reload(), 500);
    }

    function handleError(xhr) {
        const res = xhr.responseJSON || {};
        const errors = res.errors || {};
        Object.entries(errors).forEach(([key, messages]) => {
            const $el = errorMap[key];
            if ($el) {
                $el.text(messages[0]).removeClass('hidden');
            }
        });
        showToast('error', res.message || 'Failed to update task');
    }
});
