{{-- Lista "rótulo · barra · total" usada nos cartões da página de Acessos. --}}
@props(['title', 'icon', 'rows' => [], 'empty' => 'Sem dados no período.'])

@php($max = max(1, ...array_values($rows ?: [0])))

<x-ui.card size="sm">
    <x-ui.card.header>
        <x-ui.card.title class="flex items-center gap-2 text-sm">
            <x-dynamic-component :component="'lucide-'.$icon" class="size-4 text-muted-foreground" />
            {{ $title }}
        </x-ui.card.title>
    </x-ui.card.header>
    <x-ui.card.content class="grid gap-2">
        @forelse ($rows as $label => $total)
            <div class="grid gap-1" wire:key="{{ $title }}-{{ $loop->index }}">
                <div class="flex items-center justify-between gap-3 text-sm">
                    <span class="truncate" title="{{ $label }}">{{ $label }}</span>
                    <span class="text-xs font-medium text-muted-foreground tabular-nums">{{ $total }}</span>
                </div>
                <div class="h-1.5 rounded-full bg-muted">
                    <div class="h-full rounded-full bg-primary/70" style="width: {{ max(3, $total / $max * 100) }}%"></div>
                </div>
            </div>
        @empty
            <p class="py-3 text-center text-xs text-muted-foreground">{{ $empty }}</p>
        @endforelse
    </x-ui.card.content>
</x-ui.card>
