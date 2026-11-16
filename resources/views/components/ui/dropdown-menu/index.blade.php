{{--
    Menu suspenso (Alpine + x-anchor). O botão que abre vai no slot "trigger".

    <x-ui.dropdown-menu align="end" content-class="min-w-48">
        <x-slot:trigger aria-label="Conta" class="…">…</x-slot:trigger>

        <x-ui.dropdown-menu.label>Thiago</x-ui.dropdown-menu.label>
        <x-ui.dropdown-menu.separator />
        <x-ui.dropdown-menu.item href="{{ route('settings') }}" wire:navigate>Configurações</x-ui.dropdown-menu.item>
        <x-ui.dropdown-menu.item variant="destructive" @click="…">Excluir</x-ui.dropdown-menu.item>
    </x-ui.dropdown-menu>

    Teclado: Enter/Espaço/↓ abre · ↑ ↓ Home End navegam · Esc fecha.
--}}
@props(['align' => 'start', 'contentClass' => null])

@php($placement = $align === 'end' ? 'bottom-end' : 'bottom-start')

<div
    data-slot="dropdown-menu"
    x-data="{
        open: false,
        items() {
            return [...($refs.content?.querySelectorAll('[role=menuitem]:not([data-disabled])') ?? [])]
        },
        focusItem(position) {
            this.$nextTick(() => {
                const items = this.items()
                if (! items.length) return
                ;(position === 'last' ? items[items.length - 1] : items[0]).focus()
            })
        },
        toggle(event) {
            this.open = ! this.open
            if (! this.open) return
            // Aberto pelo teclado → foca o 1º item; pelo mouse → foca o menu.
            if (event.detail === 0) this.focusItem('first')
            else this.$nextTick(() => $refs.content.focus())
        },
        openWith(position) {
            this.open = true
            this.focusItem(position)
        },
        close(focusTrigger = false) {
            this.open = false
            if (focusTrigger) $refs.trigger.focus()
        },
        move(step) {
            const items = this.items()
            const index = items.indexOf(document.activeElement)
            items[(index + step + items.length) % items.length]?.focus()
        },
    }"
    {{ $attributes->cn('relative inline-flex') }}
>
    <button
        type="button"
        x-ref="trigger"
        data-slot="dropdown-menu-trigger"
        aria-haspopup="menu"
        :aria-expanded="open"
        :data-state="open ? 'open' : 'closed'"
        @click="toggle($event)"
        @keydown.down.prevent="openWith('first')"
        @keydown.up.prevent="openWith('last')"
        {{ $trigger->attributes }}
    >{{ $trigger }}</button>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-ref="content"
            x-anchor.{{ $placement }}.offset.4="$refs.trigger"
            x-transition:enter="animate-in fade-in-0 zoom-in-95 slide-in-from-top-2 duration-100"
            x-transition:leave="animate-out fade-out-0 zoom-out-95 duration-100"
            role="menu"
            tabindex="-1"
            data-slot="dropdown-menu-content"
            data-state="open"
            @click.outside="if (! $refs.trigger.contains($event.target)) close()"
            @keydown.down.prevent="move(1)"
            @keydown.up.prevent="move(-1)"
            @keydown.home.prevent="items()[0]?.focus()"
            @keydown.end.prevent="items().at(-1)?.focus()"
            @keydown.escape.prevent.stop="close(true)"
            @keydown.tab="close()"
            class="{{ cn('z-50 min-w-32 overflow-x-hidden overflow-y-auto rounded-lg bg-popover p-1 text-popover-foreground shadow-md ring-1 ring-foreground/10 outline-none', $align === 'end' ? 'origin-top-right' : 'origin-top-left', $contentClass) }}"
        >
            {{ $slot }}
        </div>
    </template>
</div>
