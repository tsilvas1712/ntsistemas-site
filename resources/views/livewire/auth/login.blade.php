<div class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-background px-5 py-10">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(40rem_24rem_at_50%_-10%,rgba(79,70,229,0.25),transparent)]"></div>

    <div class="mb-6 flex flex-col items-center gap-3 text-center">
        <x-brand.mark class="size-14" />
        <div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">NT Sistemas <span class="text-indigo-600 dark:text-indigo-400">Web</span></h1>
            <p class="mt-1 text-sm text-muted-foreground">Acesse o painel administrativo</p>
        </div>
    </div>

    <x-ui.card class="w-full max-w-sm shadow-sm">
        <x-ui.card.content>
            <form wire:submit="login" class="grid gap-4">
                <x-field label="E-mail" for="email" error="email">
                    <x-ui.input id="email" type="email" autocomplete="email" required autofocus wire:model="email" :aria-invalid="$errors->has('email') ? 'true' : null" />
                </x-field>
                <x-field label="Senha" for="password" error="password">
                    <x-ui.input id="password" type="password" autocomplete="current-password" required placeholder="••••••••" wire:model="password" :aria-invalid="$errors->has('password') ? 'true' : null" />
                </x-field>

                <div class="flex items-center gap-2">
                    <x-ui.checkbox id="remember" wire:model="remember" />
                    <x-ui.label for="remember" class="font-normal">Lembrar-me</x-ui.label>
                </div>

                <x-ui.button type="submit" class="w-full" size="lg">Entrar no painel</x-ui.button>
            </form>
        </x-ui.card.content>
    </x-ui.card>

    <a href="{{ route('home') }}" class="mt-6 flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
        <x-lucide-arrow-left class="size-4" /> Voltar para o site
    </a>
</div>
