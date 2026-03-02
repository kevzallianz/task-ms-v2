const ACCESS_LABELS = {
    viewer: 'View',
    editor: 'Editor',
    all: 'All',
};

const ACCESS_CLASSES = {
    viewer: 'bg-gray-100 text-gray-700',
    editor: 'bg-blue-100 text-blue-700',
    all: 'bg-indigo-100 text-indigo-700',
};

function updateBadge($badge, accessLevel) {
    if (!$badge || !$badge.length) return;

    Object.values(ACCESS_CLASSES).forEach((classGroup) => {
        classGroup.split(' ').forEach((cls) => $badge.removeClass(cls));
    });

    const label = ACCESS_LABELS[accessLevel] || accessLevel;
    $badge.attr('data-access-level', accessLevel);
    $badge.text(`${label} Access`);
    $badge.addClass(ACCESS_CLASSES[accessLevel] || ACCESS_CLASSES.viewer);
}

function setFeedback($feedback, message, isError = false) {
    if (!$feedback || !$feedback.length) return;
    $feedback
        .text(message)
        .toggleClass('text-green-600', !isError)
        .toggleClass('text-red-600', isError)
        .removeClass('hidden');
}

$(document).ready(function () {
    const $modal = $('#memberAccessModal');
    const $form = $('#memberAccessForm');
    const $radios = $form.find('input[name="access_level"]');
    const $name = $('#memberAccessName');
    const $feedback = $('#memberAccessFeedback');
    const $saveBtn = $('#memberAccessSave');
    const $cancelBtn = $('#memberAccessCancel');
    const $closeBtn = $('#memberAccessClose');
    const $updateUrl = $('#memberAccessUpdateUrl');
    const $memberId = $('#memberAccessMemberId');

    function openModal({ name, level, url, memberId }) {
        $name.text(name || 'Member');
        $radios.prop('checked', false);
        const targetLevel = level || 'viewer';
        $radios.filter(`[value="${targetLevel}"]`).prop('checked', true);
        $updateUrl.val(url || '');
        $memberId.val(memberId || '');
        $feedback.addClass('hidden').text('');
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
    }

    $(document).on('click', '.member-access-open', function () {
        const $btn = $(this);
        openModal({
            name: $btn.data('member-name'),
            level: $btn.data('current-level'),
            url: $btn.data('update-url'),
            memberId: $btn.data('member-id'),
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
        const accessLevel = $form.find('input[name="access_level"]:checked').val();
        const memberId = $memberId.val();
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        if (!updateUrl || !csrfToken || !accessLevel) {
            setFeedback($feedback, 'Missing configuration to update access.', true);
            return;
        }

        $feedback.addClass('hidden');
        $saveBtn.prop('disabled', true).text('Saving...');

        try {
            const response = await fetch(updateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ access_level: accessLevel }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                const message = data?.message || 'Unable to update access level.';
                setFeedback($feedback, message, true);
                return;
            }

            const $badge = $(`[data-campaign-member-id="${memberId}"]`).find(
                '.member-access-badge'
            );
            updateBadge($badge, accessLevel);
            setFeedback($feedback, 'Saved', false);
            setTimeout(closeModal, 400);
        } catch (error) {
            console.error('Failed to update access level', error);
            setFeedback($feedback, 'Unexpected error. Please try again.', true);
        } finally {
            $saveBtn.prop('disabled', false).text('Save');
        }
    });
});
