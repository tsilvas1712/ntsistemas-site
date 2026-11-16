{{--
    Painel lateral (gaveta). Mesmo uso do Dialog, com "side": top · right · bottom · left.

    <x-ui.sheet x-model="mobileOpen" side="left" :show-close-button="false" class="w-72">
        <x-ui.sheet.title class="sr-only">Navegação</x-ui.sheet.title>
        …
    </x-ui.sheet>
--}}
@props(['side' => 'right', 'showCloseButton' => true])

@php
    $slide = [
        'top' => ['slide-in-from-top-10', 'slide-out-to-top-10'],
        'right' => ['slide-in-from-right-10', 'slide-out-to-right-10'],
        'bottom' => ['slide-in-from-bottom-10', 'slide-out-to-bottom-10'],
        'left' => ['slide-in-from-left-10', 'slide-out-to-left-10'],
    ][$side] ?? ['slide-in-from-right-10', 'slide-out-to-right-10'];
@endphp

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
                data-slot="sheet-overlay"
                class="fixed inset-0 z-50 bg-black/10 supports-backdrop-filter:backdrop-blur-xs"
                @click="open = false"
            ></div>
            <div
                x-show="open"
                x-trap.inert.noscroll="open"
                x-transition:enter="animate-in fade-in-0 duration-200 {{ $slide[0] }}"
                x-transition:leave="animate-out fade-out-0 duration-200 {{ $slide[1] }}"
                role="dialog"
                aria-modal="true"
                tabindex="-1"
                data-slot="sheet-content"
                data-side="{{ $side }}"
                :aria-labelledby="$id('overlay-title')"
                {{ $attributes->only('class')->cn('fixed z-50 flex flex-col gap-4 bg-popover bg-clip-padding text-sm text-popover-foreground shadow-lg outline-none data-[side=bottom]:inset-x-0 data-[side=bottom]:bottom-0 data-[side=bottom]:h-auto data-[side=bottom]:border-t data-[side=left]:inset-y-0 data-[side=left]:left-0 data-[side=left]:h-full data-[side=left]:w-3/4 data-[side=left]:border-r data-[side=right]:inset-y-0 data-[side=right]:right-0 data-[side=right]:h-full data-[side=right]:w-3/4 data-[side=right]:border-l data-[side=top]:inset-x-0 data-[side=top]:top-0 data-[side=top]:h-auto data-[side=top]:border-b data-[side=left]:sm:max-w-sm data-[side=right]:sm:max-w-sm') }}
            >
                {{ $slot }}

                @if ($showCloseButton)
                    <x-ui.button type="button" variant="ghost" size="icon-sm" class="absolute top-3 right-3" data-slot="sheet-close" @click="open = false">
                        <x-lucide-x />
                        <span class="sr-only">Fechar</span>
                    </x-ui.button>
                @endif
            </div>
        </div>
    </template>
</div>
