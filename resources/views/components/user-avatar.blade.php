@props(['user', 'class' => 'w-8 h-8 text-xs'])
<div
    class="user-avatar-trigger inline-flex shrink-0"
    data-name="{{ $user->name ?? 'Unknown' }}"
    data-username="{{ $user->username ?? '' }}"
    data-bio="{{ $user->bio ?? '' }}"
    data-avatar="{{ $user?->avatar ? asset('storage/' . $user->avatar) : '' }}"
    data-initial="{{ strtoupper(substr($user->name ?? $user->email ?? '?', 0, 1)) }}"
>
    <div {{ $attributes->merge(['class' => $class . ' rounded-full bg-primary/20 flex items-center justify-center font-semibold text-primary overflow-hidden']) }}>
        @if($user?->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name ?? '' }}" class="w-full h-full object-cover" loading="lazy" />
        @else
            {{ strtoupper(substr($user->name ?? $user->email ?? '?', 0, 1)) }}
        @endif
    </div>
</div>
