<div class="mx-auto grid max-w-6xl gap-4" x-data="{ detail: false }">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="font-heading text-xl font-semibold">Acessos</h1>
            <p class="text-sm text-muted-foreground">Quem visitou o site, de onde veio e onde clicou. Acessos seus (logado) não entram.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach ($periods as $value => $label)
                <x-ui.button
                    type="button"
                    size="sm"
                    :variant="$period === $value ? 'default' : 'outline'"
                    class="rounded-full"
                    wire:click="setPeriod('{{ $value }}')"
                    wire:key="period-{{ $value }}"
                >{{ $label }}</x-ui.button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach ($this->stats as $stat)
            <x-ui.card size="sm" wire:key="stat-{{ $loop->index }}">
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
        @endforeach
    </div>

    <x-ui.card>
        <x-ui.card.header>
            <x-ui.card.title>Visualizações {{ $period === '24h' ? 'por hora' : 'por dia' }}</x-ui.card.title>
            <p class="text-xs text-muted-foreground">Últimos {{ $periods[$period] }}</p>
        </x-ui.card.header>
        <x-ui.card.content>
            @php($series = $this->series)
            @php($max = max(1, ...array_column($series, 'total')))
            @php($every = (int) ceil(count($series) / 12))
            <div class="flex h-40 items-end gap-0.5 sm:gap-1">
                @foreach ($series as $point)
                    <div class="group flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-1" title="{{ $point['total'] }} em {{ $point['title'] }}">
                        <span class="hidden text-[0.65rem] font-medium group-hover:block">{{ $point['total'] }}</span>
                        <div @class(['w-full max-w-8 rounded-sm', $point['total'] ? 'bg-primary/80 group-hover:bg-primary' : 'bg-muted']) style="height: {{ max(2, $point['total'] / $max * 100) }}%"></div>
                        <span @class(['text-[0.65rem] text-muted-foreground whitespace-nowrap', 'invisible' => $loop->index % $every !== 0])>{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-ui.card.content>
    </x-ui.card>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($this->breakdowns as $group)
            <x-analytics.breakdown :title="$group['title']" :icon="$group['icon']" :rows="$group['rows']" wire:key="bd-{{ $loop->index }}" />
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-[1.6fr_1fr]">
        <x-ui.card>
            <x-ui.card.header>
                <x-ui.card.title class="flex items-center gap-2">
                    <x-lucide-mouse-pointer-click class="size-4" />
                    Onde mais clicaram
                </x-ui.card.title>
            </x-ui.card.header>
            <x-ui.card.content class="grid gap-1">
                @forelse ($this->topClicks as $click)
                    <div class="-mx-2 flex items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-muted/60" wire:key="tc-{{ $loop->index }}">
                        <span class="w-6 text-right text-xs text-muted-foreground tabular-nums">{{ $loop->iteration }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $click['label'] }}</p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ $click['section'] ? 'em '.$click['section'] : '' }}{{ $click['section'] && $click['href'] ? ' · ' : '' }}{{ $click['href'] }}
                            </p>
                        </div>
                        <x-ui.badge variant="secondary" class="tabular-nums">{{ $click['total'] }}</x-ui.badge>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-muted-foreground">Nenhum clique registrado no período.</p>
                @endforelse
            </x-ui.card.content>
        </x-ui.card>

        <div class="grid content-start gap-4">
            <x-analytics.breakdown title="Cliques por seção" icon="layout-panel-top" :rows="$this->clicksBySection" />

            <x-ui.card size="sm">
                <x-ui.card.header>
                    <x-ui.card.title class="flex items-center gap-2 text-sm">
                        <x-lucide-network class="size-4 text-muted-foreground" />
                        IPs com mais acessos
                    </x-ui.card.title>
                </x-ui.card.header>
                <x-ui.card.content class="grid gap-1">
                    @forelse ($this->topIps as $ip => $total)
                        <button type="button" wire:click="filterIp('{{ $ip }}')" wire:key="ip-{{ $loop->index }}"
                                class="-mx-2 flex items-center justify-between gap-3 rounded-lg px-2 py-1 text-left text-sm hover:bg-muted/60">
                            <span class="truncate font-mono text-xs">{{ $ip }}</span>
                            <span class="text-xs text-muted-foreground tabular-nums">{{ $total }}</span>
                        </button>
                    @empty
                        <p class="py-3 text-center text-xs text-muted-foreground">Sem dados no período.</p>
                    @endforelse
                </x-ui.card.content>
            </x-ui.card>
        </div>
    </div>

    {{-- Lista de acessos --}}
    <div class="grid gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <h2 class="font-heading font-semibold">Últimos acessos</h2>
            <span class="text-sm text-muted-foreground">{{ $this->list->total() }} no período</span>
            <label class="ml-auto flex items-center gap-2 text-sm text-muted-foreground">
                <x-ui.checkbox wire:model.live="bots" />
                Incluir robôs
            </label>
        </div>

        <div class="relative">
            <x-lucide-search class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
            <x-ui.input wire:model.live.debounce.250ms="search" placeholder="Buscar por IP, navegador, sistema ou origem" class="pl-8" />
        </div>

        @if ($this->list->isEmpty())
            <div class="grid justify-items-center gap-2 rounded-xl bg-card px-6 py-14 text-center ring-1 ring-foreground/10">
                <x-lucide-activity class="size-8 text-muted-foreground" />
                <p class="font-medium">Nenhum acesso encontrado</p>
                <p class="text-sm text-muted-foreground">As visitas ao site aparecem aqui assim que acontecerem.</p>
            </div>
        @else
            {{-- Celular: cartões --}}
            <div class="grid gap-2 md:hidden">
                @foreach ($this->list as $visit)
                    <button type="button" wire:click="select({{ $visit->id }})" x-on:click="detail = true" wire:key="vc-{{ $visit->id }}"
                            class="flex items-center gap-3 rounded-xl bg-card p-3 text-left shadow-sm ring-1 ring-foreground/10">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-mono text-xs">{{ $visit->ip }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ $visit->browser ?? '?' }} · {{ $visit->os ?? '?' }} · {{ $visit->sourceLabel() }}</p>
                        </div>
                        <div class="text-right text-xs text-muted-foreground">
                            <p>{{ $visit->created_at->format('d/m H:i') }}</p>
                            <p>{{ $visit->clicks_count }} {{ Str::plural('clique', $visit->clicks_count) }}</p>
                        </div>
                    </button>
                @endforeach
            </div>

            {{-- Desktop: tabela --}}
            <div class="hidden overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10 md:block">
                <x-ui.table>
                    <x-ui.table.header>
                        <x-ui.table.row>
                            <x-ui.table.head>Quando</x-ui.table.head>
                            <x-ui.table.head>IP</x-ui.table.head>
                            <x-ui.table.head>Navegador</x-ui.table.head>
                            <x-ui.table.head>Aparelho</x-ui.table.head>
                            <x-ui.table.head>Origem</x-ui.table.head>
                            <x-ui.table.head class="text-right">Cliques</x-ui.table.head>
                        </x-ui.table.row>
                    </x-ui.table.header>
                    <x-ui.table.body>
                        @foreach ($this->list as $visit)
                            <x-ui.table.row class="cursor-pointer" wire:key="vr-{{ $visit->id }}" wire:click="select({{ $visit->id }})" x-on:click="detail = true">
                                <x-ui.table.cell class="text-muted-foreground" title="{{ $visit->created_at->format('d/m/Y H:i:s') }}">{{ $visit->created_at->format('d/m H:i') }}</x-ui.table.cell>
                                <x-ui.table.cell class="font-mono text-xs">{{ $visit->ip }}</x-ui.table.cell>
                                <x-ui.table.cell>{{ trim(($visit->browser ?? '—').' '.$visit->browser_version) }} <span class="text-muted-foreground">· {{ $visit->os ?? '—' }}</span></x-ui.table.cell>
                                <x-ui.table.cell>
                                    {{ $visit->deviceLabel() }}
                                </x-ui.table.cell>
                                <x-ui.table.cell class="max-w-48 truncate text-muted-foreground">{{ $visit->sourceLabel() }}</x-ui.table.cell>
                                <x-ui.table.cell class="text-right tabular-nums">{{ $visit->clicks_count }}</x-ui.table.cell>
                            </x-ui.table.row>
                        @endforeach
                    </x-ui.table.body>
                </x-ui.table>
            </div>

            {{ $this->list->links() }}
        @endif
    </div>

    {{-- Detalhe de um acesso --}}
    <x-ui.sheet x-model="detail" side="right" class="w-full overflow-y-auto p-5 sm:max-w-md">
        @if ($visit = $this->visit)
            <x-ui.sheet.header class="p-0">
                <x-ui.sheet.title>Acesso #{{ $visit->id }}</x-ui.sheet.title>
                <x-ui.sheet.description>{{ $visit->created_at->format('d/m/Y \à\s H:i:s') }}</x-ui.sheet.description>
            </x-ui.sheet.header>

            <dl class="grid grid-cols-2 gap-3 text-sm">
                @foreach ([
                    'IP' => $visit->ip,
                    'Página' => $visit->path,
                    'Navegador' => trim(($visit->browser ?? '—').' '.$visit->browser_version),
                    'Sistema' => $visit->os,
                    'Aparelho' => $visit->deviceLabel(),
                    'Origem' => $visit->sourceLabel(),
                    'Campanha' => collect([$visit->utm_medium, $visit->utm_campaign])->filter()->implode(' · '),
                ] as $label => $value)
                    <div class="grid gap-0.5">
                        <dt class="text-xs text-muted-foreground">{{ $label }}</dt>
                        <dd class="font-medium break-words">{{ $value ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>

            @if ($this->sameIpCount > 1)
                <x-ui.button size="sm" variant="outline" wire:click="filterIp('{{ $visit->ip }}')" x-on:click="detail = false">
                    <x-lucide-history data-icon="inline-start" />
                    Ver os {{ $this->sameIpCount }} acessos deste IP
                </x-ui.button>
            @endif

            <div class="grid gap-1 border-t pt-3">
                <p class="text-xs text-muted-foreground">User-Agent</p>
                <p class="font-mono text-xs break-all text-muted-foreground">{{ $visit->user_agent ?? '—' }}</p>
            </div>

            <div class="grid gap-2 border-t pt-3">
                <p class="text-xs text-muted-foreground">Cliques ({{ $visit->clicks->count() }})</p>
                @forelse ($visit->clicks as $click)
                    <div class="flex gap-3" wire:key="vclk-{{ $click->id }}">
                        <span class="pt-0.5 font-mono text-[0.7rem] text-muted-foreground">{{ $click->created_at->format('H:i:s') }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium">{{ $click->label ?? 'Sem texto' }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ collect([$click->section ? 'em '.$click->section : null, $click->href])->filter()->implode(' · ') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">Nenhum clique nesta visita.</p>
                @endforelse
            </div>
        @else
            <x-ui.sheet.title class="sr-only">Acesso</x-ui.sheet.title>
            <p class="text-sm text-muted-foreground">Carregando…</p>
        @endif
    </x-ui.sheet>
</div>
