<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('components.head')

<body>
    @yield('content')

    <div id="toast-container" class="fixed top-6 right-6 z-50 space-y-3"></div>

    <script>
        function showToast(type, message) {
            const toast = $(`
                <div class="toast-wrapper transform translate-x-6 opacity-0">
                    <x-ui.toast type="${type}" message="${message}" />
                </div>
            `);

            $('#toast-container').append(toast);

            requestAnimationFrame(() => {
                toast.removeClass('translate-x-6 opacity-0').addClass('translate-x-0 opacity-100 transition duration-300 ease-out');
            });

            setTimeout(() => {
                toast.addClass('translate-x-6 opacity-0 transition duration-200 ease-in');
                setTimeout(() => toast.remove(), 3000);
            }, 3500);

            toast.on('click', '.toast-close', () => toast.remove());
        }

        // Display server-side flash messages as toasts
        @if (session('error'))
            $(document).ready(() => showToast('error', @json(session('error'))));
        @endif
        @if (session('success'))
            $(document).ready(() => showToast('success', @json(session('success'))));
        @endif
    </script>
</body>

</html>