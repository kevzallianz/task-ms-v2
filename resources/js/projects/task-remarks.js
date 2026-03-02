/**
 * Task remarks modal handler
 */

$(document).ready(function() {
    const projectId = parseInt($('#projectId').val()) || null;
    if (!projectId) return;

    const $modal = $('#projectTaskRemarksModal');
    const $container = $('#projectTaskRemarksList');
    const $taskId = $('#projectTaskRemarksTaskId');
    const $taskTitle = $('#projectTaskRemarksTaskTitle');
    const $form = $('#addProjectTaskRemarkForm');
    const $textarea = $('#projectTaskRemarkInput');
    const $charCount = $('#projectTaskRemarkCharCount');
    const $submitBtn = $('#submitProjectTaskRemarkBtn');
    const $submitBtnText = $('#submitProjectTaskRemarkBtnText');
    const $submitBtnSpinner = $('#submitProjectTaskRemarkBtnSpinner');
    const $remarkError = $('#projectTaskRemarkError');

    function openModal(taskId, title) {
        $taskId.val(taskId);
        $taskTitle.text(`Viewing remarks for: ${title}`);
        $textarea.val('');
        $charCount.text('0');
        $remarkError.text('').addClass('hidden');
        $modal.removeClass('hidden').addClass('flex');
        fetchRemarks(taskId);
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
        $container.html('');
    }

    function fetchRemarks(taskId) {
        $container.html('<div class="text-center py-8"><svg class="w-8 h-8 animate-spin mx-auto text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><p class="text-sm text-gray-500 mt-2">Loading remarks...</p></div>');

        $.ajax({
            url: `/projects/${projectId}/tasks/${taskId}/remarks`,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: handleSuccess,
            error: handleError,
        });
    }

    function handleSuccess(response) {
        const remarks = response.remarks || [];

        if (remarks.length === 0) {
            $container.html('<div class="text-center py-8"><svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg><p class="text-sm text-gray-500 mt-2">No remarks yet. Be the first to add one!</p></div>');
            return;
        }

        let html = '';
        remarks.forEach(remark => {
            const createdAt = new Date(remark.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });

            html += `
                <div class="p-4 rounded-lg border border-secondary/30 bg-white hover:border-primary/50 transition">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="w-8 h-8 rounded-full bg-linear-to-br from-primary to-primary/60 flex items-center justify-center text-xs font-bold text-white shrink-0">
                            ${remark.user.name.charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-foreground">${remark.user.name}</p>
                            <span class="text-xs text-gray-500">${createdAt}</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap wrap-break-word">${escapeHtml(remark.remarks)}</p>
                </div>
            `;
        });

        $container.html(html).removeClass('hidden');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function handleError(xhr) {
        $container.html('<div class="text-center py-8 text-red-500"><p class="text-sm">Failed to load remarks</p></div>');
        showToast('error', 'Failed to load remarks');
    }

    function setSubmitLoading(isLoading) {
        $submitBtn.prop('disabled', isLoading);
        $submitBtnText.text(isLoading ? 'Posting...' : 'Post Remark');
        isLoading ? $submitBtnSpinner.removeClass('hidden') : $submitBtnSpinner.addClass('hidden');
    }

    // Character counter
    $textarea.on('input', function() {
        $charCount.text($(this).val().length);
    });

    // Submit remark
    $form.on('submit', function(e) {
        e.preventDefault();
        $remarkError.text('').addClass('hidden');

        const taskId = $taskId.val();
        const remark = $textarea.val().trim();

        if (!remark) {
            $remarkError.text('Please enter a remark').removeClass('hidden');
            return;
        }

        setSubmitLoading(true);

        const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
        const formData = new FormData();
        formData.append('remark', remark);

        $.ajax({
            url: `/projects/${projectId}/tasks/${taskId}/remarks`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: handleAddRemarkSuccess,
            error: handleAddRemarkError,
            complete: () => setSubmitLoading(false),
        });
    });

    function handleAddRemarkSuccess(response) {
        showToast('success', response.message || 'Remark added successfully');
        $textarea.val('');
        $charCount.text('0');
        fetchRemarks($taskId.val());
    }

    function handleAddRemarkError(xhr) {
        const res = xhr.responseJSON || {};
        $remarkError.text(res.message || 'Failed to add remark').removeClass('hidden');
        showToast('error', res.message || 'Failed to add remark');
    }

    // Open from task buttons
    $(document).on('click', '.taskActionBtn[data-action="remarks"]', function() {
        const taskId = $(this).data('task-id');
        const taskTitle = $(this).data('task-title');
        openModal(taskId, taskTitle);
    });

    // Close handlers
    $('#closeProjectTaskRemarksModal, #closeProjectTaskRemarksBtnFooter').on('click', closeModal);
    $modal.on('click', function(e) {
        if ($(e.target).is(this)) closeModal();
    });
});
