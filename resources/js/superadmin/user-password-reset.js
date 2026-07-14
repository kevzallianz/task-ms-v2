$(function () {
    const $modal = $('#userPasswordResetModal');
    const $name = $('#userPasswordResetName');
    const $email = $('#userPasswordResetEmail');
    const $resetUrl = $('#userPasswordResetUrl');
    const $feedback = $('#userPasswordResetFeedback');
    const $confirmBtn = $('#userPasswordResetConfirm');
    const $cancelBtn = $('#userPasswordResetCancel');
    const $closeBtn = $('#userPasswordResetClose');

    function openModal({ name, email, url }) {
        $name.text(name || 'this user');
        $email.text(email || 'no email address');
        $resetUrl.val(url || '');
        $feedback.addClass('hidden').removeClass('text-red-600 text-emerald-700').text('');
        $confirmBtn.prop('disabled', false).text('Send Reset Link');
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
    }

    $(document).on('click', '.user-password-reset-open', function () {
        const $btn = $(this);
        openModal({
            name: $btn.data('user-name'),
            email: $btn.data('user-email'),
            url: $btn.data('reset-url'),
        });
    });

    $cancelBtn.on('click', closeModal);
    $closeBtn.on('click', closeModal);
    $modal.on('click', function (event) {
        if (event.target === this) closeModal();
    });

    $confirmBtn.on('click', async function () {
        const url = $resetUrl.val();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!url || !csrfToken) {
            $feedback.removeClass('hidden').addClass('text-red-600').text('Missing password reset configuration.');
            return;
        }

        $feedback.addClass('hidden').removeClass('text-red-600 text-emerald-700').text('');
        $confirmBtn.prop('disabled', true).text('Sending...');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                $feedback.removeClass('hidden').addClass('text-red-600').text(data?.message || 'Unable to send the reset link.');
                return;
            }

            $feedback.removeClass('hidden').addClass('text-emerald-700').text(data.message);
            $confirmBtn.text('Link Sent');
        } catch (error) {
            console.error('Failed to send password reset link', error);
            $feedback.removeClass('hidden').addClass('text-red-600').text('Unexpected error. Please try again.');
        } finally {
            if ($confirmBtn.text() !== 'Link Sent') {
                $confirmBtn.prop('disabled', false).text('Send Reset Link');
            }
        }
    });
});
