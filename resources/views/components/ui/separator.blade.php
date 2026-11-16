@props(['orientation' => 'horizontal', 'decorative' => true])

<div
    data-slot="separator"
    data-orientation="{{ $orientation }}"
    role="{{ $decorative ? 'none' : 'separator' }}"
    @unless ($decorative) aria-orientation="{{ $orientation }}" @endunless
    {{ $attributes->cn('shrink-0 bg-border data-horizontal:h-px data-horizontal:w-full data-vertical:w-px data-vertical:self-stretch') }}
></div>
