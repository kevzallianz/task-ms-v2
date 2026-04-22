<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('components.head')

<body>
    @yield('content')

    <div id="toast-container" class="fixed top-6 right-6 z-50 space-y-3"></div>

    {{-- User hover card --}}
    <div id="user-hover-card" class="pointer-events-none fixed z-[9999] hidden w-80 rounded-2xl border border-gray-100 bg-white p-5 shadow-2xl">
        <div class="flex flex-col items-center gap-3">
            <div id="uhc-avatar" class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary/20 text-3xl font-bold text-primary ring-4 ring-primary/20"></div>
            <div class="text-center min-w-0">
                <p id="uhc-name" class="truncate text-base font-semibold text-gray-900"></p>
                <p id="uhc-username" class="truncate text-sm text-gray-400"></p>
            </div>
        </div>
        <p id="uhc-bio" class="mt-3 line-clamp-4 text-sm leading-relaxed text-gray-500"></p>
    </div>

    <script>
    (function () {
        const card    = document.getElementById('user-hover-card');
        const elName  = document.getElementById('uhc-name');
        const elUser  = document.getElementById('uhc-username');
        const elBio   = document.getElementById('uhc-bio');
        const elAvt   = document.getElementById('uhc-avatar');
        let hideTimer;

        function show(trigger) {
            clearTimeout(hideTimer);
            const { name, username, bio, avatar, initial } = trigger.dataset;

            elName.textContent = name || 'Unknown';
            elUser.textContent = username ? '@' + username : '';
            elUser.classList.toggle('hidden', !username);
            elBio.textContent  = bio || '';
            elBio.classList.toggle('hidden', !bio);
            elAvt.innerHTML    = avatar
                ? `<img src="${avatar}" alt="${name}" class="w-full h-full object-cover" />`
                : (initial || '?');

            card.classList.remove('hidden');
            position(trigger);
        }

        function position(trigger) {
            const r   = trigger.getBoundingClientRect();
            const cw  = card.offsetWidth  || 240;
            const ch  = card.offsetHeight || 80;
            const gap = 8;

            let left = r.left + r.width / 2 - cw / 2;
            left = Math.max(8, Math.min(left, window.innerWidth - cw - 8));

            const above = r.top >= ch + gap + 4;
            const top   = above ? r.top - ch - gap : r.bottom + gap;

            card.style.left = left + 'px';
            card.style.top  = top  + 'px';
        }

        function hide() {
            hideTimer = setTimeout(() => card.classList.add('hidden'), 120);
        }

        document.addEventListener('mouseover', function (e) {
            const t = e.target.closest('.user-avatar-trigger');
            if (t) show(t);
        });

        document.addEventListener('mouseout', function (e) {
            if (e.target.closest('.user-avatar-trigger')) hide();
        });
    })();
    </script>

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