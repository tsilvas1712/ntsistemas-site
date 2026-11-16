{{--
    Item do menu. Vira <a> com href; senão é <button> (use type="submit" dentro de um <form>).
    Fecha o menu ao ser clicado.
--}}
@props(['variant' => 'default', 'href' => null, 'inset' => false, 'disabled' => false])

@php
    $attributes = $attributes
        ->cn("group/dropdown-menu-item relative flex w-full cursor-default items-center gap-1.5 rounded-md px-1.5 py-1 text-left text-sm outline-hidden select-none focus:bg-accent focus:text-accent-foreground not-data-[variant=destructive]:focus:**:text-accent-foreground data-inset:pl-7 data-[variant=destructive]:text-destructive data-[variant=destructive]:focus:bg-destructive/10 data-[variant=destructive]:focus:text-destructive dark:data-[variant=destructive]:focus:bg-destructive/20 data-disabled:pointer-events-none data-disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 data-[variant=destructive]:*:[svg]:text-destructive")
        ->merge([
            'role' => 'menuitem',
            'tabindex' => '-1',
            'data-slot' => 'dropdown-menu-item',
            'data-variant' => $variant,
            'data-inset' => $inset ? 'true' : null,
            'data-disabled' => $disabled ? 'true' : null,
            'aria-disabled' => $disabled ? 'true' : null,
        ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes }} @mousemove="$el.focus()" @click="close()">{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button']) }} @mousemove="$el.focus()" @click="close()">{{ $slot }}</button>
@endif
