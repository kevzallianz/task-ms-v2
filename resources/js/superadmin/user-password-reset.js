$(function () {
    const $modal = $('#userPasswordResetModal');
    const $name = $('#userPasswordResetName');
    const $email = $('#userPasswordResetEmail');
    const $resetUrl = $('#userPasswordResetUrl');
    const $feedback = $('#userPasswordResetFeedback');
    const $fallback = $('#userPasswordResetFallback');
    const $resetLink = $('#userPasswordResetLink');
    const $copyBtn = $('#userPasswordResetCopy');
    const $confirmBtn = $('#userPasswordResetConfirm');
    const $cancelBtn = $('#userPasswordResetCancel');
    const $closeBtn = $('#userPasswordResetClose');

    function openModal({ name, email, url }) {
        $name.text(name || 'this user');
        $email.text(email || 'no email address');
        $resetUrl.val(url || '');
        $feedback.addClass('hidden').removeClass('text-red-600 text-amber-700 text-emerald-700').text('');
        $fallback.addClass('hidden');
        $resetLink.val('');
        $copyBtn.text('Copy');
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

    $copyBtn.on('click', async function () {
        const resetLink = $resetLink.val();

        if (!resetLink) return;

        try {
            await navigator.clipboard.writeText(resetLink);
            $copyBtn.text('Copied');
        } catch (error) {
            $resetLink.trigger('select');
            document.execCommand('copy');
            $copyBtn.text('Copied');
        }
    });

    $confirmBtn.on('click', async function () {
        const url = $resetUrl.val();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!url || !csrfToken) {
            $feedback.removeClass('hidden').addClass('text-red-600').text('Missing password reset configuration.');
            return;
        }

        $feedback.addClass('hidden').removeClass('text-red-600 text-amber-700 text-emerald-700').text('');
        $confirmBtn.prop('disabled', true).text('Sending...');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.success) {
                $feedback.removeClass('hidden').addClass('text-red-600').text(data?.message || 'Unable to send the reset link.');
                return;
            }

            if (data.delivered === false && data.reset_url) {
                $feedback.removeClass('hidden').addClass('text-amber-700').text(data.message);
                $fallback.removeClass('hidden');
                $resetLink.val(data.reset_url);
                $confirmBtn.text('Reset Link Ready');
                return;
            }

            $feedback.removeClass('hidden').addClass('text-emerald-700').text(data.message);
            $confirmBtn.text('Link Sent');
        } catch (error) {
            console.error('Failed to send password reset link', error);
            $feedback.removeClass('hidden').addClass('text-red-600').text('Unexpected error. Please try again.');
        } finally {
            if (!['Link Sent', 'Reset Link Ready'].includes($confirmBtn.text())) {
                $confirmBtn.prop('disabled', false).text('Send Reset Link');
            }
        }
    });
});
