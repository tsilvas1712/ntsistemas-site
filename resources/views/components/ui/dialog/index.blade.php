{{--
    Modal centralizado (Alpine). Controle o estado com x-model (Alpine) ou wire:model (Livewire):

    <div x-data="{ open: false }">
        <x-ui.button @click="open = true">Abrir</x-ui.button>
        <x-ui.dialog x-model="open">
            <x-ui.dialog.header>
                <x-ui.dialog.title>Título</x-ui.dialog.title>
                <x-ui.dialog.description>Descrição</x-ui.dialog.description>
            </x-ui.dialog.header>
            <x-ui.dialog.footer>…</x-ui.dialog.footer>
        </x-ui.dialog>
    </div>

    Fecha com Esc, clique no fundo ou no X. O foco fica preso no modal e o scroll da página é travado.
--}}
@props(['showCloseButton' => true])

<div
    x-data="{ open: false }"
    x-modelable="open"
    x-id="['overlay-title', 'overlay-description']"
    {{ $attributes->except('class') }}
>
    <template x-teleport="body">
        <div x-show="open" x-cloak @keydown.escape.window="open = false">
            <div
                x-show="open"
                x-transition:enter="animate-in fade-in-0 duration-100"
                x-transition:leave="animate-out fade-out-0 duration-100"
                data-slot="dialog-overlay"
                class="fixed inset-0 isolate z-50 bg-black/10 supports-backdrop-filter:backdrop-blur-xs"
                @click="open = false"
            ></div>
            <div
                x-show="open"
                x-trap.inert.noscroll="open"
                x-transition:enter="animate-in fade-in-0 zoom-in-95 duration-100"
                x-transition:leave="animate-out fade-out-0 zoom-out-95 duration-100"
                role="dialog"
                aria-modal="true"
                tabindex="-1"
                data-slot="dialog-content"
                :aria-labelledby="$id('overlay-title')"
                :aria-describedby="$id('overlay-description')"
                {{ $attributes->only('class')->cn('fixed top-1/2 left-1/2 z-50 grid w-full max-w-[calc(100%-2rem)] -translate-x-1/2 -translate-y-1/2 gap-4 rounded-xl bg-popover p-4 text-sm text-popover-foreground ring-1 ring-foreground/10 outline-none sm:max-w-sm') }}
            >
                {{ $slot }}

                @if ($showCloseButton)
                    <x-ui.button type="button" variant="ghost" size="icon-sm" class="absolute top-2 right-2" data-slot="dialog-close" @click="open = false">
                        <x-lucide-x />
                        <span class="sr-only">Fechar</span>
                    </x-ui.button>
                @endif
            </div>
        </div>
    </template>
</div>
