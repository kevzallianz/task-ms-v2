document.addEventListener('DOMContentLoaded', () => {
    const importBtn = document.getElementById('importBtn');
    const importModal = document.getElementById('importModal');
    const importModalClose = document.getElementById('importModalClose');
    const importFileInput = document.getElementById('importFileInput');
    const importPreviewBtn = document.getElementById('importPreviewBtn');
    const importPreviewContainer = document.getElementById('importPreviewContainer');
    const importPreviewTable = document.getElementById('importPreviewTable');
    const importCancelBtn = document.getElementById('importCancelBtn');
    const importConfirmBtn = document.getElementById('importConfirmBtn');

    if (!importBtn || !importModal) return;

    function openModal() {
        importModal.classList.remove('hidden');
        importPreviewContainer.classList.add('hidden');
        importPreviewTable.innerHTML = '';
        importFileInput.value = '';
    }

    function closeModal() {
        importModal.classList.add('hidden');
    }

    const importLoadingOverlay = document.getElementById('importLoadingOverlay');
    function setLoading(isLoading) {
        if (!importLoadingOverlay) return;
        if (isLoading) {
            importLoadingOverlay.classList.remove('hidden');
            importPreviewBtn.disabled = true;
            importConfirmBtn.disabled = true;
            importFileInput.disabled = true;
            importModal.setAttribute('aria-busy', 'true');
        } else {
            importLoadingOverlay.classList.add('hidden');
            importPreviewBtn.disabled = false;
            importConfirmBtn.disabled = false;
            importFileInput.disabled = false;
            importModal.removeAttribute('aria-busy');
        }
    }

    importBtn.addEventListener('click', openModal);
    importModalClose.addEventListener('click', closeModal);
    importCancelBtn.addEventListener('click', closeModal);
    // Close when clicking backdrop
    const importModalBackdrop = document.getElementById('importModalBackdrop');
    if (importModalBackdrop) {
        importModalBackdrop.addEventListener('click', closeModal);
    }

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && importModal && !importModal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Helper to get active campaign id from visible panel
    function getActiveCampaignId() {
        const panel = document.querySelector('[data-campaign-panel]:not(.hidden)');
        return panel ? panel.getAttribute('data-campaign-panel') : null;
    }

    function renderPreviewTable(rows) {
        // Build header from keys
        if (!rows || !rows.length) return;
        const keys = Object.keys(rows[0]);
        let html = '<thead class="bg-gray-50 border-b"><tr>' + keys.map(k => `<th class="px-3 py-2 text-left font-semibold text-xs">${k}</th>`).join('') + '</tr></thead>';
        html += '<tbody>';
        rows.forEach(r => {
            html += '<tr>' + keys.map(k => `<td class="px-3 py-2 align-top text-xs">${(r[k] !== undefined && r[k] !== null) ? String(r[k]) : ''}</td>`).join('') + '</tr>';
        });
        html += '</tbody>';
        importPreviewTable.innerHTML = html;
        importPreviewContainer.classList.remove('hidden');
    }

    importPreviewBtn.addEventListener('click', () => {
        const file = importFileInput.files[0];
        if (!file) {
            alert('Please choose a file to preview.');
            return;
        }
        setLoading(true);
        const reader = new FileReader();
        reader.onload = (e) => {
            const data = e.target.result;
            let workbook;
            try {
                workbook = XLSX.read(data, { type: 'binary', cellDates: true });
            } catch (err) {
                // try CSV parse
                try {
                    const csv = new TextDecoder().decode(data);
                    const ws = XLSX.utils.sheet_to_json(XLSX.utils.aoa_to_sheet([csv.split('\n')[0].split(',')]));
                    renderPreviewTable([]);
                    setLoading(false);
                    return;
                } catch (e) {
                    alert('Could not parse file.');
                    setLoading(false);
                    return;
                }
            }

            const sheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[sheetName];
            const json = XLSX.utils.sheet_to_json(worksheet, { defval: '', raw: false, dateNF: 'yyyy-mm-dd' });

            // Normalize columns to lowercase keys (preview all rows)
            const normalized = json.map(row => {
                const out = {};
                Object.keys(row).forEach(k => {
                    const key = String(k).trim().toLowerCase().replace(/\s+/g, '_');
                    // ignore month and campaign columns
                    if (key === 'month' || key === 'campaign') return;
                    out[key] = row[k];
                });
                return out;
            });

            // Build a display version for preview that ensures a `title` column exists
            const TITLE_MAX = 50;
            const displayRows = normalized.map(row => {
                const copy = Object.assign({}, row);
                // if there's no explicit title, use description/task as title for preview
                if (!copy.title || String(copy.title).trim() === '') {
                    const candidate = copy.task || copy.description || copy.details || '';
                    if (candidate) {
                        const s = String(candidate).trim();
                        copy.title = s.length > TITLE_MAX ? s.slice(0, TITLE_MAX) : s;
                    } else {
                        copy.title = '';
                    }
                } else if (String(copy.title).length > TITLE_MAX) {
                    copy.title = String(copy.title).slice(0, TITLE_MAX);
                }
                return copy;
            });

            renderPreviewTable(displayRows);
            // store normalized preview for later import (ignore Month/Campaign)
            importPreviewContainer.dataset.preview = JSON.stringify(normalized);
            setLoading(false);
        };

        // Read as binary string so xlsx can parse both csv/xlsx
        reader.readAsBinaryString(file);
    });

    importConfirmBtn.addEventListener('click', async () => {
        const previewRaw = importPreviewContainer.dataset.preview;
        if (!previewRaw) {
            alert('Please preview a file first.');
            return;
        }

        let rows = JSON.parse(previewRaw);
        // Convert rows into expected shape
        const tasks = rows.map(r => {
            const normalized = {};
            // map possible keys to expected fields
            const mapKey = (names) => {
                for (const n of names) {
                    const foundKey = Object.keys(r).find(k => k.toLowerCase().replace(/\s+/g,'_') === n);
                    if (foundKey) return r[foundKey];
                }
                return '';
            };

            // Owner column in your CSV is the assigned member(s)
            const assignedRaw = mapKey(['assigned_members', 'assigned', 'assigned_member', 'members', 'owner', 'owner_name', 'owner(s)']);
            const assignedArr = assignedRaw ? String(assignedRaw).split(/[,;\n&]+/).map(s => s.trim()).filter(Boolean) : [];

            // normalize status variants to allowed values
            const rawStatus = (mapKey(['status']) || 'planning').toString().toLowerCase().trim();
            const mapStatus = (s) => {
                if (!s) return 'planning';
                if (['accomplished', 'completed', 'done'].includes(s)) return 'accomplished';
                if (['in_progress', 'in progress', 'inprogress', 'ongoing', 'pending', 'todo', 'to_do'].includes(s)) return 'ongoing';
                if (['on_hold', 'on-hold', 'on hold', 'hold', 'delayed'].includes(s)) return 'on_hold';
                if (['planning', 'plan'].includes(s)) return 'planning';
                return 'planning';
            };

            const extractYearFromString = (s) => {
                if (!s) return null;
                const m = String(s).match(/(\d{4})/);
                if (m) {
                    const y = parseInt(m[1], 10);
                    if (y >= 1900 && y <= 2100) return y;
                }
                return null;
            };

            const normalizeDate = (val, fallbackYear = null) => {
                if (val === null || val === undefined || val === '') return null;
                if (val instanceof Date && !isNaN(val)) {
                    const iso = val.toISOString().slice(0,10);
                    return iso;
                }
                if (typeof val === 'number') {
                    try {
                        if (XLSX.SSF && typeof XLSX.SSF.parse_date_code === 'function') {
                            const parsed = XLSX.SSF.parse_date_code(val);
                            if (parsed) {
                                let d = new Date(Date.UTC(parsed.y, parsed.m - 1, parsed.d));
                                // correct implausible year if needed
                                if (d.getFullYear() < 1900) {
                                    if (fallbackYear) d.setUTCFullYear(fallbackYear);
                                    else if (parsed.y >= 1000 && parsed.y < 2000) d.setUTCFullYear(parsed.y + 1000);
                                }
                                return d.toISOString().slice(0,10);
                            }
                        }
                        let d = new Date((val - 25569) * 86400 * 1000);
                        if (!isNaN(d)) {
                            if (d.getFullYear() < 1900) {
                                if (fallbackYear) d.setUTCFullYear(fallbackYear);
                                else if (d.getUTCFullYear() >= 1000 && d.getUTCFullYear() < 2000) d.setUTCFullYear(d.getUTCFullYear() + 1000);
                            }
                            return d.toISOString().slice(0,10);
                        }
                    } catch (e) {
                        return null;
                    }
                }
                const s = String(val).trim();
                const isoMatch = s.match(/(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})/);
                if (isoMatch) {
                    let y = parseInt(isoMatch[1], 10);
                    const mth = isoMatch[2].padStart(2,'0');
                    const dt = isoMatch[3].padStart(2,'0');
                    if (y < 1900) {
                        if (fallbackYear) y = fallbackYear;
                        else if (y >= 1000 && y < 2000) y = y + 1000;
                    }
                    return `${String(y)}-${mth}-${dt}`;
                }
                const dt = new Date(s);
                if (!isNaN(dt)) {
                    // correct implausible historical years
                    if (dt.getFullYear && dt.getFullYear() < 1900) {
                        if (fallbackYear) dt.setFullYear(fallbackYear);
                        else if (dt.getFullYear() >= 1000 && dt.getFullYear() < 2000) dt.setFullYear(dt.getFullYear() + 1000);
                    }
                    return dt.toISOString().slice(0,10);
                }
                return null;
            };

            normalized.assigned_members = assignedArr;
            // many files put the task title under "Description" — prefer explicit title, fall back to description
            normalized.title = mapKey(['title', 'task', 'description']);
            normalized.description = mapKey(['remarks', 'details']);
            // Ensure title fits DB column (50 chars). If too long, move overflow into description.
            if (normalized.title && String(normalized.title).length > 50) {
                const fullTitle = String(normalized.title).trim();
                const short = fullTitle.slice(0, 50);
                const rest = fullTitle.slice(50).trim();
                normalized.title = short;
                if (!normalized.description) {
                    normalized.description = rest || null;
                } else if (rest) {
                    normalized.description = String(normalized.description) + '\n\n' + rest;
                }
            }
            const rawStart = mapKey(['start_date', 'start']);
            const startIso = normalizeDate(rawStart);
            let fallbackYear = null;
            if (startIso) {
                const y = parseInt(startIso.slice(0,4), 10);
                if (!isNaN(y)) fallbackYear = y;
            } else {
                fallbackYear = extractYearFromString(rawStart);
            }

            normalized.start_date = startIso;
            normalized.target_date = normalizeDate(mapKey(['target_date', 'target', 'due_date']), fallbackYear);
            normalized.status = mapStatus(rawStatus);
            normalized.completed_at = normalizeDate(mapKey(['completed_at', 'completed', 'date_completed']), fallbackYear);
            return normalized;
        });

        // Basic client-side validation
        if (!tasks.length) {
            alert('No tasks to import');
            return;
        }

        const campaignId = getActiveCampaignId();
        if (!campaignId) {
            alert('No campaign selected');
            return;
        }

        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = tokenMeta ? tokenMeta.getAttribute('content') : null;

        try {
            setLoading(true);
            const res = await fetch(`/campaigns/${campaignId}/import`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                },
                body: JSON.stringify({ tasks }),
            });

            // attempt to parse json, but show text if not json
            const text = await res.text();
            let data = null;
            try { data = JSON.parse(text); } catch (e) { data = null; }

            if (res.ok && data && data.success) {
                closeModal();
                window.location.reload();
            } else {
                const msg = (data && (data.message || (data.errors && Object.values(data.errors).flat().join('; ')))) || text || 'Import failed.';
                alert(msg);
            }
        } catch (err) {
            console.error(err);
            alert('An error occurred while importing.');
        } finally {
            setLoading(false);
        }
    });
});
