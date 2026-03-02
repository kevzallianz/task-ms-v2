/**
 * Campaign task remarks modal handler
 */

document.addEventListener('DOMContentLoaded', () => {
    const campaignIdInput = document.querySelector('[data-campaign-tab]');
    // We'll derive campaignId from visible panel when opening modal

    const $modal = document.getElementById('campaignTaskRemarksModal');
    if (! $modal) return;

    const $loading = document.getElementById('campaignRemarksLoading');
    const $container = document.getElementById('campaignRemarksContainer');
    const $empty = document.getElementById('campaignRemarksEmpty');
    const $taskId = document.getElementById('campaignRemarksTaskId');
    const $taskTitle = document.getElementById('campaignRemarksTaskTitle');
    const $form = document.getElementById('campaignAddRemarkForm');
    const $textarea = document.getElementById('campaignRemarkText');
    const $charCount = document.getElementById('campaignRemarkCharCount');
    const $submitBtn = document.getElementById('campaignSubmitRemarkBtn');
    const $submitBtnText = document.getElementById('campaignSubmitRemarkBtnText');
    const $submitBtnSpinner = document.getElementById('campaignSubmitRemarkBtnSpinner');
    const $remarkError = document.getElementById('campaignRemarkError');

    function openModal(taskId, title) {
        $taskId.value = taskId;
        $taskTitle.textContent = `Viewing remarks for: ${title}`;
        $textarea.value = '';
        $charCount.textContent = '0';
        $remarkError.textContent = '';
        $remarkError.classList.add('hidden');
        $modal.classList.remove('hidden');
        $modal.classList.add('flex');
        fetchRemarks(taskId);
    }

    function closeModal() {
        $modal.classList.add('hidden');
        $modal.classList.remove('flex');
        $container.innerHTML = '';
        $container.classList.add('hidden');
        $empty.classList.add('hidden');
        $loading.classList.remove('hidden');
    }

    function getActiveCampaignId() {
        const panel = document.querySelector('[data-campaign-panel]:not(.hidden)');
        return panel ? panel.getAttribute('data-campaign-panel') : null;
    }

    function fetchRemarks(taskId) {
        $loading.classList.remove('hidden');
        $container.classList.add('hidden');
        $empty.classList.add('hidden');

        const campaignId = getActiveCampaignId();
        if (!campaignId) {
            handleError();
            return;
        }

        fetch(`/campaigns/${campaignId}/tasks/${taskId}/remarks`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        }).then(r => r.json())
        .then(handleSuccess)
        .catch(handleError);
    }

    function handleSuccess(response) {
        $loading.classList.add('hidden');
        const remarks = response.remarks || [];

        if (remarks.length === 0) {
            $empty.classList.remove('hidden');
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
                            <p class="text-sm font-semibold text-foreground">${escapeHtml(remark.user.name)}</p>
                            <span class="text-xs text-gray-500">${createdAt}</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap wrap-break-word">${escapeHtml(remark.remarks)}</p>
                </div>
            `;
        });

        $container.innerHTML = html;
        $container.classList.remove('hidden');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function handleError() {
        $loading.classList.add('hidden');
        // simple alert for now
        alert('Failed to load remarks');
    }

    function setSubmitLoading(isLoading) {
        $submitBtn.disabled = isLoading;
        $submitBtnText.textContent = isLoading ? 'Posting...' : 'Post Remark';
        if (isLoading) $submitBtnSpinner.classList.remove('hidden'); else $submitBtnSpinner.classList.add('hidden');
    }

    // Character counter
    $textarea.addEventListener('input', () => {
        $charCount.textContent = $textarea.value.length;
    });

    // Submit remark
    $form.addEventListener('submit', (e) => {
        e.preventDefault();
        $remarkError.textContent = '';
        $remarkError.classList.add('hidden');

        const taskId = $taskId.value;
        const remark = $textarea.value.trim();
        if (!remark) {
            $remarkError.textContent = 'Please enter a remark';
            $remarkError.classList.remove('hidden');
            return;
        }

        setSubmitLoading(true);
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const formData = new FormData();
        formData.append('remark', remark);

        const campaignId = getActiveCampaignId();
        if (!campaignId) {
            alert('No campaign selected');
            setSubmitLoading(false);
            return;
        }

        fetch(`/campaigns/${campaignId}/tasks/${taskId}/remarks`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        }).then(async (r) => {
            const data = await r.json().catch(() => null);
            if (r.ok) {
                alert(data?.message || 'Remark added successfully');
                $textarea.value = '';
                $charCount.textContent = '0';
                fetchRemarks(taskId);
            } else {
                const msg = data?.message || 'Failed to add remark';
                $remarkError.textContent = msg;
                $remarkError.classList.remove('hidden');
                alert(msg);
            }
        }).catch((err) => {
            console.error(err);
            alert('Failed to add remark');
        }).finally(() => setSubmitLoading(false));
    });

    // Open from task buttons
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.taskActionBtn[data-action="remarks"]');
        if (!btn) return;
        const taskId = btn.getAttribute('data-task-id');
        const taskTitle = btn.getAttribute('data-task-title');
        openModal(taskId, taskTitle);
    });

    // Close handlers
    document.getElementById('closeCampaignRemarksModal').addEventListener('click', closeModal);
    document.getElementById('campaignCloseRemarksBtn').addEventListener('click', closeModal);
    $modal.addEventListener('click', (e) => { if (e.target === $modal) closeModal(); });
});
