<div class="mx-auto grid max-w-3xl gap-4" x-data="{ confirmDelete: false }">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs text-muted-foreground">
                <a href="{{ route('contacts.index') }}" wire:navigate class="hover:text-foreground">Contatos</a>
                / #{{ $contact->id }}
            </p>
            <h1 class="font-heading text-xl font-semibold">{{ $contact->name }}</h1>
            <p class="text-sm text-muted-foreground">Recebido em {{ $contact->created_at->format('d/m/Y \à\s H:i') }}@if ($contact->utm_source) · veio de <strong>{{ $contact->utm_source }}</strong>{{ $contact->utm_medium ? ' / '.$contact->utm_medium : '' }}{{ $contact->utm_campaign ? ' ('.$contact->utm_campaign.')' : '' }}@endif</p>
        </div>
        <x-contacts.status-badge :status="$contact->status" />
    </div>

    @if (session('saved'))
        <div class="flex items-center gap-2 rounded-lg bg-emerald-500/10 px-3 py-2 text-sm text-emerald-700 dark:text-emerald-300 ring-1 ring-emerald-500/20" role="status">
            <x-lucide-circle-check class="size-4" /> {{ session('saved') }}
        </div>
    @endif

    <div class="grid gap-2 sm:grid-cols-3">
        <x-ui.button variant="outline" href="mailto:{{ $contact->email }}?subject={{ rawurlencode('Re: seu contato com a NT Sistemas Web') }}">
            <x-lucide-mail data-icon="inline-start" /> Responder por e-mail
        </x-ui.button>
        @if ($contact->whatsappNumber())
            <x-ui.button variant="outline" href="https://wa.me/{{ $contact->whatsappNumber() }}" target="_blank" rel="noopener">
                <x-lucide-message-circle data-icon="inline-start" /> WhatsApp
            </x-ui.button>
        @endif
        @if ($contact->status !== App\Enums\ContactStatus::Respondido)
            <x-ui.button variant="secondary" wire:click="setStatus('respondido')">
                <x-lucide-check-check data-icon="inline-start" /> Marcar como respondido
            </x-ui.button>
        @endif
    </div>

    <x-ui.card>
        <x-ui.card.content class="grid gap-4">
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                @foreach ([
                    'E-mail' => $contact->email,
                    'Telefone' => $contact->phone,
                    'Empresa' => $contact->company,
                    'Orçamento' => $contact->budget,
                    'Stack atual' => $contact->stack,
                    'Respondido em' => $contact->replied_at?->format('d/m/Y H:i'),
                ] as $label => $value)
                    <div class="grid gap-0.5">
                        <dt class="text-xs text-muted-foreground">{{ $label }}</dt>
                        <dd class="font-medium break-words">{{ $value ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>

            @if ($contact->serviceLabels())
                <div class="grid gap-1.5 border-t pt-3">
                    <p class="text-xs text-muted-foreground">Interesse</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($contact->serviceLabels() as $service)
                            <x-ui.badge variant="secondary">{{ $service }}</x-ui.badge>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid gap-1.5 border-t pt-3">
                <p class="text-xs text-muted-foreground">Mensagem</p>
                <p class="text-sm leading-relaxed whitespace-pre-line">{{ $contact->message }}</p>
            </div>
        </x-ui.card.content>
    </x-ui.card>

    <form wire:submit="save">
        <x-ui.card>
            <x-ui.card.header>
                <x-ui.card.title>Acompanhamento</x-ui.card.title>
            </x-ui.card.header>
            <x-ui.card.content class="grid gap-3">
                <x-field label="Status" for="status" error="status">
                    <x-ui.select id="status" wire:model="status" :options="App\Enums\ContactStatus::options()" class="w-full" />
                </x-field>
                <x-field label="Observações internas" for="notes" error="notes">
                    <x-ui.textarea id="notes" rows="4" placeholder="Ex.: liguei dia 14, mandar proposta até sexta" wire:model="notes" />
                </x-field>
                <div class="flex justify-end">
                    <x-ui.button type="submit">
                        <span wire:loading.remove wire:target="save">Salvar</span>
                        <span wire:loading wire:target="save">Salvando…</span>
                    </x-ui.button>
                </div>
            </x-ui.card.content>
        </x-ui.card>
    </form>

    <div class="flex items-center justify-between gap-3 text-xs text-muted-foreground">
        <span>IP {{ $contact->ip ?? '—' }} · {{ App\Support\UserAgent::summary($contact->user_agent) }}</span>
        <x-ui.button variant="destructive" size="sm" @click="confirmDelete = true">Excluir contato</x-ui.button>
    </div>

    <x-ui.dialog x-model="confirmDelete">
        <x-ui.dialog.header>
            <x-ui.dialog.title>Excluir contato?</x-ui.dialog.title>
            <x-ui.dialog.description>O contato de {{ $contact->name }} será removido de vez.</x-ui.dialog.description>
        </x-ui.dialog.header>
        <x-ui.dialog.footer>
            <x-ui.button variant="outline" @click="confirmDelete = false">Cancelar</x-ui.button>
            <x-ui.button variant="destructive" wire:click="delete">Excluir</x-ui.button>
        </x-ui.dialog.footer>
    </x-ui.dialog>
</div>
