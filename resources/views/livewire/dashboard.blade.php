<div class="mx-auto grid max-w-6xl gap-4">
    <div>
        <h1 class="font-heading text-xl font-semibold capitalize">Olá, {{ $firstName }}</h1>
        <p class="text-sm text-muted-foreground">{{ $today }}</p>
    </div>

    <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <a href="{{ route('contacts.index', $stat['filter'] === 'todos' ? [] : ['filter' => $stat['filter']]) }}" wire:navigate wire:key="stat-{{ $loop->index }}">
                <x-ui.card size="sm" class="h-full transition hover:ring-primary/50">
                    <x-ui.card.content class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-muted-foreground">{{ $stat['title'] }}</p>
                            <span class="flex size-8 items-center justify-center rounded-lg {{ $stat['iconClass'] }}">
                                <x-dynamic-component :component="'lucide-'.$stat['icon']" class="size-4" />
                            </span>
                        </div>
                        <p class="font-heading text-xl font-semibold">{{ $stat['value'] }}</p>
                        <p class="text-xs text-muted-foreground">{{ $stat['hint'] }}</p>
                    </x-ui.card.content>
                </x-ui.card>
            </a>
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_1.4fr]">
        <x-ui.card>
            <x-ui.card.header>
                <x-ui.card.title>Contatos por mês</x-ui.card.title>
                <p class="text-xs text-muted-foreground">Últimos 6 meses</p>
            </x-ui.card.header>
            <x-ui.card.content>
                @php($max = max(1, ...array_column($months, 'total')))
                <div class="flex h-36 items-end gap-3">
                    @foreach ($months as $month)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-1.5" title="{{ $month['total'] }} em {{ $month['label'] }}">
                            <span class="text-xs font-medium">{{ $month['total'] }}</span>
                            <div @class(['w-full max-w-8 rounded-md', $loop->last ? 'bg-primary' : 'bg-muted']) style="height: {{ max(4, $month['total'] / $max * 100) }}%"></div>
                            <span class="text-xs text-muted-foreground">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card.content>
        </x-ui.card>

        <x-ui.card>
            <x-ui.card.header class="flex-row items-center justify-between">
                <x-ui.card.title class="flex items-center gap-2">
                    <x-lucide-inbox class="size-4" />
                    Últimos contatos
                </x-ui.card.title>
                <a href="{{ route('contacts.index') }}" wire:navigate class="text-xs font-medium text-indigo-700 hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-indigo-200">Ver todos</a>
            </x-ui.card.header>
            <x-ui.card.content class="grid gap-1">
                @forelse ($latest as $contact)
                    <a href="{{ route('contacts.show', $contact) }}" wire:navigate class="-mx-2 flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-muted/60">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-indigo-500/15 text-xs font-medium text-indigo-700 dark:text-indigo-300">
                            {{ $contact->initials() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium">{{ $contact->name }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ $contact->company ?: $contact->email }}</p>
                        </div>
                        <x-contacts.status-badge :status="$contact->status" />
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-muted-foreground">Nenhum contato ainda. Eles chegam pelo formulário do site.</p>
                @endforelse
            </x-ui.card.content>
        </x-ui.card>
    </div>
</div>
