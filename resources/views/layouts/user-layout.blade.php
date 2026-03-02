@extends('layouts.general-layout')
@section('content')
    <main class="flex items-start w-full max-h-screen">
        <x-navigation/>
        <section class="p-3 w-full overflow-y-auto h-screen">
            @yield('user-content')
        </section>
    </main>
@endsection