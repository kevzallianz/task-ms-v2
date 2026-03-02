$(document).ready(function () {
    // Filter function
    function filterTasks(campaignId) {
        const searchTerm = $(`.campaign-task-search[data-campaign-id="${campaignId}"]`).val().toLowerCase();
        const statusFilter = $(`.campaign-status-filter[data-campaign-id="${campaignId}"]`).val();
        const memberFilter = $(`.campaign-member-filter[data-campaign-id="${campaignId}"]`).val();
        const monthFilter = $(`.campaign-month-filter[data-campaign-id="${campaignId}"]`).val();
        const yearFilter = $(`.campaign-year-filter[data-campaign-id="${campaignId}"]`).val();

        let visibleCount = 0;
        let totalCount = 0;

        $(`.campaign-task-row[data-campaign-id="${campaignId}"]`).each(function () {
            const $row = $(this);
            totalCount++;

            const title = $row.data('task-title') || '';
            const description = $row.data('task-description') || '';
            const status = $row.data('task-status') || '';
            const memberIds = String($row.data('task-member-ids') || '');
            const startDate = String($row.data('task-start-date') || '');

            // Search filter
            let matchesSearch = true;
            if (searchTerm) {
                matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            }

            // Status filter
            let matchesStatus = true;
            if (statusFilter) {
                matchesStatus = status === statusFilter;
            }

            // Member filter
            let matchesMember = true;
            if (memberFilter) {
                const memberIdArray = memberIds.split(',').filter(id => id);
                matchesMember = memberIdArray.includes(memberFilter);
            }

            // Month filter (filter by start date month)
            let matchesMonth = true;
            if (monthFilter) {
                if (!startDate) {
                    matchesMonth = false;
                } else {
                    const parts = startDate.split('-');
                    if (parts.length < 2) {
                        matchesMonth = false;
                    } else {
                        const month = parseInt(parts[1], 10);
                        matchesMonth = month === parseInt(monthFilter, 10);
                    }
                }
            }

            // Year filter (filter by start date year)
            let matchesYear = true;
            if (yearFilter) {
                if (!startDate) {
                    matchesYear = false;
                } else {
                    const parts = startDate.split('-');
                    if (parts.length < 1) {
                        matchesYear = false;
                    } else {
                        const year = parseInt(parts[0], 10);
                        matchesYear = year === parseInt(yearFilter, 10);
                    }
                }
            }

            // Show/hide row
            if (matchesSearch && matchesStatus && matchesMember && matchesMonth && matchesYear) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });

        // Update count
        $(`.campaign-task-count[data-campaign-id="${campaignId}"]`).text(
            `Showing ${visibleCount} of ${totalCount} tasks`
        );

        // Show "No results" message if needed
        const $panel = $(`[data-campaign-panel="${campaignId}"]`);
        const $noResults = $panel.find('.no-filter-results');
        
        if (visibleCount === 0 && totalCount > 0) {
            if ($noResults.length === 0) {
                $panel.find('table').after(`
                    <div class="no-filter-results flex flex-col items-center justify-center py-8 text-center">
                        <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">No tasks match your filters</p>
                    </div>
                `);
            }
            $panel.find('table').hide();
        } else {
            $noResults.remove();
            $panel.find('table').show();
        }
    }

    // Search input handler
    $(document).on('input', '.campaign-task-search', function () {
        const campaignId = $(this).data('campaign-id');
        filterTasks(campaignId);
    });

    // Status filter handler
    $(document).on('change', '.campaign-status-filter', function () {
        const campaignId = $(this).data('campaign-id');
        filterTasks(campaignId);
    });

    // Member filter handler
    $(document).on('change', '.campaign-member-filter', function () {
        const campaignId = $(this).data('campaign-id');
        filterTasks(campaignId);
    });

    // Month filter handler
    $(document).on('change', '.campaign-month-filter', function () {
        const campaignId = $(this).data('campaign-id');
        filterTasks(campaignId);
    });

    // Year filter handler
    $(document).on('change', '.campaign-year-filter', function () {
        const campaignId = $(this).data('campaign-id');
        filterTasks(campaignId);
    });

    // Clear filters handler
    $(document).on('click', '.campaign-clear-filters', function () {
        const campaignId = $(this).data('campaign-id');
        
        // Reset all filters
        $(`.campaign-task-search[data-campaign-id="${campaignId}"]`).val('');
        $(`.campaign-status-filter[data-campaign-id="${campaignId}"]`).val('');
        $(`.campaign-member-filter[data-campaign-id="${campaignId}"]`).val('');
        $(`.campaign-month-filter[data-campaign-id="${campaignId}"]`).val('');
        $(`.campaign-year-filter[data-campaign-id="${campaignId}"]`).val('');
        
        // Show all tasks
        filterTasks(campaignId);
    });
});
