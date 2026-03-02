<!-- Edit Project Modal -->
<div id="editProjectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 my-8">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div>
                <h2 class="text-xl font-semibold text-primary">Edit Project</h2>
                <p class="text-sm text-gray-600">Update project details</p>
            </div>
            <button id="closeEditProjectModal" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editProjectForm" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Hidden fields for current project data -->
            <input type="hidden" id="currentProjectName" value="{{ $project->name }}" />
            <input type="hidden" id="currentProjectDescription" value="{{ $project->description }}" />
            <input type="hidden" id="currentProjectStartDate" value="{{ $project->start_date }}" />
            <input type="hidden" id="currentProjectTargetDate" value="{{ $project->target_date }}" />
            <input type="hidden" id="currentProjectStatus" value="{{ $project->status }}" />

            <!-- Campaign (Read-only) -->
            <div>
                <label class="text-sm font-medium text-gray-700">Campaign</label>
                <div class="mt-1 px-3 py-2 bg-gray-100 border border-secondary/20 rounded-lg text-sm text-gray-600 cursor-not-allowed">
                    <span id="editProjectCampaign">{{ $project->campaign->name ?? 'Unknown Campaign' }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Campaign cannot be changed after project creation</p>
            </div>

            <!-- Project Name -->
            <div>
                <label for="editProjectName" class="text-sm font-medium text-gray-700 block mb-1">
                    Project Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="editProjectName"
                    placeholder="Enter project name"
                    required
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-primary/20" />
                <span class="text-xs text-red-500 hidden" id="editNameError"></span>
            </div>

            <!-- Project Description -->
            <div>
                <label for="editProjectDescription" class="text-sm font-medium text-gray-700 block mb-1">Description</label>
                <textarea
                    name="description"
                    id="editProjectDescription"
                    placeholder="Enter project description..."
                    rows="4"
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-primary/20 resize-none"></textarea>
                <span class="text-xs text-red-500 hidden" id="editDescriptionError"></span>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Start Date -->
                <div>
                    <label for="editStartDate" class="text-sm font-medium text-gray-700 block mb-1">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="start_date"
                        id="editStartDate"
                        required
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="editStartDateError"></span>
                </div>

                <!-- Target Date -->
                <div>
                    <label for="editTargetDate" class="text-sm font-medium text-gray-700 block mb-1">Target Date</label>
                    <input
                        type="date"
                        name="target_date"
                        id="editTargetDate"
                        class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="editTargetDateError"></span>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label for="editProjectStatus" class="text-sm font-medium text-gray-700 block mb-1">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    name="status"
                    id="editProjectStatus"
                    required
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select a status</option>
                    <option value="planning">Planning</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                </select>
                <span class="text-xs text-red-500 hidden" id="editStatusError"></span>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 border-t border-secondary/20 px-6 py-4 bg-gray-50">
            <button type="button"
                id="cancelEditProjectBtn"
                class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">
                Cancel
            </button>
            <button type="submit"
                form="editProjectForm"
                id="submitEditProjectBtn"
                class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <span id="submitEditBtnText">Update Project</span>
                <svg id="submitEditBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>
</div>
