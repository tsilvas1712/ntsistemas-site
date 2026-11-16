<div class="mx-auto grid max-w-6xl gap-4">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-heading text-xl font-semibold">Contatos</h1>
            <p class="text-sm text-muted-foreground">
                {{ $this->contacts->total() }} {{ Str::plural('registro', $this->contacts->total()) }}
            </p>
        </div>
        <x-ui.button size="sm" variant="outline" wire:click="export">
            <x-lucide-download data-icon="inline-start" />
            Exportar CSV
        </x-ui.button>
    </div>

    <div class="relative">
        <x-lucide-search class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
        <x-ui.input wire:model.live.debounce.250ms="search" placeholder="Buscar por nome, e-mail, empresa ou telefone" class="pl-8" />
    </div>

    <div class="flex flex-wrap gap-2">
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
                @php($count = $value === 'todos' ? array_sum($this->counts) : ($this->counts[$value] ?? 0))
                <span class="ml-1 rounded-full bg-current/15 px-1.5 text-[0.7rem]">{{ $count }}</span>
            </x-ui.button>
        @endforeach
    </div>

    @if ($this->contacts->isEmpty())
        <div class="grid justify-items-center gap-2 rounded-xl bg-card px-6 py-14 text-center ring-1 ring-foreground/10">
            <x-lucide-inbox class="size-8 text-muted-foreground" />
            <p class="font-medium">Nenhum contato encontrado</p>
            <p class="text-sm text-muted-foreground">Os contatos enviados pelo formulário do site aparecem aqui.</p>
        </div>
    @else
        {{-- Celular: cartões --}}
        <div class="grid gap-3 md:hidden">
            @foreach ($this->contacts as $contact)
                <a
                    href="{{ route('contacts.show', $contact) }}"
                    wire:navigate
                    wire:key="card-{{ $contact->id }}"
                    class="flex items-center gap-3 rounded-xl bg-card p-3 shadow-sm ring-1 ring-foreground/10"
                >
                    <x-ui.avatar>
                        <x-ui.avatar.fallback class="bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">{{ $contact->initials() }}</x-ui.avatar.fallback>
                    </x-ui.avatar>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium">{{ $contact->name }}</p>
                        <p class="truncate text-xs text-muted-foreground">{{ $contact->company ?: $contact->email }} · {{ $contact->created_at->format('d/m') }}</p>
                    </div>
                    <x-contacts.status-badge :status="$contact->status" />
                    <x-lucide-chevron-right class="size-4 text-muted-foreground" />
                </a>
            @endforeach
        </div>

        {{-- Desktop: tabela --}}
        <div class="hidden overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10 md:block">
            <x-ui.table>
                <x-ui.table.header>
                    <x-ui.table.row>
                        <x-ui.table.head>Contato</x-ui.table.head>
                        <x-ui.table.head>Empresa</x-ui.table.head>
                        <x-ui.table.head>Interesse</x-ui.table.head>
                        <x-ui.table.head>Recebido</x-ui.table.head>
                        <x-ui.table.head>Status</x-ui.table.head>
                    </x-ui.table.row>
                </x-ui.table.header>
                <x-ui.table.body>
                    @foreach ($this->contacts as $contact)
                        <x-ui.table.row
                            class="cursor-pointer"
                            wire:key="row-{{ $contact->id }}"
                            x-on:click="Livewire.navigate('{{ route('contacts.show', $contact) }}')"
                        >
                            <x-ui.table.cell>
                                <div class="flex items-center gap-3">
                                    <x-ui.avatar size="sm">
                                        <x-ui.avatar.fallback class="bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">{{ $contact->initials() }}</x-ui.avatar.fallback>
                                    </x-ui.avatar>
                                    <div>
                                        <p class="font-medium">{{ $contact->name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ $contact->email }}</p>
                                    </div>
                                </div>
                            </x-ui.table.cell>
                            <x-ui.table.cell>{{ $contact->company ?: '—' }}</x-ui.table.cell>
                            <x-ui.table.cell class="max-w-56 truncate text-muted-foreground">{{ implode(', ', $contact->serviceLabels()) ?: '—' }}</x-ui.table.cell>
                            <x-ui.table.cell class="text-muted-foreground" title="{{ $contact->created_at->format('d/m/Y H:i') }}">{{ $contact->created_at->diffForHumans() }}</x-ui.table.cell>
                            <x-ui.table.cell><x-contacts.status-badge :status="$contact->status" /></x-ui.table.cell>
                        </x-ui.table.row>
                    @endforeach
                </x-ui.table.body>
            </x-ui.table>
        </div>

        {{ $this->contacts->links() }}
    @endif
</div>
