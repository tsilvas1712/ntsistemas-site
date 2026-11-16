<div class="mx-auto grid max-w-xl gap-4" x-data="{ confirmDelete: false }">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs text-muted-foreground">
                <a href="{{ route('users.index') }}" wire:navigate class="hover:text-foreground">Usuários</a>
                / {{ $user->id }}
            </p>
            <h1 class="font-heading text-xl font-semibold">{{ $user->name }}</h1>
        </div>
        <x-users.status-badge :status="$user->status" />
    </div>

    @if (session('password'))
        <div class="grid gap-1 rounded-lg bg-amber-500/10 px-3 py-2.5 text-sm text-amber-800 dark:text-amber-200 ring-1 ring-amber-500/20" role="status">
            <p>Senha de acesso (copie agora, ela não será mostrada de novo):</p>
            <code class="font-mono text-base font-semibold text-foreground select-all">{{ session('password') }}</code>
        </div>
    @endif

    <x-ui.card>
        <x-ui.card.content class="grid gap-4">
            <div class="flex items-center gap-3">
                <x-ui.avatar size="lg">
                    <x-ui.avatar.fallback class="bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">{{ $user->initials() }}</x-ui.avatar.fallback>
                </x-ui.avatar>
                <div>
                    <p class="font-medium">{{ $user->email }}</p>
                    <p class="text-sm text-muted-foreground">{{ $user->role->label() }}</p>
                </div>
            </div>

            <dl class="grid gap-3 text-sm">
                <div class="flex justify-between gap-4 border-t pt-3">
                    <dt class="text-muted-foreground">ID</dt>
                    <dd class="font-medium">{{ $user->id }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-muted-foreground">Perfil</dt>
                    <dd class="font-medium">{{ $user->role->label() }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-muted-foreground">Criado em</dt>
                    <dd class="font-medium">{{ $user->created_at?->format('d/m/Y') }}</dd>
                </div>
                @if ($user->notes)
                    <div class="grid gap-1 border-t pt-3">
                        <dt class="text-muted-foreground">Observações</dt>
                        <dd>{{ $user->notes }}</dd>
                    </div>
                @endif
            </dl>
        </x-ui.card.content>
    </x-ui.card>

    <div class="grid grid-cols-3 gap-2">
        <x-ui.button variant="outline" href="{{ route('users.edit', $user) }}" wire:navigate>Editar</x-ui.button>
        <x-ui.button variant="outline" wire:click="resetPassword" wire:confirm="Gerar uma nova senha para {{ $user->name }}?">Nova senha</x-ui.button>
        @if ($user->is(auth()->user()))
            <x-ui.button variant="destructive" disabled title="Você não pode excluir a própria conta">Excluir</x-ui.button>
        @else
            <x-ui.button variant="destructive" @click="confirmDelete = true">Excluir</x-ui.button>
        @endif
    </div>

    <x-ui.dialog x-model="confirmDelete">
        <x-ui.dialog.header>
            <x-ui.dialog.title>Excluir usuário?</x-ui.dialog.title>
            <x-ui.dialog.description>Essa ação remove {{ $user->name }} do painel.</x-ui.dialog.description>
        </x-ui.dialog.header>
        <x-ui.dialog.footer>
            <x-ui.button variant="outline" @click="confirmDelete = false">Cancelar</x-ui.button>
            <x-ui.button variant="destructive" wire:click="delete">Excluir</x-ui.button>
        </x-ui.dialog.footer>
    </x-ui.dialog>
</div>
