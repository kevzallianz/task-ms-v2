@extends('layouts.user-layout')

@section('user-content')
<main class="flex flex-col gap-6">

	{{-- Page Header --}}
	<article class="flex items-center justify-between">
		<div>
			<h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
				<x-heroicon-o-user-group class="w-6 h-6" />
				Users
			</h1>
			<p class="text-sm text-gray-600">All users across campaigns</p>
		</div>
	</article>

	{{-- Filters --}}
	<section class="p-4 bg-gray-50 rounded-lg border border-secondary/20 w-full">
		<form action="{{ route('superadmin.users') }}" method="GET" class="flex items-end w-full justify-end gap-3">
			<div class="w-full">
				<label class="text-xs font-medium text-gray-700 mb-1 block">Search Users</label>
				<input type="text" name="q" value="{{ $search ?? request('q') }}" placeholder="Search by name, email, or username" class="w-full px-3 py-2 text-sm border border-secondary/30 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20" />
			</div>
			<div class="flex items-end justify-end gap-2 w-fit">
				<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded hover:bg-primary/90 transition">Search</button>
				<a href="{{ route('superadmin.users') }}" class="px-4 py-2 text-sm font-medium text-secondary border border-secondary/50 bg-white rounded hover:bg-white/80 transition">Clear</a>
			</div>
		</form>
	</section>

	{{-- Users Table --}}
	<section class="bg-white rounded-lg border border-gray-200 overflow-hidden">
		<div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50">
			<p class="text-sm text-gray-600"><span id="userSelectCount">0</span> selected</p>
			<button
				type="button"
				id="bulkAssignOpen"
				class="px-4 py-2 text-sm font-medium text-white bg-primary rounded disabled:opacity-50 disabled:cursor-not-allowed"
				data-update-url="{{ route('superadmin.users.assign-campaign-bulk') }}"
				disabled
			>Assign to Campaign</button>
		</div>
		<div class="overflow-x-auto">
			<table class="min-w-full">
				<thead class="bg-gray-50 border-b border-gray-200">
					<tr>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3 w-10">
							<input type="checkbox" id="userSelectAll" class="h-4 w-4 text-primary border-secondary/40 rounded" aria-label="Select all users">
						</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">User</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Role</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Campaign</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Access</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Joined</th>
						<th class="text-left text-xs font-medium text-foreground/60 uppercase tracking-wider px-4 py-3">Actions</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200">
					@forelse($users as $user)
						<x-superadmin.users.user-row :user="$user" />
					@empty
					<tr>
						<td colspan="7" class="px-4 py-10 text-center">
							<div class="flex flex-col items-center gap-2 text-gray-500">
								<x-heroicon-o-folder-open class="w-10 h-10 text-gray-300" />
								<p class="text-sm">No users found</p>
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
				{{ $users->links() }}
			</div>
		</div>
	</section>
	<x-superadmin.users.role-modal />
	<x-superadmin.users.assign-campaign-modal :campaigns="$campaignOptions" />
	<x-superadmin.users.assign-campaign-bulk-modal :campaigns="$campaignOptions" />
	<x-superadmin.users.delete-modal />

	@vite(['resources/js/superadmin/index.js'])
</main>
@endsection