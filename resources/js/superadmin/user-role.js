$(function () {
    const $modal = $('#userRoleModal');
    const $form = $('#userRoleForm');
    const $radios = $form.find('input[name="role"]');
    const $name = $('#userRoleName');
    const $feedback = $('#userRoleFeedback');
    const $saveBtn = $('#userRoleSave');
    const $cancelBtn = $('#userRoleCancel');
    const $closeBtn = $('#userRoleClose');
    const $updateUrl = $('#userRoleUpdateUrl');
    const $userId = $('#userRoleUserId');

    function openModal({ name, role, url, userId }) {
        $name.text(name || 'User');
        $radios.prop('checked', false);
        $radios.filter(`[value="${role || 'user'}"]`).prop('checked', true);
        $updateUrl.val(url || '');
        $userId.val(userId || '');
        $feedback.addClass('hidden').text('');
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
    }

    $(document).on('click', '.user-role-open', function () {
        const $btn = $(this);
        openModal({
            name: $btn.data('user-name'),
            role: $btn.data('user-role'),
            url: $btn.data('update-url'),
            userId: $btn.data('user-id'),
        });
    });

    $cancelBtn.on('click', closeModal);
    $closeBtn.on('click', closeModal);
    $modal.on('click', function (event) {
        if (event.target === this) closeModal();
    });

    $form.on('submit', async function (event) {
        event.preventDefault();

        const updateUrl = $updateUrl.val();
        const role = $form.find('input[name="role"]:checked').val();
        const userId = $userId.val();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!updateUrl || !csrfToken || !role) {
            $feedback.removeClass('hidden').text('Missing configuration to update role.');
            return;
        }

        $feedback.addClass('hidden').text('');
        $saveBtn.prop('disabled', true).text('Saving...');

        try {
            const response = await fetch(updateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ role }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                const message = data?.message || 'Unable to update role.';
                $feedback.removeClass('hidden').addClass('text-red-600').text(message);
                return;
            }

            const $row = $(`[data-user-id="${userId}"]`).closest('tr');
            const $roleBadge = $row.find('[data-role-badge]');
            if ($roleBadge.length) {
                $roleBadge
                    .removeClass('bg-purple-100 text-purple-700 bg-orange-100 text-orange-700 bg-emerald-100 text-emerald-700')
                    .text(role.charAt(0).toUpperCase() + role.slice(1));

                const roleClass = role === 'superadmin'
                    ? 'bg-purple-100 text-purple-700'
                    : role === 'admin'
                        ? 'bg-orange-100 text-orange-700'
                        : 'bg-emerald-100 text-emerald-700';
                $roleBadge.addClass(roleClass);
            }

            // Update button data-role for future edits
            const $actionBtn = $row.find('.user-role-open');
            $actionBtn.data('user-role', role);

            $feedback.removeClass('hidden text-red-600').addClass('text-green-600').text('Saved');
            setTimeout(closeModal, 350);
        } catch (error) {
            console.error('Failed to update role', error);
            $feedback.removeClass('hidden').addClass('text-red-600').text('Unexpected error. Please try again.');
        } finally {
            $saveBtn.prop('disabled', false).text('Save');
        }
    });
});
