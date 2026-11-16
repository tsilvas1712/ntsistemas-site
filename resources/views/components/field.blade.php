{{--
    Rótulo + campo + mensagem de erro de validação.
    <x-field label="Nome" for="name" error="form.name"> <x-ui.input id="name" wire:model="form.name" /> </x-field>
--}}
@props(['label', 'for' => null, 'error' => null])

<div {{ $attributes->cn('grid gap-1.5') }}>
    <x-ui.label :for="$for">{{ $label }}</x-ui.label>
    {{ $slot }}
    @if ($error)
        @error($error)
            <p class="text-xs text-destructive">{{ $message }}</p>
        @enderror
    @endif
</div>
