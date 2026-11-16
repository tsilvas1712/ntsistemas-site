@props(['src', 'alt' => ''])

<img data-slot="avatar-image" src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->cn('aspect-square size-full rounded-full object-cover') }} />
