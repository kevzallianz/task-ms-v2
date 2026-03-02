@props(['activity'])

@php
    $actionIcons = [
        'created' => 'o-plus-circle',
        'updated' => 'o-pencil-square',
        'status_changed' => 'o-arrow-path',
        'task_added' => 'o-document-plus',
        'task_updated' => 'o-pencil',
        'task_deleted' => 'o-trash',
        'task_status_changed' => 'o-arrow-path-rounded-square',
        'task_remark_added' => 'o-chat-bubble-left-ellipsis',
    ];

    $actionColors = [
        'created' => 'text-green-600 bg-green-100',
        'updated' => 'text-blue-600 bg-blue-100',
        'status_changed' => 'text-purple-600 bg-purple-100',
        'task_added' => 'text-indigo-600 bg-indigo-100',
        'task_updated' => 'text-blue-600 bg-blue-100',
        'task_deleted' => 'text-red-600 bg-red-100',
        'task_status_changed' => 'text-yellow-600 bg-yellow-100',
        'task_remark_added' => 'text-cyan-600 bg-cyan-100',
    ];

    $icon = $actionIcons[$activity->action_type] ?? 'o-information-circle';
    $colorClass = $actionColors[$activity->action_type] ?? 'text-gray-600 bg-gray-100';
    $timeAgo = $activity->created_at->diffForHumans();
@endphp

<div class="flex items-start gap-4 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition px-2 rounded-lg">
    {{-- Activity Icon --}}
    <div class="shrink-0 w-8 h-8 rounded-full {{ $colorClass }} flex items-center justify-center">
        <x-dynamic-component :component="'heroicon-' . $icon" class="w-4 h-4" />
    </div>

    {{-- Activity Content --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <div class="flex-1">
                <p class="text-sm text-foreground">
                    <span class="font-medium text-primary">{{ $activity->user->name }}</span>
                    <span class="text-gray-600">{{ $activity->description }}</span>
                </p>

                {{-- Show metadata details for status changes --}}
                @if ($activity->action_type === 'status_changed' && $activity->metadata)
                    <div class="mt-1 flex items-center gap-2 text-xs">
                        <span class="px-2 py-0.5 rounded-full
                            {{ $activity->metadata['old_status'] === 'planning' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $activity->metadata['old_status'] === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $activity->metadata['old_status'] === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $activity->metadata['old_status'] === 'on_hold' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucwords(str_replace('_', ' ', $activity->metadata['old_status'])) }}
                        </span>
                        <x-heroicon-o-arrow-right class="w-3 h-3 text-gray-400" />
                        <span class="px-2 py-0.5 rounded-full
                            {{ $activity->metadata['new_status'] === 'planning' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $activity->metadata['new_status'] === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $activity->metadata['new_status'] === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $activity->metadata['new_status'] === 'on_hold' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucwords(str_replace('_', ' ', $activity->metadata['new_status'])) }}
                        </span>
                    </div>
                @endif

                {{-- Show metadata details for task status changes --}}
                @if ($activity->action_type === 'task_status_changed' && $activity->metadata && isset($activity->metadata['old_status']) && isset($activity->metadata['new_status']))
                    <div class="mt-1 flex items-center gap-2 text-xs">
                        <span class="px-2 py-0.5 rounded-full
                            {{ $activity->metadata['old_status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $activity->metadata['old_status'] === 'ongoing' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $activity->metadata['old_status'] === 'completed' ? 'bg-green-100 text-green-700' : '' }}">
                            {{ ucfirst((string) $activity->metadata['old_status']) }}
                        </span>
                        <x-heroicon-o-arrow-right class="w-3 h-3 text-gray-400" />
                        <span class="px-2 py-0.5 rounded-full
                            {{ $activity->metadata['new_status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $activity->metadata['new_status'] === 'ongoing' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $activity->metadata['new_status'] === 'completed' ? 'bg-green-100 text-green-700' : '' }}">
                            {{ ucfirst((string) $activity->metadata['new_status']) }}
                        </span>
                    </div>
                @endif

                {{-- Show task details for task-related activities --}}
                @if (in_array($activity->action_type, ['task_added', 'task_updated', 'task_deleted']) && $activity->metadata && isset($activity->metadata['task_title']))
                    <div class="mt-1 pl-3 border-l-2 border-indigo-200">
                        <p class="text-xs text-gray-600 font-medium">{{ $activity->metadata['task_title'] }}</p>
                    </div>
                @endif

                {{-- Show changes for update activities --}}
                @if ($activity->action_type === 'updated' && $activity->metadata && isset($activity->metadata['changes']))
                    <div class="mt-1 text-xs text-gray-500 space-y-0.5">
                        @foreach ($activity->metadata['changes'] as $field)
                            @if (is_string($field))
                                <div class="flex items-center gap-1">
                                    <x-heroicon-o-check-circle class="w-3 h-3 text-blue-500" />
                                    <span>{{ ucfirst(str_replace('_', ' ', $field)) }} updated</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Timestamp --}}
            <span class="text-xs text-gray-500 whitespace-nowrap">{{ $timeAgo }}</span>
        </div>
    </div>
</div>
