<div class="mx-auto grid max-w-2xl gap-4">
    <div>
        <h1 class="font-heading text-xl font-semibold">Ajustes do site</h1>
        <p class="text-sm text-muted-foreground">O que mudar aqui aparece no site na hora, sem novo deploy.</p>
    </div>

    @if (session('saved'))
        <div class="flex items-center gap-2 rounded-lg bg-emerald-500/10 px-3 py-2 text-sm text-emerald-700 dark:text-emerald-300 ring-1 ring-emerald-500/20" role="status">
            <x-lucide-circle-check class="size-4" /> {{ session('saved') }}
        </div>
    @endif

    <form wire:submit="save" class="grid gap-4">
        <x-ui.card>
            <x-ui.card.header>
                <x-ui.card.title>Contato</x-ui.card.title>
            </x-ui.card.header>
            <x-ui.card.content class="grid gap-3">
                <x-field label="E-mail exibido no site" for="contact_email" error="contact_email">
                    <x-ui.input id="contact_email" type="email" wire:model="contact_email" />
                </x-field>
                <x-field label="E-mail que recebe os avisos de novo contato" for="notification_email" error="notification_email">
                    <x-ui.input id="notification_email" type="email" wire:model="notification_email" placeholder="Vazio: não envia aviso" />
                </x-field>
                <x-field label="WhatsApp (com DDI e DDD)" for="whatsapp" error="whatsapp">
                    <x-ui.input id="whatsapp" wire:model="whatsapp" placeholder="55 51 99999-0000 · vazio: esconde o botão" />
                </x-field>
                <x-field label="Prazo de resposta (horas)" for="response_hours" error="response_hours">
                    <x-ui.input id="response_hours" type="number" min="1" wire:model="response_hours" class="w-32" />
                </x-field>
            </x-ui.card.content>
        </x-ui.card>

        <x-ui.card>
            <x-ui.card.header>
                <x-ui.card.title>Links e disponibilidade</x-ui.card.title>
            </x-ui.card.header>
            <x-ui.card.content class="grid gap-3">
                <x-field label="GitHub" for="github_url" error="github_url">
                    <x-ui.input id="github_url" wire:model="github_url" placeholder="https://github.com/…" />
                </x-field>
                <x-field label="LinkedIn" for="linkedin_url" error="linkedin_url">
                    <x-ui.input id="linkedin_url" wire:model="linkedin_url" placeholder="https://linkedin.com/company/…" />
                </x-field>
                <div class="flex items-center gap-2 pt-1">
                    <x-ui.checkbox id="available" wire:model="available" />
                    <x-ui.label for="available" class="font-normal">Mostrar o selo "Disponível para novos projetos"</x-ui.label>
                </div>
            </x-ui.card.content>
        </x-ui.card>

        <div class="flex justify-end gap-2">
            <x-ui.button variant="outline" href="{{ route('home') }}" target="_blank">Ver site</x-ui.button>
            <x-ui.button type="submit">
                <span wire:loading.remove wire:target="save">Salvar ajustes</span>
                <span wire:loading wire:target="save">Salvando…</span>
            </x-ui.button>
        </div>
    </form>
</div>
