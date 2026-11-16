{{-- <x-ui.avatar size="sm"><x-ui.avatar.fallback>AM</x-ui.avatar.fallback></x-ui.avatar> --}}
@props(['size' => 'default'])

<span data-slot="avatar" data-size="{{ $size }}" {{ $attributes->cn('group/avatar relative flex size-8 shrink-0 rounded-full select-none after:absolute after:inset-0 after:rounded-full after:border after:border-border after:mix-blend-darken data-[size=lg]:size-10 data-[size=sm]:size-6 dark:after:mix-blend-lighten') }}>{{ $slot }}</span>
