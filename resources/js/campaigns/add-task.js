$(document).ready(function () {
    // DOM Elements
    const $addTaskBtn = $("#addTaskBtn");
    const $addTaskModal = $("#addTaskModal");
    const $closeAddTaskModal = $("#closeAddTaskModal");
    const $cancelAddTaskBtn = $("#cancelAddTaskBtn");
    const $addTaskForm = $("#addTaskForm");
    const $taskCampaignId = $("#taskCampaignId");
    const $taskAssignedMembers = $("#taskAssignedMembers");
    const $selectedMemberBadges = $("#selectedMemberBadges");
    const $noSelectionText = $("#noSelectionText");
    const $submitBtn = $('button[type="submit"][form="addTaskForm"]');
    $addTaskModal.addClass("hidden");

    // Track selected members
    let selectedMembers = [];
    let isSubmitting = false;

    // Open modal on button click
    $addTaskBtn.on("click", function (e) {
        e.preventDefault();
        openModal();
    });

    // Close modal handlers
    $closeAddTaskModal.on("click", closeModal);
    $cancelAddTaskBtn.on("click", closeModal);

    // Close modal when clicking outside
    $addTaskModal.on("click", function (e) {
        if ($(e.target).is(this)) {
            closeModal();
        }
    });

    // Form submission
    $addTaskForm.on("submit", function (e) {
        e.preventDefault();
        submitForm();
    });

    /**
     * Open modal and populate with campaign data
     */
    function openModal() {
        $addTaskModal.removeClass("hidden");
        $addTaskModal.addClass("flex");
        let campaignId = getActiveCampaignId();

        if (campaignId) {
            $taskCampaignId.val(campaignId);
            populateMembersCheckboxes(campaignId);
            $addTaskModal.removeClass("hidden");
        } else {
            console.warn("No campaign found");
        }
    }

    /**
     * Close modal and reset form
     */
    function closeModal() {
        $addTaskModal.addClass("hidden");
        $addTaskForm.trigger("reset");
        selectedMembers = [];
        clearErrors();
    }

    /**
     * Get the currently active campaign ID
     */
    function getActiveCampaignId() {
        // Check for active tab
        const $activeTab = $("[data-campaign-tab].bg-primary");
        if ($activeTab.length > 0) {
            return $activeTab.attr("data-campaign-tab");
        }

        // Fallback to first campaign panel
        return $("[data-campaign-panel]").first().attr("data-campaign-panel");
    }

    /**
     * Populate members checkboxes from visible members panel
     */
    function populateMembersCheckboxes(campaignId) {
        $taskAssignedMembers.html("");
        selectedMembers = [];
        updateSelectedBadges();

        const $membersPanel = $(
            `[data-campaign-panel-members="${campaignId}"]`
        );
        const $memberElements = $membersPanel.find("[data-campaign-member-id]");
        const currentUserId =
            parseInt($addTaskForm.attr("data-current-user-id")) || null;

        if ($memberElements.length === 0) {
            $taskAssignedMembers.html(
                '<p class="text-xs text-gray-600 py-2">No members found</p>'
            );
            return;
        }

        $memberElements.each(function () {
            const $this = $(this);
            const memberName = $this
                .find(".font-medium.text-foreground")
                .text()
                .trim();
            const campaignMemberId = $this.data("campaign-member-id");
            const userId = $this.data("user-id");

            if (campaignMemberId) {
                const isCurrentUser = userId === currentUserId;

                const memberHtml = `
                    <div class="flex items-center gap-2 p-2 rounded cursor-pointer hover:bg-white transition member-item"
                         data-member-id="${campaignMemberId}"
                         data-member-name="${escapeHtml(memberName)}"
                         data-is-current="${isCurrentUser}">
                        <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold text-primary shrink-0">
                            ${memberName.charAt(0).toUpperCase()}
                        </div>
                        <span class="text-sm text-foreground">${escapeHtml(
                            memberName
                        )}${
                    isCurrentUser
                        ? ' <span class="text-xs text-gray-500">(you)</span>'
                        : ""
                }</span>
                    </div>
                `;
                $taskAssignedMembers.append(memberHtml);
            }
        });

        // Add click handlers
        $(".member-item").on("click", function () {
            const memberId = $(this).data("member-id");
            const memberName = $(this).data("member-name");
            const isCurrentUser = $(this).data("is-current") === true;

            addMemberSelection(memberId, memberName, isCurrentUser);
        });
    }

    /**
     * Add a member to selection
     */
    function addMemberSelection(memberId, memberName, isCurrentUser = false) {
        // Check if already selected
        if (selectedMembers.some((m) => m.id === memberId)) {
            return;
        }

        selectedMembers.push({
            id: memberId,
            name: memberName,
            isCurrentUser: isCurrentUser,
        });

        updateSelectedBadges();
    }

    /**
     * Remove a member from selection
     */
    function removeMemberSelection(memberId) {
        selectedMembers = selectedMembers.filter((m) => m.id !== memberId);
        updateSelectedBadges();
    }

    /**
     * Update the selected member badges display
     */
    function updateSelectedBadges() {
        $selectedMemberBadges.html("");

        if (selectedMembers.length === 0) {
            $noSelectionText.removeClass("hidden");
            $selectedMemberBadges.append($noSelectionText);
            return;
        }

        $noSelectionText.addClass("hidden");

        selectedMembers.forEach((member) => {
            const badgeHtml = `
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-primary/10 border border-primary/30 text-primary rounded-full text-sm">
                    <span>${escapeHtml(member.name)}${
                member.isCurrentUser ? " (you)" : ""
            }</span>
                    <button type="button" class="remove-member-badge hover:text-red-600 transition" data-member-id="${
                        member.id
                    }">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
            `;
            $selectedMemberBadges.append(badgeHtml);
        });

        // Add click handlers for remove buttons
        $(".remove-member-badge").on("click", function () {
            const memberId = $(this).data("member-id");
            removeMemberSelection(memberId);
        });
    }

    /**
     * Submit form via AJAX
     */
    function submitForm() {
        if (isSubmitting) return;

        clearErrors();

        const formData = new FormData($addTaskForm[0]);
        const campaignId = $taskCampaignId.val();
        const title = formData.get("title");
        const csrfToken = $('input[name="_token"]').val();

        // Add selected member IDs to form data
        selectedMembers.forEach((member) => {
            formData.append("assigned_member_ids[]", member.id);
        });

        // Validate
        if (!title || title.trim() === "") {
            showError("taskTitleError", "Title is required");
            return;
        }

        if (selectedMembers.length === 0) {
            showError(
                "taskAssignedMemberIdsError",
                "Please assign at least one member"
            );
            return;
        }

        if (!campaignId) {
            console.error("Campaign ID not found");
            return;
        }

        if (!csrfToken) {
            console.error("CSRF Token not found");
            return;
        }

        // Show loading state
        setLoadingState(true);

        // Log form data
        console.log("Form data being sent:");
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }

        // Send AJAX request
        $.ajax({
            url: `/campaigns/${campaignId}/tasks`,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            data: formData,
            processData: false,
            contentType: false,
            success: handleSuccess,
            error: handleError,
            complete: function () {
                setLoadingState(false);
            },
        });
    }

    /**
     * Set loading state for submit button
     */
    function setLoadingState(loading) {
        isSubmitting = loading;
        if (loading) {
            $submitBtn.prop("disabled", true);
            $submitBtn.html(`
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Creating...</span>
            `);
        } else {
            $submitBtn.prop("disabled", false);
            $submitBtn.html(`
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Task
            `);
        }
    }

    /**
     * Handle successful form submission
     */
    function handleSuccess(data) {
        if (data.success) {
            closeModal();
            window.location.reload();
            showToast("Task created successfully!", "success");
        }
    }

    /**
     * Show toast notification
     */
    function showToast(message, type = "success") {
        const bgColor = type === "success" ? "bg-green-500" : "bg-red-500";
        const icon =
            type === "success"
                ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';

        const toast = $(`
            <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50 toast-notification">
                ${icon}
                <span>${message}</span>
            </div>
        `);

        $("body").append(toast);

        setTimeout(() => {
            toast.fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }

    /**
     * Handle form submission error
     */
    function handleError(xhr) {
        console.log("Error Response Status:", xhr.status);
        console.log("Error Response JSON:", xhr.responseJSON);

        const error = xhr.responseJSON || {};

        if (xhr.status === 422 && error.errors) {
            console.log("Validation Errors:", error.errors);
            $.each(error.errors, function (field, messages) {
                const errorFieldName =
                    field === "assigned_member_ids"
                        ? "assigned_member_ids"
                        : field;
                showError(
                    `task${capitalize(errorFieldName)}Error`,
                    messages[0]
                );
            });
            showToast("Please fix the validation errors", "error");
        } else if (error.message) {
            console.error("Error:", error.message);
            showToast(error.message, "error");
        } else {
            console.error("Unknown error:", xhr);
            showToast("An error occurred while creating the task", "error");
        }
    }

    /**
     * Show error message for a field
     */
    function showError(elementId, message) {
        const $errorEl = $(`#${elementId}`);
        if ($errorEl.length > 0) {
            $errorEl.text(message).removeClass("hidden");
        }
    }

    /**
     * Clear all error messages
     */
    function clearErrors() {
        $('[id*="Error"]').text("").addClass("hidden");
    }

    /**
     * Create task element HTML
     */
    function createTaskElement(task) {
        const statusColors = {
            planning: "bg-blue-100 text-blue-700",
            ongoing: "bg-yellow-100 text-yellow-700",
            on_hold: "bg-red-100 text-red-700",
            accomplished: "bg-green-100 text-green-700",
        };

        const statusColor =
            statusColors[task.status] || "bg-gray-100 text-gray-700";
        const statusLabel =
            task.status.charAt(0).toUpperCase() +
            task.status.slice(1).replace("_", " ");

        let html = `
            <div class="p-4 border border-secondary/20 rounded-lg hover:border-secondary/40 hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-foreground">${escapeHtml(
                            task.title
                        )}</h3>
        `;

        if (task.description) {
            html += `<p class="text-xs text-gray-600 mt-1 line-clamp-2">${escapeHtml(
                task.description
            )}</p>`;
        }

        html += `
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap ${statusColor}">
                        ${statusLabel}
                    </span>
                </div>

                <div class="flex items-center gap-4 text-xs text-gray-600 mt-3">
        `;

        if (task.start_date) {
            const startDate = new Date(task.start_date).toLocaleDateString(
                "en-US",
                { month: "short", day: "numeric" }
            );
            html += `
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v2h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1z"></path></svg>
                    ${startDate}
                </span>
            `;
        }

        if (task.target_date) {
            const targetDate = new Date(task.target_date).toLocaleDateString(
                "en-US",
                { month: "short", day: "numeric" }
            );
            html += `
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    ${targetDate}
                </span>
            `;
        }

        html += "</div>";

        // Show multiple assigned members
        if (task.members && task.members.length > 0) {
            html += `
                <div class="mt-3 pt-3 border-t border-secondary/20">
                    <div class="text-xs text-gray-600 mb-2">Assigned to:</div>
                    <div class="flex flex-wrap gap-2">
            `;

            task.members.forEach((member) => {
                const initials = member.name.charAt(0).toUpperCase();
                html += `
                    <div class="flex items-center gap-1 px-2 py-1 bg-primary/10 rounded-full text-xs">
                        <div class="w-4 h-4 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold text-primary">
                            ${initials}
                        </div>
                        <span class="text-foreground">${escapeHtml(
                            member.name
                        )}</span>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        }

        html += "</div>";

        return html;
    }

    /**
     * Update task count in header
     */
    function updateTaskCount(campaignId) {
        const $taskPanel = $(`[data-campaign-panel="${campaignId}"]`);
        if ($taskPanel.length === 0) return;

        const $tasksContainer = $taskPanel.find(".space-y-3");
        const taskCount = $tasksContainer.find("> div").length;

        const $countBadge = $taskPanel.find(".text-xs.text-gray-500").first();
        if ($countBadge.length > 0) {
            $countBadge.text(`${taskCount} task(s)`);
        }
    }

    /**
     * Capitalize first letter
     */
    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /**
     * Escape HTML special characters
     */
    function escapeHtml(text) {
        const map = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        };
        return text.replace(/[&<>"']/g, (m) => map[m]);
    }
});
