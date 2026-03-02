/**
 * Add Contributor Modal Handler
 * Handles all AJAX operations and DOM manipulations for the add contributor modal
 */

$(document).ready(function() {
    // =====================
    // Element References
    // =====================
    
    // Get project ID from hidden input
    const projectId = parseInt($('#projectId').val()) || null;
    
    console.log('Project ID:', projectId);
    
    if (!projectId) {
        console.error('Project ID not found. Modal will not function.');
        return;
    }

    const $addContributorModal = $('#addContributorModal');
    const $addContributorBtn = $('#addContributorBtn');
    const $addContributorBtnHeader = $('#addContributorBtnHeader');
    const $closeContributorModal = $('#closeContributorModal');
    const $cancelContributorBtn = $('#cancelContributorBtn');
    const $addContributorForm = $('#addContributorForm');
    const $submitContributorBtn = $('#submitContributorBtn');
    const $submitBtnText = $('#submitBtnText');
    const $submitBtnSpinner = $('#submitBtnSpinner');

    // Verify all elements exist
    if (!$addContributorModal.length) {
        console.warn('Add Contributor Modal element not found in DOM');
    }
    if (!$addContributorBtn.length && !$addContributorBtnHeader.length) {
        console.warn('Add Contributor Button element not found in DOM');
    }

    // =====================
    // Modal Management
    // =====================

    /**
     * Open the add contributor modal
     */
    function openModal() {
        $addContributorModal.removeClass('hidden').addClass('flex');
    }

    /**
     * Close the add contributor modal and reset form
     */
    function closeModal() {
        $addContributorModal.addClass('hidden').removeClass('flex');
        $addContributorForm[0].reset();
        clearErrors();
    }

    /**
     * Clear all error messages
     */
    function clearErrors() {
        $('#campaignError').text('').addClass('hidden');
    }

    // =====================
    // Event Listeners
    // =====================

    // Open modal buttons (sidebar and header)
    $addContributorBtn.on('click', openModal);
    $addContributorBtnHeader.on('click', openModal);

    // Close modal buttons
    $closeContributorModal.on('click', closeModal);
    $cancelContributorBtn.on('click', closeModal);

    // Close modal when clicking outside
    $addContributorModal.on('click', function(e) {
        if ($(e.target).is(this)) {
            closeModal();
        }
    });

    // =====================
    // Form Submission
    // =====================

    /**
     * Handle form submission for adding a contributor
     */
    $addContributorForm.on('submit', function(e) {
        e.preventDefault();
        
        clearErrors();
        setSubmitButtonLoading(true);

        const formData = new FormData(this);
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

        $.ajax({
            url: `/projects/${projectId}/add-contributor`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: handleAddContributorSuccess,
            error: handleAddContributorError,
            complete: () => setSubmitButtonLoading(false)
        });
    });

    /**
     * Handle successful contributor addition
     */
    function handleAddContributorSuccess(response) {
        showToast('success', response.message || 'Contributor added successfully!');
        closeModal();
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    /**
     * Handle contributor addition error
     */
    function handleAddContributorError(xhr) {
        const responseJSON = xhr.responseJSON || {};
        
        // Display validation errors
        if (responseJSON.errors) {
            if (responseJSON.errors.campaign_id) {
                $('#campaignError')
                    .text(responseJSON.errors.campaign_id[0])
                    .removeClass('hidden');
            }
        }
        
        const message = responseJSON.message || 'Failed to add contributor';
        showToast('error', message);
    }

    /**
     * Update submit button loading state
     */
    function setSubmitButtonLoading(isLoading) {
        $submitContributorBtn.prop('disabled', isLoading);
        $submitBtnText.text(isLoading ? 'Adding...' : 'Add Contributor');
        isLoading ? $submitBtnSpinner.removeClass('hidden') : $submitBtnSpinner.addClass('hidden');
    }

    // =====================
    // Contributor Removal
    // =====================

    /**
     * Handle remove contributor button clicks
     */
    $(document).on('click', '.removeContributorBtn', function() {
        const contributorId = $(this).data('id');
        
        if (confirm('Are you sure you want to remove this contributor?')) {
            removeContributor(contributorId);
        }
    });

    /**
     * Remove a contributor via AJAX
     */
    function removeContributor(contributorId) {
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

        $.ajax({
            url: `/projects/${projectId}/remove-contributor/${contributorId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: handleRemoveContributorSuccess,
            error: handleRemoveContributorError
        });
    }

    /**
     * Handle successful contributor removal
     */
    function handleRemoveContributorSuccess(response) {
        showToast('success', response.message || 'Contributor removed successfully!');
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    /**
     * Handle contributor removal error
     */
    function handleRemoveContributorError(xhr) {
        const responseJSON = xhr.responseJSON || {};
        const message = responseJSON.message || 'Failed to remove contributor';
        showToast('error', message);
    }
});
