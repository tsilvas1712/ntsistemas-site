<div class="mx-auto grid max-w-6xl gap-4">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-heading text-xl font-semibold">Usuários</h1>
            <p class="text-sm text-muted-foreground">
                {{ $this->users->count() }} {{ Str::plural('registro', $this->users->count()) }}
            </p>
        </div>
        <x-ui.button size="sm" href="{{ route('users.create') }}" wire:navigate>
            <x-lucide-plus data-icon="inline-start" />
            Novo
        </x-ui.button>
    </div>

    <div class="relative">
        <x-lucide-search class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
        <x-ui.input wire:model.live.debounce.250ms="search" placeholder="Buscar usuários" class="pl-8" />
    </div>

    <div class="flex gap-2">
        @foreach ($filters as $value => $label)
            <x-ui.button
                type="button"
                size="sm"
                :variant="$filter === $value ? 'default' : 'outline'"
                class="rounded-full"
                wire:click="setFilter('{{ $value }}')"
                wire:key="filter-{{ $value }}"
            >
                {{ $label }}
            </x-ui.button>
        @endforeach
    </div>

    {{-- Celular: cartões --}}
    <div class="grid gap-3 md:hidden">
        @foreach ($this->users as $user)
            <a
                href="{{ route('users.show', $user) }}"
                wire:navigate
                wire:key="card-{{ $user->id }}"
                class="flex items-center gap-3 rounded-xl bg-card p-3 shadow-sm ring-1 ring-foreground/10"
            >
                <x-ui.avatar>
                    <x-ui.avatar.fallback class="bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">{{ $user->initials() }}</x-ui.avatar.fallback>
                </x-ui.avatar>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium">{{ $user->name }}</p>
                    <p class="truncate text-xs text-muted-foreground">{{ $user->email }}</p>
                </div>
                <x-users.status-badge :status="$user->status" />
                <x-lucide-chevron-right class="size-4 text-muted-foreground" />
            </a>
        @endforeach
    </div>

    {{-- Desktop: tabela --}}
    <div class="hidden overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10 md:block">
        <x-ui.table>
            <x-ui.table.header>
                <x-ui.table.row>
                    <x-ui.table.head>Usuário</x-ui.table.head>
                    <x-ui.table.head>Perfil</x-ui.table.head>
                    <x-ui.table.head>Status</x-ui.table.head>
                    <x-ui.table.head class="text-right">Ações</x-ui.table.head>
                </x-ui.table.row>
            </x-ui.table.header>
            <x-ui.table.body>
                @foreach ($this->users as $user)
                    <x-ui.table.row
                        class="cursor-pointer"
                        wire:key="row-{{ $user->id }}"
                        x-on:click="Livewire.navigate('{{ route('users.show', $user) }}')"
                    >
                        <x-ui.table.cell>
                            <div class="flex items-center gap-3">
                                <x-ui.avatar size="sm">
                                    <x-ui.avatar.fallback class="bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">{{ $user->initials() }}</x-ui.avatar.fallback>
                                </x-ui.avatar>
                                <div>
                                    <p class="font-medium">{{ $user->name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $user->email }}</p>
                                </div>
                            </div>
                        </x-ui.table.cell>
                        <x-ui.table.cell>{{ $user->role->label() }}</x-ui.table.cell>
                        <x-ui.table.cell>
                            <x-users.status-badge :status="$user->status" />
                        </x-ui.table.cell>
                        <x-ui.table.cell class="text-right">
                            <x-ui.button variant="ghost" size="sm" href="{{ route('users.edit', $user) }}" wire:navigate x-on:click.stop>
                                Editar
                            </x-ui.button>
                        </x-ui.table.cell>
                    </x-ui.table.row>
                @endforeach
            </x-ui.table.body>
        </x-ui.table>
    </div>
</div>
