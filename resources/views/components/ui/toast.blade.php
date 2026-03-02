@props([
'type' => 'info',
'message' => ''
])

@php
$colors = [
'success' => 'bg-green-600',
'error' => 'bg-red-600',
'warning' => 'bg-yellow-500',
'info' => 'bg-gray-900',
];
@endphp

<div class="toast pointer-events-auto flex items-center gap-3 rounded-lg px-4 py-3 text-white shadow-md {{ $colors[$type] ?? $colors['info'] }}" data-timeout="3500">
    <span class="flex-1 text-sm">{{ $message }}</span>
    <button class="toast-close text-white/70 hover:text-white transition">&times;</button>
</div>