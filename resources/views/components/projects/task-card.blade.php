@props(['task', 'userAccessLevel' => 'viewer', 'isProjectOwner' => false])

@php
use Illuminate\Support\Str;
use Carbon\Carbon;

$statusClasses = [
'pending' => 'bg-yellow-100 text-yellow-700',
'ongoing' => 'bg-blue-100 text-blue-700',
'completed' => 'bg-green-100 text-green-700',
];

// Determine which buttons should be visible based on access level
// Project owners have all permissions+
$isDue = $task->target_date && Carbon::parse($task->target_date)->isPast();

$canEditTask = $isProjectOwner || in_array($userAccessLevel, ['editor', 'all']);
$canDeleteTask = $isProjectOwner || in_array($userAccessLevel, ['all']);
$canUpdateStatus = $isProjectOwner || in_array($userAccessLevel, ['editor', 'all']);
$canAddRemark = $isProjectOwner || in_array($userAccessLevel, ['viewer', 'editor', 'all']);
@endphp

<div class="flex items-start gap-3 p-3 rounded-lg border border-secondary/20 hover:bg-gray-50 transition">
    <div class="flex-1 space-y-2">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-2">
                <h4 class="text-base font-semibold text-primary flex items-center gap-1.5">{{ $task->title }}</h4>
            </div>
            <span class="px-2 py-1 text-xs rounded-full {{ $statusClasses[$task->status] ?? 'bg-gray-100 text-gray-700' }} flex items-center gap-1">
                <x-heroicon-o-sparkles class="w-3 h-3" />
                {{ ucfirst($task->status) }}
            </span>
        </div>

        <div class="flex items-center gap-3 text-xs flex-wrap">
            <span class="inline-flex items-center gap-1">
                <x-heroicon-o-clock class="w-4 h-4 " />
                Start: {{ $task->start_date ? date('M d, Y', strtotime($task->start_date)) : 'Not set' }}
            </span>
            @if ($isDue && $task->status !== 'completed')
            <span class="inline-flex items-center gap-1 text-red-600 font-medium">
                <x-heroicon-o-flag class="w-4 h-4" />
                Due: {{ Carbon::parse($task->target_date)->format('M d, Y') }}
            </span>
            @else
            <span class="inline-flex items-center gap-1 text-foreground font-medium">
                <x-heroicon-o-flag class="w-4 h-4" />
                Due: {{ Carbon::parse($task->target_date)->format('M d, Y') }}
            </span>
            @endif
            @if($task->status === 'completed')
            <span class="inline-flex text-xs items-center gap-1 text-green-600 font-medium">
                <x-heroicon-o-check-circle class="w-4 h-4" />
                Completed: {{ Carbon::parse($task->completed_at)->format('M d, Y') }}
            </span>
            @endif
        </div>

        @if ($task->description)
        <p class="text-xs text-gray-600 leading-relaxed">
            {{ Str::limit($task->description, 140) }}
        </p>
        @endif

        <div class="flex items-center gap-2 text-xs">
            @if ($canAddRemark)
            <button type="button" class="taskActionBtn relative px-2 py-1 border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center gap-1"
                data-task-id="{{ $task->id }}" data-task-title="{{ $task->title }}" data-action="remarks">
                <x-heroicon-o-chat-bubble-left class="w-4 h-4" />
                Remarks
                @if (($task->remarks ?? collect())->count() > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-blue-600 rounded-full">{{ $task->remarks->count() }}</span>
                @endif
            </button>
            @endif

            @if ($canUpdateStatus)
            <button type="button" class="taskActionBtn px-2 py-1 border border-secondary/30 rounded-lg text-foreground hover:bg-gray-100 transition flex items-center gap-1"
                data-task-id="{{ $task->id }}" data-task-status="{{ $task->status }}" data-task-title="{{ $task->title }}" data-action="status">
                <x-heroicon-o-arrow-path class="w-4 h-4" />
                Update Status
            </button>
            @endif

            @if ($canEditTask)
            <button type="button" class="taskActionBtn px-2 py-1 border border-green-400/30 rounded-lg text-green-600 hover:bg-green-100 transition flex items-center gap-1"
                data-task-id="{{ $task->id }}"
                data-task-title="{{ $task->title }}"
                data-task-description="{{ $task->description }}"
                data-task-start-date="{{ $task->start_date }}"
                data-task-target-date="{{ $task->target_date }}"
                data-task-status="{{ $task->status }}"
                data-task-campaign-id="{{ $task->assigned_campaign_id }}"
                data-action="edit">
                <x-heroicon-o-pencil class="w-4 h-4" />
                Edit Task
            </button>
            @endif

            @if ($canDeleteTask)
            <button type="button" class="taskActionBtn px-2 py-1 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition flex items-center gap-1"
                data-task-id="{{ $task->id }}" data-task-title="{{ $task->title }}" data-action="delete">
                <x-heroicon-o-trash class="w-4 h-4" />
                Delete Task
            </button>
            @endif
        </div>
    </div>
</div>