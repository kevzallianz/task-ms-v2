<!-- Member Contributions Modal -->
<div id="memberContributionsModal" class="fixed flex inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full mx-4">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-secondary/20">
            <div>
                <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                    <x-heroicon-o-chart-bar-square class="w-5 h-5" />
                    Team Contributions
                </h2>
                <p class="text-sm text-gray-600">View how much each team member has accomplished</p>
            </div>
            <button type="button" id="memberContributionsModalClose" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6">
            <!-- Loading State -->
            <div id="contributionsLoading" class="flex items-center justify-center py-12">
                <div class="flex items-center gap-3">
                    <svg class="animate-spin w-5 h-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span class="text-sm text-gray-700">Loading contributions...</span>
                </div>
            </div>

            <!-- Content Container -->
            <div id="contributionsContent" class="hidden space-y-6">
                <!-- Tasks Chart -->
                <div>
                    <h3 class="text-base font-semibold text-foreground mb-4 flex items-center gap-2">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-600" />
                        Accomplished Tasks
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <canvas id="tasksChart" class="max-h-80"></canvas>
                    </div>
                </div>

                <!-- Members Table -->
                <div>
                    <h3 class="text-base font-semibold text-foreground mb-4 flex items-center gap-2">
                        <x-heroicon-o-user-group class="w-5 h-5 text-blue-600" />
                        Member Details
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-secondary/20">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Member</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Access Level</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Accomplished Tasks</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Task Completion Rate</th>
                                </tr>
                            </thead>
                            <tbody id="contributionTableBody">
                                <!-- Table rows will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="emptyContributions" class="hidden flex flex-col items-center justify-center py-12 text-center">
                    <x-heroicon-o-user-group class="w-12 h-12 text-gray-300 mb-2" />
                    <p class="text-sm text-gray-500">No contributions yet.</p>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-secondary/20">
            <button type="button" id="memberContributionsCancel" class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">Close</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('memberContributionsModal');
        const closeBtn = document.getElementById('memberContributionsModalClose');
        const cancelBtn = document.getElementById('memberContributionsCancel');
        let tasksChart = null;

        if (!modal) return;

        function close() {
            modal.classList.add('hidden');
        }

        function open(campaignId) {
            modal.classList.remove('hidden');
            loadContributions(campaignId);
        }

        function loadContributions(campaignId) {
            const loading = document.getElementById('contributionsLoading');
            const content = document.getElementById('contributionsContent');
            const empty = document.getElementById('emptyContributions');

            loading.classList.remove('hidden');
            content.classList.add('hidden');
            empty.classList.add('hidden');

            fetch(`/campaigns/${campaignId}/member-contributions`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.members && data.members.length > 0) {
                    renderContributions(data.members);
                    loading.classList.add('hidden');
                    content.classList.remove('hidden');
                } else {
                    loading.classList.add('hidden');
                    empty.classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error('Error loading contributions:', err);
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
                showToast('error', 'Failed to load contributions');
            });
        }

        function renderContributions(members) {
            // Render chart
            const taskLabels = members.map(m => m.name);
            const accomplishedTasksData = members.map(m => m.accomplished_tasks);
            const totalTasksData = members.map(m => m.total_tasks);

            const ctx = document.getElementById('tasksChart')?.getContext('2d');
            if (ctx) {
                if (tasksChart) {
                    tasksChart.destroy();
                }

                tasksChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: taskLabels,
                        datasets: [
                            {
                                label: 'Accomplished Tasks',
                                data: accomplishedTasksData,
                                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 1,
                            },
                            {
                                label: 'Total Tasks',
                                data: totalTasksData,
                                backgroundColor: 'rgba(156, 163, 175, 0.4)',
                                borderColor: 'rgba(156, 163, 175, 1)',
                                borderWidth: 1,
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                }
                            }
                        }
                    }
                });
            }

            // Render table
            const tbody = document.getElementById('contributionTableBody');
            tbody.innerHTML = members.map(member => {
                const completionRate = member.total_tasks > 0
                    ? Math.round((member.accomplished_tasks / member.total_tasks) * 100)
                    : 0;

                const accessBadgeClass = {
                    'viewer': 'bg-gray-100 text-gray-700',
                    'editor': 'bg-blue-100 text-blue-700',
                    'all': 'bg-indigo-100 text-indigo-700',
                }[member.access_level] || 'bg-gray-100 text-gray-700';

                return `
                    <tr class="border-b border-secondary/10 hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold text-primary overflow-hidden">
                                    ${member.avatar ? `<img src="${member.avatar}" alt="${member.name}" class="w-full h-full object-cover" loading="lazy" />` : member.avatar_initial}
                                </div>
                                <div>
                                    <p class="font-medium text-foreground">${member.name}</p>
                                    <p class="text-xs text-gray-500">${member.email}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap ${accessBadgeClass}">
                                ${member.access_level.charAt(0).toUpperCase() + member.access_level.slice(1)}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="text-base font-semibold text-green-600">${member.accomplished_tasks}/${member.total_tasks}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-full max-w-xs bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: ${completionRate}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 min-w-12">${completionRate}%</span>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Event listeners
        if (closeBtn) closeBtn.addEventListener('click', close);
        if (cancelBtn) cancelBtn.addEventListener('click', close);

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                close();
            }
        });

        // Expose open function globally
        window.openMemberContributions = open;
    });
</script>
