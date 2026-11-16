<div class="mx-auto grid max-w-xl gap-4">
    <div>
        <h1 class="font-heading text-xl font-semibold">Editar usuário</h1>
        <p class="text-sm text-muted-foreground">{{ $user->name }} · ID {{ $user->id }}</p>
    </div>

    @include('livewire.users._form', ['submitLabel' => 'Atualizar', 'cancelUrl' => route('users.show', $user)])
</div>
