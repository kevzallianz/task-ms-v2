/**
 * Add Task Modal Handler
 * Handles modal lifecycle and AJAX submission for creating project tasks
 */

$(document).ready(function() {
    const projectId = parseInt($('#projectId').val()) || null;

    if (!projectId) {
        console.error('Project ID not found. Task modal disabled.');
        return;
    }

    // Element references
    const $addTaskModal = $('#addTaskModal');
    const $addTaskBtn = $('#addTaskBtn');
    const $addTaskBtnHeader = $('#addTaskBtnHeader');
    const $closeTaskModal = $('#closeTaskModal');
    const $cancelTaskBtn = $('#cancelTaskBtn');
    const $addTaskForm = $('#addTaskForm');
    const $submitTaskBtn = $('#submitTaskBtn');
    const $submitTaskBtnText = $('#submitTaskBtnText');
    const $submitTaskBtnSpinner = $('#submitTaskBtnSpinner');

    // Modal helpers
    function openModal() {
        $addTaskModal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $addTaskModal.addClass('hidden').removeClass('flex');
        if ($addTaskForm.length) {
            $addTaskForm[0].reset();
        }
        clearErrors();
    }

    function clearErrors() {
        $('#taskTitleError, #taskCampaignError, #taskStatusError, #taskStartDateError, #taskTargetDateError, #taskDescriptionError')
            .text('')
            .addClass('hidden');
    }

    function setSubmitButtonLoading(isLoading) {
        $submitTaskBtn.prop('disabled', isLoading);
        $submitTaskBtnText.text(isLoading ? 'Saving...' : 'Create Task');
        isLoading ? $submitTaskBtnSpinner.removeClass('hidden') : $submitTaskBtnSpinner.addClass('hidden');
    }

    // Event bindings
    $addTaskBtn.on('click', openModal);
    $addTaskBtnHeader.on('click', openModal);
    $closeTaskModal.on('click', closeModal);
    $cancelTaskBtn.on('click', closeModal);

    $addTaskModal.on('click', function(e) {
        if ($(e.target).is(this)) {
            closeModal();
        }
    });

    // Form submission
    $addTaskForm.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        setSubmitButtonLoading(true);

        const formData = new FormData(this);
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

        $.ajax({
            url: `/projects/${projectId}/tasks`,
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
            complete: () => setSubmitButtonLoading(false),
        });
    });

    function handleSuccess(response) {
        showToast('success', response.message || 'Task created successfully!');
        closeModal();
        setTimeout(() => window.location.reload(), 800);
    }

    function handleError(xhr) {
        const responseJSON = xhr.responseJSON || {};
        const errors = responseJSON.errors || {};

        if (errors.title) {
            $('#taskTitleError').text(errors.title[0]).removeClass('hidden');
        }
        if (errors.assigned_campaign_id) {
            $('#taskCampaignError').text(errors.assigned_campaign_id[0]).removeClass('hidden');
        }
        if (errors.status) {
            $('#taskStatusError').text(errors.status[0]).removeClass('hidden');
        }
        if (errors.start_date) {
            $('#taskStartDateError').text(errors.start_date[0]).removeClass('hidden');
        }
        if (errors.target_date) {
            $('#taskTargetDateError').text(errors.target_date[0]).removeClass('hidden');
        }
        if (errors.description) {
            $('#taskDescriptionError').text(errors.description[0]).removeClass('hidden');
        }

        const message = responseJSON.message || 'Failed to create task. Please try again.';
        showToast('error', message);
    }
});
