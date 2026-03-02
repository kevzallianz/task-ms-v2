/**
 * Delete task handler with confirmation modal
 */

$(document).ready(function() {
    const projectId = parseInt($('#projectId').val()) || null;
    if (!projectId) return;

    const $modal = $('#confirmationModal');
    const $title = $('#confirmationTitle');
    const $message = $('#confirmationMessage');
    const $confirmBtn = $('#confirmActionBtn');
    const $confirmText = $('#confirmActionBtnText');
    const $confirmSpinner = $('#confirmActionBtnSpinner');

    let pendingDelete = null;

    function openConfirmation(taskId, taskTitle) {
        pendingDelete = { taskId, taskTitle };
        $title.text('Delete Task');
        $message.text(`Are you sure you want to delete "${taskTitle}"? This action cannot be undone.`);
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
        pendingDelete = null;
        setLoading(false);
    }

    function setLoading(isLoading) {
        $confirmBtn.prop('disabled', isLoading);
        $confirmText.text(isLoading ? 'Deleting...' : 'Delete');
        isLoading ? $confirmSpinner.removeClass('hidden') : $confirmSpinner.addClass('hidden');
    }

    // Open confirmation from delete button
    $(document).on('click', '.taskActionBtn[data-action="delete"]', function() {
        const taskId = $(this).data('task-id');
        const taskTitle = $(this).data('task-title');
        openConfirmation(taskId, taskTitle);
    });

    // Close handlers
    $('#closeConfirmationModal, #cancelConfirmationBtn').on('click', closeModal);
    $modal.on('click', function(e) {
        if ($(e.target).is(this)) closeModal();
    });

    // Confirm delete
    $confirmBtn.on('click', function() {
        if (!pendingDelete) return;

        setLoading(true);
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

        $.ajax({
            url: `/projects/${projectId}/tasks/${pendingDelete.taskId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: function(response) {
                showToast('success', response.message || 'Task deleted successfully');
                closeModal();
                setTimeout(() => window.location.reload(), 500);
            },
            error: function(xhr) {
                const res = xhr.responseJSON || {};
                showToast('error', res.message || 'Failed to delete task');
                setLoading(false);
            }
        });
    });
});
