/**
 * Edit project modal handler
 */

$(document).ready(function() {
    const projectId = parseInt($('[data-project-id]').attr('data-project-id')) || null;
    if (!projectId) return;

    const $modal = $('#editProjectModal');
    const $form = $('#editProjectForm');
    const $nameInput = $('#editProjectName');
    const $descriptionInput = $('#editProjectDescription');
    const $startDateInput = $('#editStartDate');
    const $targetDateInput = $('#editTargetDate');
    const $statusSelect = $('#editProjectStatus');
    const $campaignDisplay = $('#editProjectCampaign');

    const $submitBtn = $('#submitEditProjectBtn');
    const $submitText = $('#submitEditBtnText');
    const $submitSpinner = $('#submitEditBtnSpinner');

    // Error elements
    const $nameError = $('#editNameError');
    const $descriptionError = $('#editDescriptionError');
    const $startDateError = $('#editStartDateError');
    const $targetDateError = $('#editTargetDateError');
    const $statusError = $('#editStatusError');

    function openModal() {
        // Get current project data from hidden fields
        const projectName = $('#currentProjectName').val();
        const projectDescription = $('#currentProjectDescription').val();
        const startDate = $('#currentProjectStartDate').val();
        const targetDate = $('#currentProjectTargetDate').val();
        const currentStatus = $('#currentProjectStatus').val();

        // Populate form
        $nameInput.val(projectName);
        $descriptionInput.val(projectDescription);
        $startDateInput.val(startDate);
        $targetDateInput.val(targetDate);
        $statusSelect.val(currentStatus);

        clearErrors();
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
        $form[0].reset();
        clearErrors();
    }

    function clearErrors() {
        $nameError.text('').addClass('hidden');
        $descriptionError.text('').addClass('hidden');
        $startDateError.text('').addClass('hidden');
        $targetDateError.text('').addClass('hidden');
        $statusError.text('').addClass('hidden');
    }

    function setLoading(isLoading) {
        $submitBtn.prop('disabled', isLoading);
        $submitText.text(isLoading ? 'Updating...' : 'Update Project');
        isLoading ? $submitSpinner.removeClass('hidden') : $submitSpinner.addClass('hidden');
    }

    // Open modal from edit button
    $(document).on('click', '#editProjectBtn', openModal);

    // Close handlers
    $('#closeEditProjectModal, #cancelEditProjectBtn').on('click', closeModal);
    $modal.on('click', function(e) {
        if ($(e.target).is(this)) closeModal();
    });

    // Submit form
    $form.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
        const formData = new FormData(this);

        $.ajax({
            url: `/projects/${projectId}`,
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
        showToast('success', response.message || 'Project updated successfully');
        closeModal();
        setTimeout(() => window.location.reload(), 500);
    }

    function handleError(xhr) {
        const res = xhr.responseJSON || {};

        if (res.errors) {
            if (res.errors.name) $nameError.text(res.errors.name[0]).removeClass('hidden');
            if (res.errors.description) $descriptionError.text(res.errors.description[0]).removeClass('hidden');
            if (res.errors.start_date) $startDateError.text(res.errors.start_date[0]).removeClass('hidden');
            if (res.errors.target_date) $targetDateError.text(res.errors.target_date[0]).removeClass('hidden');
            if (res.errors.status) $statusError.text(res.errors.status[0]).removeClass('hidden');
        }

        showToast('error', res.message || 'Failed to update project');
    }
});
