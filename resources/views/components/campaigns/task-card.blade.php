@props(['task', 'campaign', 'isProject' => false])

@php
use Illuminate\Support\Str;

$statusClasses = [
'planning' => 'bg-blue-50 text-blue-700 border-blue-200',
'ongoing' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
'on_hold' => 'bg-red-50 text-red-700 border-red-200',
'accomplished' => 'bg-green-50 text-green-700 border-green-200',
];

$memberUserIds = $task->taskMembers->map(fn($tm) => $tm->campaignMember->user_id)->join(',');
@endphp

<tr class="campaign-task-row border-b border-secondary/20 hover:bg-gray-50 transition-colors"
    data-campaign-id="{{ $campaign->id }}"
    data-campaign-task-id="{{ $task->id }}"
    data-task-title="{{ strtolower($task->title) }}"
    data-task-description="{{ strtolower($task->description ?? '') }}"
    data-task-status="{{ $task->status }}"
    data-task-start-date="{{ $task->start_date ? date('Y-m-d', strtotime($task->start_date)) : '' }}"
    data-task-member-ids="{{ $memberUserIds }}">
    {{-- Task Title & Description --}}
    <td class="px-4 py-3">
        <div class="flex flex-col">
            <h4 class="text-sm font-semibold text-foreground">{{ $task->title }}</h4>
            @if ($task->description)
            <p class="text-xs text-gray-600 mt-0.5 line-clamp-1">{{ $task->description }}</p>
            @endif
        </div>
    </td>

    {{-- Project --}}
    @if($isProject)
    <td class="px-4 py-3">
        @if ($task->project)
        <a href="{{ route('campaigns.projects.view', ['campaign' => $campaign->id, 'project' => $task->project->id]) }}"
            class="text-sm text-primary hover:text-primary/80 hover:underline font-medium transition">
            {{ $task->project->title }}
        </a>
        @else
        <span class="text-xs text-gray-400">No Project</span>
        @endif
    </td>
    @endif

    {{-- Status --}}
    <td class="px-4 py-3">
        <span
            data-current-status="{{ $task->status }}"
            data-badge-task-id="{{ $task->id }}"
            @class([ 'inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full campaign-task-status-badge border' ,
            $statusClasses[$task->status] ?? 'bg-gray-50 text-gray-700 border-gray-200',
            ])>
            {{ ucwords(str_replace('_', ' ', $task->status)) }}
        </span>
    </td>

    {{-- Start Date --}}
    <td class="px-4 py-3">
        @if ($task->start_date)
        <span class="text-sm text-nowrap text-foreground">{{ date('M d, Y', strtotime($task->start_date)) }}</span>
        @else
        <span class="text-xs text-gray-400">-</span>
        @endif
    </td>

    {{-- Due Date --}}
    <td class="px-4 py-3">
        @if ($task->target_date)
        <span class="text-sm text-nowrap text-foreground">{{ date('M d, Y', strtotime($task->target_date)) }}</span>
        @else
        <span class="text-xs text-gray-400">-</span>
        @endif
    </td>

    {{-- Completed At --}}
    <td class="px-4 py-3">
        @if ($task->completed_at)
        <span class="text-sm text-foreground text-nowrap">{{ date('M d, Y', strtotime($task->completed_at)) }}</span>
        @else
        <span class="text-xs text-gray-400">Not yet completed</span>
        @endif
    </td>

    {{-- Members --}}
    <td class="px-4 py-3">
        @if ($task->taskMembers->count() > 0)
        @php
        $memberNames = $task->taskMembers->map(function($tm) {
        return $tm->campaignMember->user->name;
        })->join(', ');
        @endphp
        <div class="flex flex-wrap gap-1.5" title="{{ $memberNames }}">
            @foreach ($task->taskMembers as $taskMember)
            @php $member = $taskMember->campaignMember->user; @endphp
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary/10 rounded-full text-xs border border-primary/20">
                <div class="w-4 h-4 rounded-full bg-primary/30 flex items-center justify-center text-[9px] font-semibold text-primary">
                    {{ substr($member->name, 0, 1) }}
                </div>
                <span class="text-foreground text-xs">{{ $member->name }}</span>
            </span>
            @endforeach
        </div>
        @else
        <span class="text-xs text-gray-400">No Assigned Members</span>
        @endif
    </td>

    {{-- Actions --}}
    <td class="px-4 py-3">
        <div class="flex items-center justify-end gap-1.5">
            <button type="button" class="campaignTaskStatusBtn p-1.5 rounded-md hover:bg-secondary/10 text-secondary transition" title="Update Status"
                data-task-id="{{ $task->id }}"
                data-task-title="{{ $task->title }}"
                data-task-status="{{ $task->status }}"
                data-campaign-id="{{ $campaign->id }}">
                <x-heroicon-o-arrow-path class="w-4 h-4" />
            </button>

            <button type="button" class="campaignTaskEditBtn p-1.5 rounded-md hover:bg-green-100 text-green-600 transition" title="Edit Task"
                data-task-id="{{ $task->id }}"
                data-task-title="{{ $task->title }}"
                data-task-description="{{ $task->description }}"
                data-task-start-date="{{ $task->start_date }}"
                data-task-target-date="{{ $task->target_date }}"
                data-task-status="{{ $task->status }}"
                data-completed-at="{{ $task->completed_at }}"
                data-campaign-id="{{ $campaign->id }}"
                data-assigned-members="{{ json_encode($task->assignedMembers->pluck('id')->toArray()) }}">
                <x-heroicon-o-pencil class="w-4 h-4" />
            </button>

            <button type="button" class="campaignTaskDeleteBtn p-1.5 rounded-md hover:bg-red-100 text-red-600 transition" title="Delete Task"
                data-task-id="{{ $task->id }}"
                data-task-title="{{ $task->title }}"
                data-campaign-id="{{ $campaign->id }}">
                <x-heroicon-o-trash class="w-4 h-4" />
            </button>
            <button type="button" class="taskActionBtn p-1.5 rounded-md hover:bg-secondary/10 text-secondary transition" title="Remarks"
                data-action="remarks"
                data-task-id="{{ $task->id }}"
                data-task-title="{{ $task->title }}">
                <x-heroicon-o-chat-bubble-left class="w-4 h-4" />
            </button>
        </div>
    </td>
</tr>