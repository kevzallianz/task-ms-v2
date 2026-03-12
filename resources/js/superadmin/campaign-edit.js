$(function () {
    const $modal = $('#editCampaignModal');
    const $form = $('#editCampaignForm');
    const $title = $('#editCampaignTitle');
    const $updateUrl = $('#editCampaignUpdateUrl');
    const $campaignId = $('#editCampaignId');
    const $name = $('#editCampaignName');
    const $desc = $('#editCampaignDescription');
    const $feedback = $('#editCampaignFeedback');
    const $saveBtn = $('#editCampaignSaveBtn');
    const $cancelBtn = $('#editCampaignCancelBtn');
    const $closeBtn = $('#editCampaignCloseBtn');
    const errorMap = {
        name: $('#editCampaignNameError'),
        description: $('#editCampaignDescriptionError'),
    };

    function clearErrors() {
        Object.values(errorMap).forEach(($el) => $el.addClass('hidden').text(''));
        $feedback.addClass('hidden').text('');
    }

    function openModal({ id, name, description, url }) {
        clearErrors();
        $title.text(name || 'Campaign');
        $campaignId.val(id || '');
        $updateUrl.val(url || '');
        $name.val(name || '');
        $desc.val(description || '');
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
    }

    $(document).on('click', '.campaign-edit-open', function () {
        const $btn = $(this);
        openModal({
            id: $btn.data('campaign-id'),
            name: $btn.data('campaign-name'),
            description: $btn.data('campaign-description'),
            url: $btn.data('update-url'),
        });
    });

    $cancelBtn.on('click', closeModal);
    $closeBtn.on('click', closeModal);
    $modal.on('click', function (event) {
        if (event.target === this) closeModal();
    });

    $form.on('submit', async function (event) {
        event.preventDefault();
        clearErrors();

        const url = $updateUrl.val();
        const id = $campaignId.val();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!url || !csrfToken) {
            $feedback.removeClass('hidden').text('Missing configuration.');
            return;
        }

        $saveBtn.prop('disabled', true).text('Saving...');

        try {
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    name: $name.val().trim(),
                    description: $desc.val().trim(),
                }),
            });

            const data = await response.json();

            if (response.status === 422) {
                const errors = data.errors || {};
                Object.keys(errors).forEach((key) => {
                    if (errorMap[key]) {
                        errorMap[key].removeClass('hidden').text(errors[key][0]);
                    }
                });
                return;
            }

            if (!response.ok || !data.success) {
                $feedback.removeClass('hidden').text(data?.message || 'Unable to update campaign.');
                return;
            }

            // Update the table row inline
            const $row = $(`[data-campaign-row="${id}"]`);
            if ($row.length) {
                $row.find('td:nth-child(1) .font-medium').text(data.campaign.name);
                $row.find('td:nth-child(2) p').text(data.campaign.description || '—');

                // Update button data attrs for future edits
                $row.find('.campaign-edit-open')
                    .data('campaign-name', data.campaign.name)
                    .data('campaign-description', data.campaign.description || '');
                $row.find('.campaign-delete-open').data('campaign-name', data.campaign.name);
            }

            closeModal();
        } catch (error) {
            console.error('Failed to update campaign', error);
            $feedback.removeClass('hidden').text('Unexpected error. Please try again.');
        } finally {
            $saveBtn.prop('disabled', false).text('Save Changes');
        }
    });
});
