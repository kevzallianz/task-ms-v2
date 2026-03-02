@extends('layouts.general-layout')

@section('content')
<section class="min-h-screen bg-background">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[70%_30%]">
        <div class="relative hidden lg:flex flex-col justify-center px-20 overflow-hidden bg-linear-to-br from-primary via-primary/90 to-secondary text-white">
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-accent/20 rounded-full blur-3xl"></div>

            <div class="relative max-w-3xl space-y-12">
                <div class="space-y-6">
                    <h1 class="text-5xl font-bold leading-tight">Reset your password</h1>
                    <p class="text-xl opacity-90 max-w-2xl">Enter the email associated with your account and we'll send a link to reset your password.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center px-6">
            <div class="w-full max-w-lg">
                @include('components.forms.forgot')
            </div>
        </div>

    </div>

</section>
@endsection
