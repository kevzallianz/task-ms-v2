@props(["href" => "#", "active" => false])

<a href="{{ $href }}" class="px-4 py-2 rounded-lg bg-white border border-foreground/20 text-foreground duration-200 transition-all w-full flex items-center gap-2 text-sm {{ $active ? 'bg-primary! text-white!' : '' }}">{{ $slot }}</a>