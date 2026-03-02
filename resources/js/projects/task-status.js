/**
 * Task status update modal handler
 */

$(document).ready(function() {
    const projectId = parseInt($('#projectId').val()) || null;
    if (!projectId) return;

    const $modal = $('#updateTaskStatusModal');
    const $form = $('#updateTaskStatusForm');
    const $statusRadios = $('#updateStatusRadios input[name="status"]');
    const $statusError = $('#updateStatusError');
    const $taskIdInput = $('#updateTaskId');
    const $taskTitle = $('#updateTaskTitle');
    const $taskCurrentStatus = $('#updateTaskCurrentStatus');
    const $submitBtn = $('#submitUpdateStatusBtn');
    const $submitText = $('#submitUpdateStatusBtnText');
    const $submitSpinner = $('#submitUpdateStatusBtnSpinner');

    function openModal(taskId, title, currentStatus) {
        $taskIdInput.val(taskId);
        $taskTitle.text(title || 'Update status');
        const statusToSet = currentStatus || 'pending';
        $taskCurrentStatus.text(`Current status: ${statusToSet}`);
        $statusRadios.prop('checked', false);
        $statusRadios.filter(`[value="${statusToSet}"]`).prop('checked', true);
        $modal.removeClass('hidden').addClass('flex');
        clearErrors();
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
        $form[0].reset();
        clearErrors();
    }

    function clearErrors() {
        $statusError.text('').addClass('hidden');
    }

    function setLoading(isLoading) {
        $submitBtn.prop('disabled', isLoading);
        $submitText.text(isLoading ? 'Updating...' : 'Update Status');
        isLoading ? $submitSpinner.removeClass('hidden') : $submitSpinner.addClass('hidden');
    }

    // open from task buttons
    $(document).on('click', '.taskActionBtn[data-action="status"]', function() {
        const taskId = $(this).data('task-id');
        const taskTitle = $(this).data('task-title');
        const taskStatus = $(this).data('task-status');
        openModal(taskId, taskTitle, taskStatus);
    });

    // close handlers
    $('#closeUpdateStatusModal, #cancelUpdateStatusBtn').on('click', closeModal);
    $modal.on('click', function(e) {
        if ($(e.target).is(this)) closeModal();
    });

    // submit
    $form.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const taskId = $taskIdInput.val();
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
        const formData = new FormData(this);

        $.ajax({
            url: `/projects/${projectId}/tasks/${taskId}/status`,
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
        showToast('success', response.message || 'Task status updated');
        closeModal();
        setTimeout(() => window.location.reload(), 500);
    }

    function handleError(xhr) {
        const res = xhr.responseJSON || {};
        if (res.errors && res.errors.status) {
            $statusError.text(res.errors.status[0]).removeClass('hidden');
        }
        showToast('error', res.message || 'Failed to update status');
    }
});
