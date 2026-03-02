<article {{ $attributes->merge([
        'class' => 'p-5 bg-white cursor-pointer border border-secondary/30 rounded-xl hover:shadow-sm transition'
    ]) }}>
    <div class="flex items-start justify-between gap-4">

        {{-- Project Info --}}
        <div class="flex-1">
            <h2 class="text-lg font-medium flex items-center text-primary gap-2">
                <x-heroicon-o-folder-open class="w-5 h-5" />
                {{ $name }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
        </div>

        {{-- Status --}}
        <span
            class="flex items-center gap-1 px-3 py-1 text-xs rounded-full
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

    {{-- Meta + Progress --}}
    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        {{-- Dates --}}
        <div class="flex items-center gap-6 text-sm">
            <span class="flex items-center gap-1">
                <x-heroicon-o-calendar class="w-4 h-4" />
                Start: {{ $startDate }}
            </span>
            <span class="flex items-center gap-1">
                <x-heroicon-o-flag class="w-4 h-4" />
                Target: {{ $targetDate }}
            </span>
        </div>
    </div>
    <div class="mt-3 pt-3 border-t border-secondary/20">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <x-heroicon-o-user class="w-4 h-4" />
            <span class="font-medium text-primary">{{ $ownerName }}</span>
            <span class="text-gray-400">·</span>
            <span>{{ $ownerCampaign }}</span>
        </div>
    </div>
</article>