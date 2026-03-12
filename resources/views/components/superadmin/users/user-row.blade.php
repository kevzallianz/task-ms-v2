@php
    $campaignMember = $user->campaignMember;
    $campaign = $campaignMember?->campaign;
    $accessLevel = $campaignMember?->access_level;
    $accessBadgeClass = match($accessLevel) {
        'viewer' => 'bg-gray-100 text-gray-700',
        'editor' => 'bg-blue-100 text-blue-700',
        'all' => 'bg-indigo-100 text-indigo-700',
        default => 'bg-gray-100 text-gray-700',
    };
    $roleBadgeClass = match($user->role) {
        'superadmin' => 'bg-purple-100 text-purple-700',
        'admin' => 'bg-orange-100 text-orange-700',
        default => 'bg-emerald-100 text-emerald-700',
    };
@endphp
<tr class="hover:bg-gray-50" data-user-row="{{ $user->id }}">
    <td class="px-4 py-3 align-top">
        <input type="checkbox" class="user-select h-4 w-4 text-primary border-secondary/40 rounded" value="{{ $user->id }}" aria-label="Select {{ $user->name ?? $user->email }}">
    </td>
    <td class="px-4 py-3 align-top">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-primary/15 text-primary flex items-center justify-center text-sm font-semibold">
                {{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-foreground">{{ $user->name ?? 'Unnamed' }}</p>
                <p class="text-xs text-gray-600">{{ $user->email }}</p>
            </div>
        </div>
    </td>
    <td class="px-4 py-3 align-top">
        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $roleBadgeClass }}" data-role-badge>
            {{ ucfirst($user->role ?? 'user') }}
        </span>
    </td>
    <td class="px-4 py-3 align-top" data-campaign-cell>
        @if ($campaign)
            <div class="flex flex-col gap-1" data-campaign-display>
                <p class="text-sm font-medium text-foreground" data-campaign-name>{{ $campaign->name }}</p>
                <p class="text-xs text-gray-500" data-campaign-desc>{{ $campaign->description ?? 'No description' }}</p>
            </div>
        @else
            <span class="text-sm text-gray-500" data-campaign-empty>—</span>
        @endif
    </td>
    <td class="px-4 py-3 align-top">
        @if ($accessLevel)
            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $accessBadgeClass }}" data-access-badge>
                {{ ucfirst($accessLevel) }}
            </span>
        @else
            <span class="text-sm text-gray-500" data-access-badge>—</span>
        @endif
    </td>
    <td class="px-4 py-3 align-top">
        <span class="text-sm text-gray-600">{{ optional($user->created_at)->diffForHumans() }}</span>
    </td>
    <td class="px-4 py-3 align-top">
        <div class="flex items-center gap-2">
            @if ($campaign)
                <a href="{{ route('superadmin.campaigns.members', $campaign) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary border border-primary/50 bg-white rounded hover:bg-white/80 transition">
                    <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                    Campaign
                </a>
            @else
                <span class="text-xs text-gray-400">No campaign</span>
            @endif

            <button
                type="button"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-secondary border border-secondary/50 bg-white rounded hover:bg-white/80 transition user-campaign-open"
                data-user-id="{{ $user->id }}"
                data-user-name="{{ $user->name ?? $user->email }}"
                data-current-campaign-id="{{ $campaign->id ?? '' }}"
                data-current-access-level="{{ $accessLevel ?? '' }}"
                data-update-url="{{ route('superadmin.users.assign-campaign', $user) }}"
                aria-label="Assign campaign for {{ $user->name ?? $user->email }}"
            >
                <x-heroicon-o-briefcase class="w-4 h-4" />
                Assign
            </button>

            <button
                type="button"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-secondary border border-secondary/50 bg-white rounded hover:bg-white/80 transition user-role-open"
                data-user-id="{{ $user->id }}"
                data-user-name="{{ $user->name ?? $user->email }}"
                data-user-role="{{ $user->role ?? 'user' }}"
                data-update-url="{{ route('superadmin.users.update-role', $user) }}"
                aria-label="Update role for {{ $user->name ?? $user->email }}"
            >
                <x-heroicon-o-adjustments-vertical class="w-4 h-4" />
                Role
            </button>

            <button
                type="button"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 border border-red-300 bg-white rounded hover:bg-red-50 transition user-delete-open"
                data-user-id="{{ $user->id }}"
                data-user-name="{{ $user->name ?? $user->email }}"
                data-delete-url="{{ route('superadmin.users.delete', $user) }}"
                aria-label="Delete {{ $user->name ?? $user->email }}"
            >
                <x-heroicon-o-trash class="w-4 h-4" />
                Delete
            </button>
        </div>
    </td>
</tr>
