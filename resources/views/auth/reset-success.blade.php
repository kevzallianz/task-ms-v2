@extends('layouts.general-layout')

@section('content')
<section class="min-h-screen bg-background flex items-center justify-center">
    <div class="w-full max-w-md bg-white border border-foreground/20 rounded-2xl p-8">
        <h1 class="text-2xl font-semibold text-primary">Password Reset Successful</h1>
        <p class="mt-4">Your password has been updated. You can now sign in with your new password.</p>
        <div class="mt-6">
            <a href="{{ route('home') }}" class="btn btn-primary">Go to home / Sign in</a>
        </div>
    </div>
</section>
@endsection
