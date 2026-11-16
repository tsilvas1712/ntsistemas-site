{{-- <x-ui.checkbox id="remember" wire:model="remember" /> (funciona com <x-ui.label for="remember">) --}}
<span data-slot="checkbox" class="relative inline-flex size-4 shrink-0">
    <input type="checkbox" {{ $attributes->cn('peer relative size-4 shrink-0 cursor-pointer appearance-none rounded-[4px] border border-input transition-colors outline-none after:absolute after:-inset-x-3 after:-inset-y-2 focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 dark:bg-input/30 checked:border-primary checked:bg-primary dark:checked:bg-primary') }} />
    <x-lucide-check class="pointer-events-none absolute inset-0 m-auto hidden size-3.5 text-primary-foreground peer-checked:block" />
</span>
