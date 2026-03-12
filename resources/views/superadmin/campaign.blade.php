@extends('layouts.user-layout')

@section('user-content')
<main class="flex flex-col gap-6">

	{{-- Page Header --}}
	<article class="flex items-center justify-between">
		<div>
			<h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
				<x-heroicon-o-users class="w-6 h-6" />
				Campaigns
			</h1>
			<p class="text-sm text-gray-600">All campaigns and their members</p>
		</div>
		<button id="addCampaignBtn" class="px-4 py-2 flex items-center gap-2 text-sm font-medium text-white bg-primary rounded hover:bg-primary-dark transition">
			<x-heroicon-o-plus class="w-4 h-4" />
			<span>Add New Campaign</span>
		</button>
	</article>
	<section class="p-4 bg-gray-50 rounded-lg border border-secondary/20 w-full">
		<form action="{{ route('superadmin.campaigns') }}" method="GET" class="flex items-end w-full justify-end gap-3">
			<div class="w-full">
				<label class="text-xs font-medium text-gray-700 mb-1 block">Search Campaigns</label>
				<input type="text" name="q" value="{{ $search ?? request('q') }}" placeholder="Search by name or description" class="w-full px-3 py-2 text-sm border border-secondary/30 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20" />
			</div>
			<div class="flex items-end justify-end gap-2 w-fit">
				<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded hover:bg-primary/90 transition">Search</button>
				<a href="{{ route('superadmin.campaigns') }}" class="px-4 py-2 text-sm font-medium text-secondary border border-secondary/50 bg-white rounded hover:bg-white/80 transition">Clear</a>
			</div>
		</form>
	</section>
	{{-- Campaigns Table --}}
	<section class="bg-white rounded-lg border border-gray-200 overflow-hidden">
		<div class="overflow-x-auto">
			<table class="min-w-full">
				<thead class="bg-gray-50 border-b border-gray-200">
					<tr>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Campaign</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Description</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Member Count</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Created</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Actions</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200">
					@forelse($campaigns as $campaign)
					<tr class="hover:bg-gray-50" data-campaign-row="{{ $campaign->id }}">
						<td class="px-4 py-3 align-top">
							<div class="flex items-center gap-2">
								<span class="font-medium text-foreground text-sm">{{ $campaign->name }}</span>
							</div>
						</td>
						<td class="px-4 py-3 align-top">
							<p class="text-sm text-foreground/50 line-clamp-2">{{ $campaign->description ?? '—' }}</p>
						</td>
						<td class="px-4 py-3 align-top">
							<span class="text-sm font-medium text-foreground">{{ $campaign->members_count }}</span>
						</td>
						<td class="px-4 py-3 align-top">
							<span class="text-sm text-gray-600">{{ optional($campaign->created_at)->diffForHumans() }}</span>
						</td>
						<td class="px-4 py-3 align-top">
							<div class="flex items-center gap-2">
								<a href="{{ route('superadmin.campaigns.members', $campaign) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary border border-primary/50 bg-white rounded hover:bg-white/80 transition">
									<x-heroicon-o-users class="w-4 h-4" />
									Members
								</a>
								<button
									type="button"
									class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-secondary border border-secondary/50 bg-white rounded hover:bg-white/80 transition campaign-edit-open"
									data-campaign-id="{{ $campaign->id }}"
									data-campaign-name="{{ $campaign->name }}"
									data-campaign-description="{{ $campaign->description ?? '' }}"
									data-update-url="{{ route('superadmin.campaigns.update', $campaign) }}"
									aria-label="Edit {{ $campaign->name }}"
								>
									<x-heroicon-o-pencil-square class="w-4 h-4" />
									Edit
								</button>
								<button
									type="button"
									class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 border border-red-300 bg-white rounded hover:bg-red-50 transition campaign-delete-open"
									data-campaign-id="{{ $campaign->id }}"
									data-campaign-name="{{ $campaign->name }}"								data-members-count="{{ $campaign->members_count }}"									data-delete-url="{{ route('superadmin.campaigns.delete', $campaign) }}"
									aria-label="Delete {{ $campaign->name }}"
								>
									<x-heroicon-o-trash class="w-4 h-4" />
									Delete
								</button>
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="5" class="px-4 py-10 text-center">
							<div class="flex flex-col items-center gap-2 text-gray-500">
								<x-heroicon-o-folder-open class="w-10 h-10 text-gray-300" />
								<p class="text-sm">No campaigns found</p>
							</div>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		{{-- Pagination Footer --}}
		<div class="flex items-center justify-end px-4 py-3 border-t border-gray-200 bg-gray-50">
			<div class="flex items-center gap-2">
				{{ $campaigns->links() }}
			</div>
		</div>
	</section>
	{{-- Add Campaign Modal Component --}}
	@include('components.superadmin.add-campaign-modal')
	@include('components.superadmin.edit-campaign-modal')
	@include('components.superadmin.delete-campaign-modal')

	{{-- Script --}}
	@vite(['resources/js/superadmin/index.js'])
</main>
@endsection