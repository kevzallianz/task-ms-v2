document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('projectTaskSearch');
    const statusFilter = document.getElementById('projectStatusFilter');
    const dateFilter = document.getElementById('projectDateFilter');
    const monthFilter = document.getElementById('projectMonthFilter');
    const yearFilter = document.getElementById('projectYearFilter');
    const memberFilter = document.getElementById('projectMemberFilter');
    const clearFiltersBtn = document.getElementById('projectClearFilters');
    const taskRows = document.querySelectorAll('.campaign-task-row');
    const taskCount = document.getElementById('taskCount');
    const taskFilterCount = document.getElementById('taskFilterCount');
    const totalTasks = taskRows.length;

    function applyFilters() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedStatus = statusFilter ? statusFilter.value : '';
        const selectedDate = dateFilter ? dateFilter.value : '';
        const selectedMonth = monthFilter ? monthFilter.value : '';
        const selectedYear = yearFilter ? yearFilter.value : '';
        const selectedMember = memberFilter ? memberFilter.value : '';
        
        let visibleCount = 0;

        taskRows.forEach(row => {
            const title = row.getAttribute('data-task-title') || '';
            const description = row.getAttribute('data-task-description') || '';
            const status = row.getAttribute('data-task-status') || '';
            const startDate = row.getAttribute('data-task-start-date') || '';
            const memberIds = row.getAttribute('data-task-member-ids') || '';
            
            // Check search
            const matchesSearch = !searchTerm || title.includes(searchTerm) || description.includes(searchTerm);
            
            // Check status
            const matchesStatus = !selectedStatus || status === selectedStatus;
            
            // Check exact date
            const matchesDate = !selectedDate || startDate === selectedDate;
            
            // Check month and year
            let matchesMonth = true;
            let matchesYear = true;
            if (startDate && (selectedMonth || selectedYear)) {
                const dateParts = startDate.split('-'); // Format: YYYY-MM-DD
                if (dateParts.length === 3) {
                    const taskYear = dateParts[0];
                    const taskMonth = parseInt(dateParts[1], 10).toString(); // Remove leading zero
                    
                    if (selectedMonth) {
                        matchesMonth = taskMonth === selectedMonth;
                    }
                    if (selectedYear) {
                        matchesYear = taskYear === selectedYear;
                    }
                }
            } else if (!startDate && (selectedMonth || selectedYear)) {
                matchesMonth = false;
                matchesYear = false;
            }
            
            // Check member
            const matchesMember = !selectedMember || memberIds.split(',').includes(selectedMember);
            
            const isVisible = matchesSearch && matchesStatus && matchesDate && matchesMonth && matchesYear && matchesMember;
            
            if (isVisible) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update counts
        if (taskCount) {
            taskCount.textContent = `${visibleCount} of ${totalTasks} task(s)`;
        }
        if (taskFilterCount) {
            taskFilterCount.textContent = `Showing ${visibleCount} of ${totalTasks} tasks`;
        }
    }

    // Attach event listeners
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    if (dateFilter) {
        dateFilter.addEventListener('change', applyFilters);
    }
    if (monthFilter) {
        monthFilter.addEventListener('change', applyFilters);
    }
    if (yearFilter) {
        yearFilter.addEventListener('change', applyFilters);
    }
    if (memberFilter) {
        memberFilter.addEventListener('change', applyFilters);
    }
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            if (dateFilter) dateFilter.value = '';
            if (monthFilter) monthFilter.value = '';
            if (yearFilter) yearFilter.value = '';
            if (memberFilter) memberFilter.value = '';
            applyFilters();
        });
    }
});
