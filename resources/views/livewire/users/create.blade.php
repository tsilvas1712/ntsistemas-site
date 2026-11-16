<div class="mx-auto grid max-w-xl gap-4">
    <div>
        <h1 class="font-heading text-xl font-semibold">Novo usuário</h1>
        <p class="text-sm text-muted-foreground">Preencha os dados para criar o registro</p>
    </div>

    @include('livewire.users._form', ['submitLabel' => 'Salvar', 'cancelUrl' => route('users.index')])
</div>
