document.addEventListener('DOMContentLoaded', () => {
    // Helpers
    const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Toggle row selection when row clicked (ignore clicks on buttons/inputs)
    document.addEventListener('click', (e) => {
        const row = e.target.closest('.campaign-task-row');
        if (!row) return;
        const targetTag = e.target.tagName.toLowerCase();
        if (['button', 'svg', 'path', 'input', 'a', 'label'].includes(targetTag)) return;

        // Toggle selection class
        row.classList.toggle('selected-for-assign');
    });

    // Provide a helper to get selected rows by class
    function getSelectedTaskRows() {
        return Array.from(document.querySelectorAll('.campaign-task-row.selected-for-assign'));
    }

    // Open bulk assign modal
    const bulkAssignBtn = document.getElementById('bulkAssignBtn');
    if (!bulkAssignBtn) return;

    // Create modal markup (match add/edit task member UI)
    const modalHtml = `
    <div id="bulkAssignModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 w-full max-w-2xl bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-semibold">Assign Members to Selected Tasks</h3>
                <button id="bulkAssignClose" class="text-secondary p-2">✕</button>
            </div>
            <div class="p-4 space-y-3">
                <p class="text-sm text-gray-600">Click members to add/remove. Selected members appear as badges above.</p>

                <div id="bulkSelectedMemberBadges" class="flex flex-wrap gap-2 mb-2 min-h-8 p-2 border border-secondary/30 rounded-lg bg-white">
                    <span class="text-xs text-gray-400 italic" id="bulkNoSelectionText">No members selected</span>
                </div>

                <div class="text-xs text-gray-600 mb-1">Click to add:</div>
                <div id="bulkAssignMembersList" class="space-y-3 max-h-64 overflow-auto border border-secondary/30 rounded-lg p-3 bg-gray-50">
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button id="bulkAssignCancel" class="px-4 py-2 text-sm bg-white border rounded">Cancel</button>
                    <button id="bulkAssignConfirm" class="px-4 py-2 text-sm bg-primary text-white rounded">Assign Members</button>
                </div>
            </div>
        </div>
        <!-- Loading overlay inside modal -->
        <div id="bulkAssignLoadingOverlay" class="hidden absolute inset-0 z-20 flex items-center justify-center bg-white/70">
            <div class="flex items-center gap-3 bg-white/0 p-4 rounded">
                <svg class="animate-spin w-5 h-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span class="text-sm text-gray-700">Assigning…</span>
            </div>
        </div>
    </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const bulkAssignModal = document.getElementById('bulkAssignModal');
    const bulkAssignClose = document.getElementById('bulkAssignClose');
    const bulkAssignCancel = document.getElementById('bulkAssignCancel');
    const bulkAssignConfirm = document.getElementById('bulkAssignConfirm');
    const bulkAssignMembersList = document.getElementById('bulkAssignMembersList');
    const bulkSelectedMemberBadges = document.getElementById('bulkSelectedMemberBadges');
    const bulkNoSelectionText = document.getElementById('bulkNoSelectionText');
    const bulkAssignLoadingOverlay = document.getElementById('bulkAssignLoadingOverlay');

    let selectedMembers = [];

    function updateBulkSelectedBadges() {
        bulkSelectedMemberBadges.innerHTML = '';
        if (!selectedMembers.length) {
            bulkSelectedMemberBadges.appendChild(bulkNoSelectionText);
            return;
        }
        selectedMembers.forEach(m => {
            const span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1 px-3 py-1 bg-primary/10 border border-primary/30 text-primary rounded-full text-sm';
            span.innerHTML = `${escapeHtml(m.name)} <button type="button" class="bulk-remove-member ml-2" data-id="${m.id}">✕</button>`;
            bulkSelectedMemberBadges.appendChild(span);
        });

        // attach remove handlers
        bulkSelectedMemberBadges.querySelectorAll('.bulk-remove-member').forEach(btn => {
            btn.addEventListener('click', (ev) => {
                const id = btn.getAttribute('data-id');
                selectedMembers = selectedMembers.filter(s => String(s.id) !== String(id));
                updateBulkSelectedBadges();
                // also toggle member item active state
                const item = bulkAssignMembersList.querySelector(`[data-member-id='${id}']`);
                if (item) item.classList.remove('active');
            });
        });
    }

    function addBulkMemberSelection(id, name) {
        if (selectedMembers.some(m => String(m.id) === String(id))) return;
        selectedMembers.push({ id, name });
        updateBulkSelectedBadges();
    }

    function removeBulkMemberSelection(id) {
        selectedMembers = selectedMembers.filter(m => String(m.id) !== String(id));
        updateBulkSelectedBadges();
    }

    // Escape helper (simple)
    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // NOTE: getSelectedTaskRows is defined above (uses .selected-for-assign class)

    function getActiveCampaignPanel() {
        return document.querySelector('[data-campaign-panel]:not(.hidden)');
    }

    function openModal() {
        const rows = getSelectedTaskRows();
        if (!rows.length) {
            alert('Please select one or more tasks to assign members to.');
            return;
        }

        // populate members from active campaign members panel
        const activePanel = getActiveCampaignPanel();
        if (!activePanel) {
            alert('No active campaign found.');
            return;
        }
        const campaignId = activePanel.getAttribute('data-campaign-panel');
        const membersPanel = document.querySelector(`[data-campaign-panel-members="${campaignId}"]`);
        if (!membersPanel) {
            alert('No members found for this campaign.');
            return;
        }

        // build member list using same clickable items as add/edit task
        bulkAssignMembersList.innerHTML = '';
        selectedMembers = [];
        updateBulkSelectedBadges();
        const memberEls = membersPanel.querySelectorAll('[data-campaign-member-id]');

        // Determine already-assigned user ids for selected tasks (intersection)
        const selectedRows = getSelectedTaskRows();
        const assignedLists = selectedRows.map(r => {
            const raw = r.getAttribute('data-task-member-ids') || '';
            return raw.split(',').map(s => s.trim()).filter(Boolean);
        });
        let assignedIntersection = [];
        if (assignedLists.length === 1) {
            assignedIntersection = assignedLists[0];
        } else if (assignedLists.length > 1) {
            assignedIntersection = assignedLists.reduce((acc, list) => acc.filter(x => list.includes(x)));
        }
        if (!memberEls.length) {
            bulkAssignMembersList.innerHTML = '<p class="text-xs text-gray-600">No members available</p>';
        } else {
            memberEls.forEach(el => {
                const memberId = el.getAttribute('data-campaign-member-id');
                const userId = el.getAttribute('data-user-id');
                const memberNameEl = el.querySelector('.font-medium.text-foreground') || el.querySelector('p');
                const memberName = memberNameEl ? memberNameEl.textContent.trim() : 'Member';
                const isCurrent = (String(userId) === String(document.querySelector('meta[name="user-id"]')?.getAttribute('content')));

                const item = document.createElement('div');
                item.className = 'flex items-center gap-2 p-2 rounded cursor-pointer hover:bg-white transition bulk-member-item';
                item.setAttribute('data-member-id', memberId);
                item.setAttribute('data-member-name', memberName);
                item.innerHTML = `<div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold text-primary shrink-0">${escapeHtml(memberName.charAt(0).toUpperCase())}</div><span class="text-sm text-foreground">${escapeHtml(memberName)}${isCurrent ? ' <span class="text-xs text-gray-500">(you)</span>' : ''}</span>`;

                // If this member's user-id is present in assignedIntersection, pre-select it
                const userIdStr = String(userId);
                if (assignedIntersection && assignedIntersection.length && assignedIntersection.includes(userIdStr)) {
                    item.classList.add('active');
                    // pre-add using campaign_member id
                    addBulkMemberSelection(memberId, memberName);
                }

                item.addEventListener('click', () => {
                    const id = item.getAttribute('data-member-id');
                    const name = item.getAttribute('data-member-name');
                    if (item.classList.contains('active')) {
                        item.classList.remove('active');
                        removeBulkMemberSelection(id);
                    } else {
                        item.classList.add('active');
                        addBulkMemberSelection(id, name);
                    }
                });

                bulkAssignMembersList.appendChild(item);
            });
        }

        bulkAssignModal.classList.remove('hidden');
        bulkAssignModal.classList.add('flex');
    }

    function closeModal() {
        bulkAssignModal.classList.add('hidden');
        bulkAssignModal.classList.remove('flex');
    }

    bulkAssignBtn.addEventListener('click', openModal);
    bulkAssignClose.addEventListener('click', closeModal);
    bulkAssignCancel.addEventListener('click', closeModal);

    bulkAssignConfirm.addEventListener('click', async () => {
        if (!selectedMembers.length) {
            alert('Please choose at least one member to assign.');
            return;
        }

        const memberIds = selectedMembers.map(m => m.id);

        const rows = getSelectedTaskRows();
        if (!rows.length) return;

        const csrf = getCsrf();
        let successCount = 0;
        let failCount = 0;

        // show loading
        setLoading(true);

        // For each selected task, send PUT update with title and status (required by server) and assigned_member_ids
        for (const row of rows) {
            const taskId = row.getAttribute('data-campaign-task-id');
            const campaignId = row.getAttribute('data-campaign-id');

            try {
                const res = await fetch(`/campaigns/${campaignId}/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        assigned_member_ids: memberIds,
                    }),
                });

                if (res.ok) {
                    successCount++;
                } else {
                    failCount++;
                }
            } catch (err) {
                failCount++;
                console.error(err);
            }
        }

        // hide loading
        setLoading(false);

        closeModal();
        if (successCount > 0) {
            window.location.reload();
        } else {
            alert('Failed to assign members to selected tasks.');
        }
    });

    function setLoading(loading) {
        if (bulkAssignLoadingOverlay) {
            bulkAssignLoadingOverlay.classList.toggle('hidden', !loading);
        }
        if (bulkAssignConfirm) bulkAssignConfirm.disabled = loading;
        if (bulkAssignCancel) bulkAssignCancel.disabled = loading;
        if (bulkAssignClose) bulkAssignClose.disabled = loading;
    }
});
