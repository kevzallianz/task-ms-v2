@extends('layouts.general-layout')

@section('content')
@php
$features = [
[
'icon' => '📊',
'title' => 'Visual Gantt Charts',
'description' => 'Instantly see timelines, overlaps, and milestones.'
],
[
'icon' => '🔗',
'title' => 'Smart Dependencies',
'description' => 'Automatically adjust schedules when tasks shift.'
],
[
'icon' => '⏱️',
'title' => 'Critical Path',
'description' => 'Identify what truly impacts delivery dates.'
],
[
'icon' => '👥',
'title' => 'Team Visibility',
'description' => 'Everyone knows what’s next and what’s blocked.'
],
];
@endphp
<section class="min-h-screen bg-background">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[70%_30%]">
        <div class="relative hidden lg:flex flex-col justify-center px-20 overflow-hidden bg-linear-to-br from-primary via-primary/90 to-secondary text-white">

            <div class="absolute -top-32 -left-32 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-accent/20 rounded-full blur-3xl"></div>

            <div class="relative max-w-3xl space-y-12">
                <div class="space-y-6">
                    <h1 class="text-5xl font-bold leading-tight">Project planning that<br>teams actually enjoy.</h1>
                    <p class="text-xl opacity-90 max-w-2xl">Plan tasks, manage dependencies, and visualize timelines with powerful Gantt charts designed for clarity and control.</p>
                </div>

                {{-- Feature Grid --}}
                <div class="grid grid-cols-2 gap-8 text-sm">
                    @foreach ($features as $feature)
                    <div class="flex gap-4">
                        <div class="h-10 w-10 rounded-lg bg-white/15 flex items-center justify-center">{{ $feature['icon'] }}</div>
                        <div>
                            <h3 class="font-semibold">{{ $feature['title'] }}</h3>
                            <p class="opacity-80">{{ $feature['description'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold">How it works</h3>
                    <div class="flex items-center gap-6 text-sm opacity-90">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 bg-accent rounded-full"></span>
                            Create tasks
                        </div>
                        →
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 bg-accent rounded-full"></span>
                            Link dependencies
                        </div>
                        →
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 bg-accent rounded-full"></span>
                            Track progress
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6 pt-4 border-t border-white/20 text-sm opacity-80">
                    <span>Used by project teams worldwide</span>
                    <span>•</span>
                    <span>Built for clarity & performance</span>
                    <span>•</span>
                    <span>Developed by Business Development Team</span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center px-6">
            <div class="w-full max-w-lg">
                @include('components.forms.login')
            </div>
        </div>

    </div>

</section>
@endsection