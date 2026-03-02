@extends('layouts.user-layout')

@section('user-content')
<main class="flex flex-col gap-6">

    {{-- Page Header --}}
    <article class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
                <x-heroicon-o-users class="w-6 h-6" />
                Campaign Members
            </h1>
            <p class="text-sm text-gray-600">Members of <span class="font-semibold text-foreground">{{ $campaign->name }}</span></p>
        </div>
        <div>
            <a href="{{ route('superadmin.campaigns') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-secondary border border-secondary/50 bg-white rounded-lg hover:bg-white/80 transition">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
                Back to Campaigns
            </a>
        </div>
    </article>

    {{-- Members Table --}}
    <section class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Name</th>
                        <th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Email</th>
                        <th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Access Level</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($members as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 align-top">
                            <span class="font-medium text-foreground text-sm">{{ $member->name }}</span>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <span class="text-sm text-gray-700">{{ $member->email }}</span>
                        </td>
                        <td class="px-4 py-3 align-top">
                            @php
                            $levelClasses = [
                            'owner' => 'bg-purple-100 text-purple-700 border-purple-200',
                            'admin' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'member' => 'bg-gray-100 text-gray-700 border-gray-200',
                            'viewer' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                            ];
                            $level = $member->pivot->access_level ?? 'member';
                            @endphp
                            <span class="text-xs px-2 py-1 rounded border {{ $levelClasses[$level] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                {{ ucfirst(str_replace('_', ' ', $level)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-500">
                                <x-heroicon-o-user class="w-10 h-10 text-gray-300" />
                                <p class="text-sm">No members found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination Footer --}}
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center gap-2">
                {{ $members->links() }}
            </div>
        </div>
    </section>

</main>
@endsection