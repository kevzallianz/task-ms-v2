$(function () {
    const $modal = $('#deleteCampaignModal');
    const $name = $('#deleteCampaignName');
    const $deleteUrl = $('#deleteCampaignUrl');
    const $campaignId = $('#deleteCampaignId');
    const $feedback = $('#deleteCampaignFeedback');
    const $confirmBtn = $('#deleteCampaignConfirmBtn');
    const $cancelBtn = $('#deleteCampaignCancelBtn');
    const $closeBtn = $('#deleteCampaignCloseBtn');
    const $membersAlert = $('#deleteCampaignMembersAlert');
    const $membersCount = $('#deleteCampaignMembersCount');

    function openModal({ id, name, url, membersCount }) {
        $name.text(name || 'Campaign');
        $deleteUrl.val(url || '');
        $campaignId.val(id || '');
        $feedback.addClass('hidden').text('');

        const count = parseInt(membersCount, 10) || 0;
        if (count > 0) {
            $membersCount.text(count);
            $membersAlert.removeClass('hidden').addClass('flex');
        } else {
            $membersAlert.addClass('hidden').removeClass('flex');
        }

        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
    }

    $(document).on('click', '.campaign-delete-open', function () {
        const $btn = $(this);
        openModal({
            id: $btn.data('campaign-id'),
            name: $btn.data('campaign-name'),
            url: $btn.data('delete-url'),
            membersCount: $btn.data('members-count'),
        });
    });

    $cancelBtn.on('click', closeModal);
    $closeBtn.on('click', closeModal);
    $modal.on('click', function (event) {
        if (event.target === this) closeModal();
    });

    $confirmBtn.on('click', async function () {
        const url = $deleteUrl.val();
        const id = $campaignId.val();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!url || !csrfToken) {
            $feedback.removeClass('hidden').text('Missing configuration to delete campaign.');
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
                $feedback.removeClass('hidden').text(data?.message || 'Unable to delete campaign.');
                return;
            }

            // Remove the row from the table
            $(`[data-campaign-row="${id}"]`).fadeOut(300, function () {
                $(this).remove();
            });

            closeModal();
        } catch (error) {
            console.error('Failed to delete campaign', error);
            $feedback.removeClass('hidden').text('Unexpected error. Please try again.');
        } finally {
            $confirmBtn.prop('disabled', false).text('Delete Campaign');
        }
    });
});
