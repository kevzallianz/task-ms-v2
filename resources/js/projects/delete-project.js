/**
 * Delete project handler with confirmation modal
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

    function openConfirmation(projectId, projectName) {
        pendingDelete = { projectId, projectName };
        $title.text('Delete Project');
        $message.text(`Are you sure you want to delete "${projectName}"? This will permanently delete all tasks, remarks, activities, and contributors. This action cannot be undone.`);
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
    $('#deleteProjectBtn').on('click', function() {
        const projectName = $(this).data('project-name');
        openConfirmation(projectId, projectName);
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
            url: `/projects/${pendingDelete.projectId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: function(response) {
                showToast('success', response.message || 'Project deleted successfully');
                closeModal();
                // Redirect to projects list after successful deletion
                setTimeout(() => window.location.href = '/projects', 500);
            },
            error: function(xhr) {
                const res = xhr.responseJSON || {};
                showToast('error', res.message || 'Failed to delete project');
                setLoading(false);
            }
        });
    });
});
