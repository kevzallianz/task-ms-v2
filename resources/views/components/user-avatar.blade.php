@props(['user', 'class' => 'w-8 h-8 text-xs'])
<div {{ $attributes->merge(['class' => $class . ' rounded-full bg-primary/20 flex items-center justify-center font-semibold text-primary shrink-0 overflow-hidden']) }}>
    @if($user?->avatar)
        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name ?? '' }}" class="w-full h-full object-cover" loading="lazy" />
    @else
        {{ strtoupper(substr($user->name ?? $user->email ?? '?', 0, 1)) }}
    @endif
</div>
