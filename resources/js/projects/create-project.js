// Get elements using jQuery
const $createProjectModal = $('#createProjectModal');
const $openCreateProjectModalBtn = $('#openCreateProjectModal');
const $closeProjectModalBtn = $('#closeProjectModal');
const $cancelProjectBtn = $('#cancelProjectBtn');
const $createProjectForm = $('#createProjectForm');
const $submitProjectBtn = $('#submitProjectBtn');
const $submitBtnText = $('#submitBtnText');
const $submitBtnSpinner = $('#submitBtnSpinner');

// Open modal
$openCreateProjectModalBtn.on('click', function() {
    $createProjectModal.removeClass('hidden').addClass('flex');
    resetForm();
});

// Close modal
const closeModal = () => {
    $createProjectModal.addClass('hidden').removeClass('flex');
    resetForm();
};

$closeProjectModalBtn.on('click', closeModal);
$cancelProjectBtn.on('click', closeModal);

// Close modal when clicking outside
$createProjectModal.on('click', function(e) {
    if ($(e.target).is(this)) {
        closeModal();
    }
});

// Form submission with AJAX
$createProjectForm.on('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    clearErrors();
    
    // Disable submit button
    $submitProjectBtn.prop('disabled', true);
    $submitBtnText.text('Creating...');
    $submitBtnSpinner.removeClass('hidden');

    const $form = $(this);
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

    $.ajax({
        url: '/projects/store',
        method: 'POST',
        data: $form.serialize(),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        success: function(response) {
            showToast('success', response.message || 'Project created successfully!');
            closeModal();
            
            // Reload projects list
            setTimeout(() => {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            const responseJSON = xhr.responseJSON || {};
            
            if (responseJSON.errors) {
                displayErrors(responseJSON.errors);
            }
            
            const message = responseJSON.message || 'Failed to create project';
            showToast('error', message);
        },
        complete: function() {
            $submitProjectBtn.prop('disabled', false);
            $submitBtnText.text('Create Project');
            $submitBtnSpinner.addClass('hidden');
        }
    });
});

// Clear form
function resetForm() {
    $createProjectForm[0].reset();
    const today = new Date().toISOString().split('T')[0];
    $('#startDate').val(today);
    clearErrors();
}

// Display validation errors
function displayErrors(errors) {
    $.each(errors, function(field, messages) {
        const $errorElement = $(`#${field}Error`);
        if ($errorElement.length) {
            $errorElement.text(messages[0]).removeClass('hidden');
        }
    });
}

// Clear all error messages
function clearErrors() {
    $('[id$="Error"]').text('').addClass('hidden');
}

// Focus on input field when error is shown
$createProjectForm.on('change', function(e) {
    const fieldName = $(e.target).attr('name');
    const $errorElement = $(`#${fieldName}Error`);
    if ($errorElement.length) {
        $errorElement.addClass('hidden').text('');
    }
});
