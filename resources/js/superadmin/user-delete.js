$(function () {
    const $modal = $('#userDeleteModal');
    const $name = $('#userDeleteName');
    const $deleteUrl = $('#userDeleteUrl');
    const $userId = $('#userDeleteUserId');
    const $feedback = $('#userDeleteFeedback');
    const $confirmBtn = $('#userDeleteConfirm');
    const $cancelBtn = $('#userDeleteCancel');
    const $closeBtn = $('#userDeleteClose');

    function openModal({ name, url, userId }) {
        $name.text(name || 'User');
        $deleteUrl.val(url || '');
        $userId.val(userId || '');
        $feedback.addClass('hidden').text('');
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
    }

    $(document).on('click', '.user-delete-open', function () {
        const $btn = $(this);
        openModal({
            name: $btn.data('user-name'),
            url: $btn.data('delete-url'),
            userId: $btn.data('user-id'),
        });
    });

    $cancelBtn.on('click', closeModal);
    $closeBtn.on('click', closeModal);
    $modal.on('click', function (event) {
        if (event.target === this) closeModal();
    });

    $confirmBtn.on('click', async function () {
        const url = $deleteUrl.val();
        const userId = $userId.val();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!url || !csrfToken) {
            $feedback.removeClass('hidden').text('Missing configuration to delete user.');
            return;
        }

        $feedback.addClass('hidden').text('');
        $confirmBtn.prop('disabled', true).text('Deleting...');

        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                const message = data?.message || 'Unable to delete user.';
                $feedback.removeClass('hidden').text(message);
                return;
            }

            // Remove the row from the table
            $(`[data-user-row="${userId}"]`).fadeOut(300, function () {
                $(this).remove();
            });

            closeModal();
        } catch (error) {
            console.error('Failed to delete user', error);
            $feedback.removeClass('hidden').text('Unexpected error. Please try again.');
        } finally {
            $confirmBtn.prop('disabled', false).text('Delete User');
        }
    });
});
