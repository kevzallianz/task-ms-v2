$(function () {
    const STORAGE_KEY = 'sa_user_selection';

    const $selectAll = $('#userSelectAll');
    const $count = $('#userSelectCount');
    const $bulkBtn = $('#bulkAssignOpen');

    const $modal = $('#userCampaignBulkModal');
    const $form = $('#userCampaignBulkForm');
    const $campaignSelect = $('#userCampaignBulkSelect');
    const $radios = $form.find('input[name="access_level"]');
    const $title = $('#userCampaignBulkTitle');
    const $feedback = $('#userCampaignBulkFeedback');
    const $saveBtn = $('#userCampaignBulkSave');
    const $cancelBtn = $('#userCampaignBulkCancel');
    const $closeBtn = $('#userCampaignBulkClose');
    const $updateUrl = $('#userCampaignBulkUpdateUrl');
    const $userIdsField = $('#userCampaignBulkUserIds');

    function loadSelection() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return new Set();
            const arr = JSON.parse(raw);
            return new Set(Array.isArray(arr) ? arr.map(String) : []);
        } catch (e) {
            console.warn('Failed to load selection', e);
            return new Set();
        }
    }

    function saveSelection(sel) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(sel)));
        } catch (e) {
            console.warn('Failed to save selection', e);
        }
    }

    const selection = loadSelection();

    function syncPageCheckboxes() {
        $('.user-select').each(function () {
            const id = String($(this).val());
            $(this).prop('checked', selection.has(id));
        });
    }

    function updateCountUI() {
        syncPageCheckboxes();
        const total = selection.size;
        $count.text(total);
        const hasSelection = total > 0;
        $bulkBtn.prop('disabled', !hasSelection);
        const pageIds = $('.user-select').map(function () { return String($(this).val()); }).get();
        const allOnPageSelected = pageIds.length > 0 && pageIds.every((id) => selection.has(id));
        $selectAll.prop('checked', allOnPageSelected);
    }

    function openModal(userIds) {
        $title.text(`${userIds.length} user${userIds.length === 1 ? '' : 's'} selected`);
        $radios.prop('checked', false);
        $campaignSelect.val('');
        $updateUrl.val($bulkBtn.data('update-url'));
        $userIdsField.val(userIds.join(','));
        $feedback.addClass('hidden').text('');
        $modal.removeClass('hidden').addClass('flex');
    }

    function closeModal() {
        $modal.addClass('hidden').removeClass('flex');
    }

    // Selection handlers
    $(document).on('change', '.user-select', function () {
        const id = String($(this).val());
        if ($(this).is(':checked')) {
            selection.add(id);
        } else {
            selection.delete(id);
        }
        saveSelection(selection);
        updateCountUI();
    });

    $selectAll.on('change', function () {
        const shouldSelect = $(this).is(':checked');
        $('.user-select').each(function () {
            const id = String($(this).val());
            if (shouldSelect) {
                selection.add(id);
                $(this).prop('checked', true);
            } else {
                selection.delete(id);
                $(this).prop('checked', false);
            }
        });
        saveSelection(selection);
        updateCountUI();
    });

    $bulkBtn.on('click', function () {
        const ids = Array.from(selection);
        if (!ids.length) return;
        openModal(ids);
    });

    $cancelBtn.on('click', closeModal);
    $closeBtn.on('click', closeModal);
    $modal.on('click', function (e) { if (e.target === this) closeModal(); });

    $form.on('submit', async function (e) {
        e.preventDefault();
        const updateUrlVal = $updateUrl.val();
        const campaignId = $campaignSelect.val();
        const accessLevel = $form.find('input[name="access_level"]:checked').val();
        const userIds = $userIdsField.val().split(',').filter(Boolean);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!updateUrlVal || !csrfToken || !campaignId || !accessLevel || !userIds.length) {
            $feedback.removeClass('hidden').text('Campaign, access level, and selected users are required.');
            return;
        }

        $feedback.addClass('hidden');
        $saveBtn.prop('disabled', true).text('Assigning...');

        try {
            const response = await fetch(updateUrlVal, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    campaign_id: campaignId,
                    access_level: accessLevel,
                    user_ids: userIds,
                }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                const message = data?.message || 'Unable to assign campaign.';
                $feedback.removeClass('hidden').addClass('text-red-600').text(message);
                return;
            }

            // Clear persisted selection after success
            selection.clear();
            saveSelection(selection);
            window.location.reload();
        } catch (error) {
            console.error('Failed to assign campaign', error);
            $feedback.removeClass('hidden').addClass('text-red-600').text('Unexpected error. Please try again.');
        } finally {
            $saveBtn.prop('disabled', false).text('Assign');
        }
    });

    // Initialize count
    updateCountUI();
});
