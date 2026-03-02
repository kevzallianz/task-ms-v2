/**
 * Update project status modal handler
 */

$(document).ready(function() {
    const projectId = parseInt($('[data-project-id]').attr('data-project-id')) || null;
    if (!projectId) return;

    const $modal = $('#updateProjectStatusModal');
    const $form = $('#updateProjectStatusForm');
    const $statusRadios = $('#updateProjectStatusRadios input[name="status"]');
    const $statusError = $('#updateProjectStatusError');
    const $projectName = $('#updateProjectName');
    const $projectCurrentStatus = $('#updateProjectCurrentStatus');
    const $submitBtn = $('#submitUpdateProjectStatusBtn');
    const $submitText = $('#submitUpdateProjectStatusBtnText');
    const $submitSpinner = $('#submitUpdateProjectStatusBtnSpinner');

    function openModal(projectName, currentStatus) {
        $projectName.text(projectName || 'Update status');
        const statusToSet = currentStatus || 'planning';
        $projectCurrentStatus.text(`Current status: ${statusToSet.replace('_', ' ')}`);
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

    // open from button
    $(document).on('click', '#updateProjectStatusBtn', function() {
        const $projectHeader = $('[data-project-id]');
        const projectName = $projectHeader.find('h1').text().trim();
        const currentStatus = $projectHeader.find('.bg-blue-100, .bg-yellow-100, .bg-green-100, .bg-red-100').first().text().trim().toLowerCase();

        openModal(projectName, currentStatus);
    });

    // close handlers
    $('#closeUpdateProjectStatusModal, #cancelUpdateProjectStatusBtn').on('click', closeModal);
    $modal.on('click', function(e) {
        if ($(e.target).is(this)) closeModal();
    });

    // submit
    $form.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
        const newStatus = $statusRadios.filter(':checked').val();

        const formData = new FormData();
        formData.append('status', newStatus);

        $.ajax({
            url: `/projects/${projectId}/status`,
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
        showToast('success', response.message || 'Project status updated');
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
