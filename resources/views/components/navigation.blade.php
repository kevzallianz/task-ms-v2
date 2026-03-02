<aside id="sidebar"
    class="min-w-60 bg-secondary/10 border-r border-secondary/20 h-screen p-4 space-y-2 transition-all duration-300">
    <div class="flex items-center justify-between mb-5">
        <h1 id="sidebarTitle" class="text-2xl font-bold text-accent">{{ config('app.name', 'Taskivo') }}</h1>
        <button id="sidebarToggle" class="p-1 hover:bg-secondary/20 rounded transition-colors" title="Toggle sidebar">
            <x-heroicon-o-bars-3 class="w-5 h-5 text-foreground" />
        </button>
    </div>
    @if (auth()->user()->isSuperAdmin())
        <nav class="flex flex-col gap-2 items-start w-full">
            <x-nav-links title="Campaigns" href="{{ route('superadmin.campaigns') }}" :active="request()->is('~/campaigns*')">
                <x-heroicon-o-rectangle-stack class="w-5 h-5" />
                <span class="sidebar-text">Campaigns</span>
            </x-nav-links>
            <x-nav-links title="Users" href="{{ route('superadmin.users') }}" :active="request()->is('~/users*')">
                <x-heroicon-o-users class="w-5 h-5" />
                <span class="sidebar-text">Users</span>
            </x-nav-links>
        </nav>
    @endif

    @if (auth()->user()->isUser())
        <nav class="flex flex-col gap-2 items-start w-full">
            <x-nav-links title="Overview" href="{{ route('user.overview') }}" :active="request()->is('overview*')">
                <x-heroicon-o-chart-bar-square class="w-5 h-5" />
                <span class="sidebar-text">Overview</span>
            </x-nav-links>
            <x-nav-links title="My Campaign" href="{{ route('user.campaign') }}" :active="request()->is('campaign*')">
                <x-heroicon-o-rectangle-group class="w-5 h-5" />
                <span class="sidebar-text">My Campaign</span>
            </x-nav-links>
            <x-nav-links title="Projects" href="{{ route('user.projects') }}" :active="request()->is('projects*')">
                <x-heroicon-o-folder-open class="w-5 h-5" />
                <span class="sidebar-text">Projects</span>
            </x-nav-links>
        </nav>
    @endif

    @if (auth()->user()->isAdmin())
        <nav class="flex flex-col gap-2 items-start w-full">
            <x-nav-links title="Campaigns" href="{{ route('admin.campaigns') }}" :active="request()->is('/~/~/campaigns*')">
                <x-heroicon-o-chart-bar-square class="w-5 h-5" />
                <span class="sidebar-text">Campaigns</span>
            </x-nav-links>
            <x-nav-links title="My Campaign" href="{{ route('admin.campaigns.projects') }}" :active="request()->is('campaign*')">
                <x-heroicon-o-rectangle-group class="w-5 h-5" />
                <span class="sidebar-text">Projects</span>
            </x-nav-links>
        </nav>
    @endif
    <form action="{{ route('user.logout') }}" method="POST" class="w-full cursor-pointer">
        @csrf
        <button type="submit"
            class="px-4 py-2 rounded-lg bg-white border border-foreground/20 cursor-pointer text-foreground duration-200 transition-all w-full flex items-center gap-2 text-sm">
            <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 shrink-0" />
            <span class="sidebar-text">Logout</span>
        </button>
    </form>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarTitle = document.getElementById('sidebarTitle');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');

        // Load saved state from localStorage
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

        function toggleSidebar() {
            const collapsed = sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', collapsed);

            if (collapsed) {
                sidebar.classList.remove('min-w-60');
                sidebar.classList.add('min-w-16');
                sidebarTitle.classList.add('hidden');
                sidebarTexts.forEach(text => text.classList.add('hidden'));
            } else {
                sidebar.classList.remove('min-w-16');
                sidebar.classList.add('min-w-60');
                sidebarTitle.classList.remove('hidden');
                sidebarTexts.forEach(text => text.classList.remove('hidden'));
            }
        }

        // Apply saved state on load
        if (isCollapsed) {
            sidebar.classList.add('collapsed', 'min-w-16');
            sidebar.classList.remove('min-w-60');
            sidebarTitle.classList.add('hidden');
            sidebarTexts.forEach(text => text.classList.add('hidden'));
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
    });
</script>
