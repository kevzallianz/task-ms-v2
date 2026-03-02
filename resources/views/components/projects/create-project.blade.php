<!-- Create Project Modal -->
<div id="createProjectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 my-8">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-secondary/20 px-6 py-4">
            <div>
                <h2 class="text-xl font-semibold text-primary">Create New Project</h2>
                <p class="text-sm text-gray-600">Fill in the details to create a new project</p>
            </div>
            <button id="closeProjectModal" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="createProjectForm" class="p-6 space-y-5">
            @csrf

            <div>
                <select name="campaign_id" id="campaignId" class="w-full px-2 py-2 rounded-lg border border-secondary/20 text-sm">
                    <option value="">Select Campaign</option>
                    @foreach ($campaigns as $campaign)
                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                    @endforeach
                    @if($campaigns->isEmpty())
                        <option value="" disabled>No campaigns available</option>
                    @endif
                </select>
                <span class="text-xs text-red-500 hidden" id="campaignIdError"></span>
            </div>
            <!-- Project Name -->
            <div>
                <x-input-field
                    label="Project Name"
                    type="text"
                    name="name"
                    id="projectName"
                    placeholder="Enter project name"
                    value="{{ old('name') }}"
                    required />
                <span class="text-xs text-red-500 hidden" id="nameError"></span>
            </div>

            <!-- Project Description -->
            <div>
                <label class="text-sm font-medium">Description</label>
                <textarea name="description"
                    id="projectDescription"
                    placeholder="Enter project description..."
                    rows="4"
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-primary/20 resize-none"></textarea>
                <span class="text-xs text-red-500 hidden" id="descriptionError"></span>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Start Date -->
                <div>
                    <x-input-field
                        label="Start Date"
                        type="date"
                        name="start_date"
                        id="startDate"
                        value="{{ old('start_date', date('Y-m-d')) }}"
                        required />
                    <span class="text-xs text-red-500 hidden" id="startDateError"></span>
                </div>

                <!-- Target Date -->
                <div>
                    <x-input-field
                        label="Target Date"
                        type="date"
                        name="target_date"
                        id="targetDate"
                        value="{{ old('target_date') }}" />
                    <span class="text-xs text-red-500 hidden" id="targetDateError"></span>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="text-sm font-medium">Status</label>
                <select name="status"
                    id="projectStatus"
                    required
                    class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-primary/20">
                    <option value="">Select a status</option>
                    <option value="planning">Planning</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                </select>
                <span class="text-xs text-red-500 hidden" id="statusError"></span>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50">
            <button type="button"
                id="cancelProjectBtn"
                class="px-4 py-2 text-sm font-medium text-foreground border border-secondary/30 rounded-lg hover:bg-gray-100 transition">
                Cancel
            </button>
            <button type="submit"
                form="createProjectForm"
                id="submitProjectBtn"
                class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                <span id="submitBtnText">Create Project</span>
                <svg id="submitBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6m0-6h6m0 0h6"></path>
                </svg>
            </button>
        </div>
    </div>
</div>