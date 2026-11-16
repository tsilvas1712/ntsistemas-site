@php
    $displayName = auth()->user()?->name ?? 'Admin';
    $badges = ['new_contacts' => App\Models\Contact::where('status', App\Enums\ContactStatus::Novo)->count()];
@endphp

<div class="flex h-full flex-col border-r border-sidebar-border bg-sidebar text-sidebar-foreground">
    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-5">
        <x-brand.mark class="size-9" />
        <div class="min-w-0">
            <p class="font-heading text-sm font-semibold">NT Sistemas Web</p>
            <p class="text-xs text-muted-foreground">Painel</p>
        </div>
    </a>

    <nav class="flex flex-1 flex-col gap-1 px-3">
        @foreach (config('adminkit.nav') as $item)
            @php($active = request()->routeIs($item['active']))
            <a
                href="{{ route($item['route']) }}"
                wire:navigate
                @if ($active) aria-current="page" @endif
                @class([
                    'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors',
                    'bg-sidebar-accent font-medium text-sidebar-accent-foreground' => $active,
                    'text-muted-foreground hover:bg-sidebar-accent/70 hover:text-sidebar-foreground' => ! $active,
                ])
            >
                <x-dynamic-component :component="'lucide-'.$item['icon']" class="size-4.5" />
                {{ $item['label'] }}
                @if (($count = $badges[$item['badge'] ?? ''] ?? 0) > 0)
                    <span class="ml-auto rounded-full bg-sidebar-primary px-2 py-0.5 text-[0.7rem] font-semibold text-sidebar-primary-foreground">{{ $count }}</span>
                @endif
            </a>
        @endforeach

        <a href="{{ route('home') }}" target="_blank" class="mt-2 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-muted-foreground transition-colors hover:bg-sidebar-accent/70 hover:text-sidebar-foreground">
            <x-lucide-external-link class="size-4.5" />
            Ver site
        </a>
    </nav>

    <div class="p-3">
        <div class="flex items-center gap-2.5 rounded-lg bg-sidebar-accent px-2.5 py-2.5">
            <x-ui.avatar size="sm">
                <x-ui.avatar.fallback class="bg-sidebar-primary text-sidebar-primary-foreground">
                    {{ App\Models\User::initialsFor($displayName) }}
                </x-ui.avatar.fallback>
            </x-ui.avatar>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium capitalize">{{ $displayName }}</p>
                <p class="text-xs text-muted-foreground">{{ auth()->user()?->role?->label() ?? 'Admin' }}</p>
            </div>
        </div>
    </div>
</div>
