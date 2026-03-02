$(function () {
    const $modal = $('#userCampaignModal');
    const $form = $('#userCampaignForm');
    const $campaignSelect = $('#userCampaignSelect');
    const $radios = $form.find('input[name="access_level"]');
    const $name = $('#userCampaignName');
    const $feedback = $('#userCampaignFeedback');
    const $saveBtn = $('#userCampaignSave');
    const $cancelBtn = $('#userCampaignCancel');
    const $closeBtn = $('#userCampaignClose');
    const $updateUrl = $('#userCampaignUpdateUrl');
    const $userId = $('#userCampaignUserId');

    const ACCESS_CLASSES = {
        viewer: 'bg-gray-100 text-gray-700',
        editor: 'bg-blue-100 text-blue-700',
        all: 'bg-indigo-100 text-indigo-700',
    };

    function openModal({ name, campaignId, accessLevel, url, userId }) {
        $name.text(name || 'User');
        $campaignSelect.val(campaignId || '');
        $radios.prop('checked', false);
        const targetLevel = accessLevel || 'viewer';
        $radios.filter(`[value="${targetLevel}"]`).prop('checked', true);
        $updateUrl.val(url || '');
        $userId.val(userId || '');
        $feedback.addClass('hidden').text('');
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
    }

    function updateAccessBadge($row, accessLevel) {
        const $badge = $row.find('[data-access-badge]');
        if (!$badge.length) return;

        $badge
            .removeClass(Object.values(ACCESS_CLASSES).join(' '))
            .addClass(ACCESS_CLASSES[accessLevel] || ACCESS_CLASSES.viewer)
            .text(accessLevel ? accessLevel.charAt(0).toUpperCase() + accessLevel.slice(1) : '');
    }

    function updateCampaignDisplay($row, campaignName) {
        const $cell = $row.find('[data-campaign-cell]');
        if (!$cell.length) return;

        const $empty = $cell.find('[data-campaign-empty]');
        const $display = $cell.find('[data-campaign-display]');
        const $name = $cell.find('[data-campaign-name]');
        const $desc = $cell.find('[data-campaign-desc]');

        if (campaignName) {
            if (!$display.length) {
                $cell.html(`
                    <div class="flex flex-col gap-1" data-campaign-display>
                        <p class="text-sm font-medium text-foreground" data-campaign-name></p>
                        <p class="text-xs text-gray-500" data-campaign-desc></p>
                    </div>
                `);
            }
            $cell.find('[data-campaign-name]').text(campaignName);
            $cell.find('[data-campaign-desc]').text('');
        } else {
            $cell.html('<span class="text-sm text-gray-500" data-campaign-empty>—</span>');
        }
    }

    $(document).on('click', '.user-campaign-open', function () {
        const $btn = $(this);
        openModal({
            name: $btn.data('user-name'),
            campaignId: $btn.data('current-campaign-id'),
            accessLevel: $btn.data('current-access-level'),
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
        const campaignId = $campaignSelect.val();
        const accessLevel = $form.find('input[name="access_level"]:checked').val();
        const userId = $userId.val();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!updateUrl || !csrfToken || !campaignId || !accessLevel) {
            $feedback.removeClass('hidden').text('Campaign and access level are required.');
            return;
        }

        $feedback.addClass('hidden');
        $saveBtn.prop('disabled', true).text('Saving...');

        try {
            const response = await fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ campaign_id: campaignId, access_level: accessLevel }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                const message = data?.message || 'Unable to assign campaign.';
                $feedback.removeClass('hidden').addClass('text-red-600').text(message);
                return;
            }

            const $row = $(`[data-user-row="${userId}"]`);
            updateCampaignDisplay($row, data.campaign?.name || '');
            updateAccessBadge($row, accessLevel);

            const $assignBtn = $row.find('.user-campaign-open');
            $assignBtn.data('current-campaign-id', data.campaign?.id || '');
            $assignBtn.data('current-access-level', accessLevel);

            $feedback.removeClass('hidden text-red-600').addClass('text-green-600').text('Saved');
            setTimeout(closeModal, 350);
        } catch (error) {
            console.error('Failed to assign campaign', error);
            $feedback.removeClass('hidden').addClass('text-red-600').text('Unexpected error. Please try again.');
        } finally {
            $saveBtn.prop('disabled', false).text('Save');
        }
    });
});
