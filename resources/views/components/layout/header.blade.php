@php
    $displayName = auth()->user()?->name ?? 'Admin';
@endphp

<header class="sticky top-0 z-30 flex items-center gap-3 border-b bg-card px-4 py-3">
    <x-ui.button type="button" variant="secondary" size="icon" class="lg:hidden" aria-label="Abrir menu" @click="mobileOpen = true">
        <x-lucide-menu />
    </x-ui.button>

    <div class="flex min-w-0 flex-1 items-center gap-2 lg:hidden">
        <x-brand.mark class="size-8" />
        <div class="min-w-0">
            <p class="font-heading text-sm font-semibold">NT Sistemas Web</p>
            <p class="hidden text-xs text-muted-foreground sm:block">Painel</p>
        </div>
    </div>

    <x-ui.button
        type="button"
        variant="secondary"
        size="icon"
        class="ml-auto"
        x-data="{ dark: document.documentElement.classList.contains('dark') }"
        x-bind:aria-label="dark ? 'Usar tema claro' : 'Usar tema escuro'"
        x-bind:title="dark ? 'Tema claro' : 'Tema escuro'"
        @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); try { localStorage.setItem('admin-theme', dark ? 'dark' : 'light') } catch (e) {}"
    >
        <x-lucide-moon x-show="!dark" />
        <x-lucide-sun x-show="dark" x-cloak />
    </x-ui.button>

    <x-ui.button variant="secondary" size="icon" aria-label="Contatos" href="{{ route('contacts.index', ['filter' => 'novo']) }}" wire:navigate>
        <x-lucide-bell />
    </x-ui.button>

    <x-ui.dropdown-menu align="end" content-class="min-w-48">
        <x-slot:trigger
            aria-label="Conta"
            class="inline-flex size-8 items-center justify-center rounded-lg bg-secondary outline-none focus-visible:ring-3 focus-visible:ring-ring/50"
        >
            <x-ui.avatar size="sm">
                <x-ui.avatar.fallback class="bg-primary text-primary-foreground">
                    {{ App\Models\User::initialsFor($displayName) }}
                </x-ui.avatar.fallback>
            </x-ui.avatar>
        </x-slot:trigger>

        <x-ui.dropdown-menu.label class="capitalize">{{ $displayName }}</x-ui.dropdown-menu.label>
        <x-ui.dropdown-menu.separator />
        <x-ui.dropdown-menu.item href="{{ route('settings') }}" wire:navigate>Ajustes do site</x-ui.dropdown-menu.item>
        <x-ui.dropdown-menu.item href="{{ route('users.show', auth()->user()) }}" wire:navigate>Minha conta</x-ui.dropdown-menu.item>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.dropdown-menu.item type="submit">Sair</x-ui.dropdown-menu.item>
        </form>
    </x-ui.dropdown-menu>
</header>
