{{-- Campos compartilhados entre criar e editar (equivalente ao UserForm do React). --}}

<form wire:submit="save" class="grid gap-4">
    <x-ui.card>
        <x-ui.card.content class="grid gap-3">
            <x-field label="Nome" for="name" error="form.name">
                <x-ui.input id="name" required placeholder="Nome completo" wire:model="form.name" :aria-invalid="$errors->has('form.name') ? 'true' : null" />
            </x-field>
            <x-field label="E-mail" for="email" error="form.email">
                <x-ui.input id="email" type="email" required placeholder="email@empresa.com" wire:model="form.email" :aria-invalid="$errors->has('form.email') ? 'true' : null" />
            </x-field>
            <x-field label="Perfil" for="role" error="form.role">
                <x-ui.select id="role" wire:model="form.role" :options="App\Enums\UserRole::options()" class="w-full" />
            </x-field>
            <x-field label="Status" for="status" error="form.status">
                <x-ui.select id="status" wire:model="form.status" :options="App\Enums\UserStatus::options()" class="w-full" />
            </x-field>
            <x-field label="Observações" for="notes" error="form.notes">
                <x-ui.textarea id="notes" placeholder="Notas internas opcionais" wire:model="form.notes" />
            </x-field>
        </x-ui.card.content>
    </x-ui.card>

    <div class="grid grid-cols-2 gap-2">
        <x-ui.button variant="outline" href="{{ $cancelUrl }}" wire:navigate>Cancelar</x-ui.button>
        <x-ui.button type="submit">
            <span wire:loading.remove wire:target="save">{{ $submitLabel }}</span>
            <span wire:loading wire:target="save">Salvando…</span>
        </x-ui.button>
    </div>
</form>
