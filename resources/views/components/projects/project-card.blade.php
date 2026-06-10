@php
    $taskCount = $taskCount ?? 0;
    $completedTaskCount = $completedTaskCount ?? 0;
    $progress = $taskCount > 0 ? (int) round(($completedTaskCount / $taskCount) * 100) : 0;

    $today = \Carbon\Carbon::now()->startOfDay();
    $targetCarbon = $targetDateRaw ? \Carbon\Carbon::parse($targetDateRaw)->startOfDay() : null;
    $isOverdue = $targetCarbon && $targetCarbon->lt($today) && $status !== 'completed' && $status !== 'cancelled';
@endphp
<article {{ $attributes->merge([
        'class' => 'p-5 bg-white cursor-pointer border border-secondary/30 rounded-xl hover:shadow-md hover:border-primary/30 transition flex flex-col gap-4 h-full'
    ]) }}>
    <div class="flex items-start justify-between gap-3">

        {{-- Project Info --}}
        <div class="flex-1 min-w-0">
            <h2 class="text-lg font-medium flex items-center text-primary gap-2">
                <x-heroicon-o-folder-open class="w-5 h-5 shrink-0" />
                <span class="truncate">{{ $name }}</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1.5 line-clamp-2 min-h-10 leading-relaxed">{{ $description ?: 'No description provided.' }}</p>
        </div>

        {{-- Status --}}
        <span
            class="flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full shrink-0
            bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
            <x-heroicon-o-arrow-path class="w-3 h-3" />
            {{ match($status) {
                'in_progress' => 'In Progress',
                'on_hold' => 'On Hold',
                'completed' => 'Completed',
                'planning' => 'Planning',
                'cancelled' => 'Cancelled',
                default => ucfirst($status),
            } }}
        </span>
    </div>

    {{-- Progress --}}
    <div>
        <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
            <span class="flex items-center gap-1.5">
                <x-heroicon-o-clipboard-document-check class="w-3.5 h-3.5" />
                {{ $completedTaskCount }}/{{ $taskCount }} tasks
            </span>
            <span class="font-medium {{ $progress === 100 ? 'text-green-600' : 'text-gray-500' }}">{{ $progress }}%</span>
        </div>
        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all {{ $progress === 100 ? 'bg-green-500' : 'bg-primary' }}" style="width: {{ $progress }}%"></div>
        </div>
    </div>

    {{-- Meta --}}
    <div class="flex items-center gap-4 text-sm text-gray-500">
        <span class="flex items-center gap-1.5">
            <x-heroicon-o-calendar class="w-4 h-4" />
            {{ $startDate }}
        </span>
        <span class="flex items-center gap-1.5 {{ $isOverdue ? 'text-red-600 font-medium' : '' }}">
            <x-heroicon-o-flag class="w-4 h-4" />
            {{ $targetDate }}
            @if ($isOverdue)
                <span class="text-xs">(Overdue)</span>
            @endif
        </span>
    </div>

    <div class="mt-auto pt-4 border-t border-secondary/20">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <x-heroicon-o-user class="w-4 h-4 shrink-0" />
            <span class="font-medium text-primary truncate">{{ $ownerName }}</span>
            <span class="text-gray-400">·</span>
            <span class="truncate">{{ $ownerCampaign }}</span>
        </div>
    </div>
</article>