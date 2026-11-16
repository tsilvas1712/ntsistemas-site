{{--
    Select nativo com o visual do shadcn. Passe :options (valor => rótulo) ou <option> no slot.
    <x-ui.select id="role" wire:model="form.role" :options="App\Enums\UserRole::options()" class="w-full" />

    "class" vai para o contêiner; os demais atributos (id, wire:model…) vão para o <select>.
--}}
@props([
    'options' => [],
    'placeholder' => null,
    'size' => 'default',
])

<div data-slot="select" {{ $attributes->only('class')->cn('relative w-fit') }}>
    <select
        data-slot="select-trigger"
        data-size="{{ $size }}"
        {{ $attributes->except('class')->merge(['class' => 'flex w-full appearance-none items-center rounded-lg border border-input bg-transparent py-1 pr-8 pl-2.5 text-sm whitespace-nowrap transition-colors outline-none select-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 data-[size=default]:h-8 data-[size=sm]:h-7 data-[size=sm]:rounded-[min(var(--radius-md),10px)] dark:bg-input/30 dark:hover:bg-input/50 dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40']) }}
    >
        @if ($placeholder)
            <option value="" disabled>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
        {{ $slot }}
    </select>
    <x-lucide-chevron-down class="pointer-events-none absolute top-1/2 right-2 size-4 -translate-y-1/2 text-muted-foreground" />
</div>
