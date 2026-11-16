@php
    $input = 'w-full rounded-lg bg-slate-950/60 px-3.5 py-2.5 text-sm text-slate-100 ring-1 ring-slate-800 outline-none placeholder:text-slate-500 focus:ring-2 focus:ring-indigo-500 aria-invalid:ring-rose-500/70';
    $label = 'text-sm font-medium text-slate-300';
@endphp

<div class="rounded-2xl bg-slate-900/70 p-6 ring-1 ring-slate-800 md:p-8">
    @if ($sent)
        <div class="grid justify-items-center gap-4 py-10 text-center" role="status">
            <span class="flex size-14 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">
                <x-lucide-check class="size-7" />
            </span>
            <h3 class="font-heading text-2xl font-bold text-white">Mensagem recebida.</h3>
            <p class="max-w-sm text-slate-400">Respondemos com uma avaliação técnica em até {{ $responseHours }} horas.</p>
            <button type="button" wire:click="$set('sent', false)" class="text-sm font-semibold text-indigo-400 hover:text-indigo-300">Enviar outra mensagem</button>
        </div>
    @else
        <ol class="mb-6 flex gap-2 font-mono text-xs">
            <li @class(['rounded-full px-3 py-1', 'bg-indigo-600 text-white' => $step === 1, 'bg-slate-800 text-slate-400' => $step !== 1])>01 · Problema</li>
            <li @class(['rounded-full px-3 py-1', 'bg-indigo-600 text-white' => $step === 2, 'bg-slate-800 text-slate-400' => $step !== 2])>02 · Detalhes</li>
        </ol>

        @if ($step === 1)
            <p class="{{ $label }}">O que você precisa? Marque tudo o que se aplica.</p>
            <div class="mt-3 grid gap-2.5 sm:grid-cols-2">
                @foreach ($options as $key => $text)
                    @php($checked = in_array($key, $services, true))
                    <button
                        type="button"
                        wire:click="toggleService('{{ $key }}')"
                        wire:key="service-{{ $key }}"
                        aria-pressed="{{ $checked ? 'true' : 'false' }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-medium ring-1 transition',
                            'bg-indigo-500/15 text-white ring-indigo-500' => $checked,
                            'bg-slate-950/40 text-slate-300 ring-slate-800 hover:ring-slate-700' => ! $checked,
                        ])
                    >
                        <span @class(['flex size-5 shrink-0 items-center justify-center rounded-md ring-1', 'bg-indigo-600 ring-indigo-500' => $checked, 'ring-slate-600' => ! $checked])>
                            @if ($checked) <x-lucide-check class="size-3.5 text-white" /> @endif
                        </span>
                        {{ $text }}
                    </button>
                @endforeach
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" wire:click="next" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/25 hover:bg-indigo-500">
                    Continuar <x-lucide-arrow-right class="size-4" />
                </button>
            </div>
        @else
            <form wire:submit="submit" class="grid gap-4">
                {{-- Armadilha para robôs --}}
                <div class="hidden" aria-hidden="true">
                    <label>Site <input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-1.5">
                        <label for="c-name" class="{{ $label }}">Nome *</label>
                        <input id="c-name" type="text" wire:model="name" autocomplete="name" placeholder="Maria Silva" class="{{ $input }}" @error('name') aria-invalid="true" @enderror>
                        @error('name') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-1.5">
                        <label for="c-email" class="{{ $label }}">E-mail *</label>
                        <input id="c-email" type="email" wire:model="email" autocomplete="email" placeholder="maria@empresa.com" class="{{ $input }}" @error('email') aria-invalid="true" @enderror>
                        @error('email') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-1.5">
                        <label for="c-phone" class="{{ $label }}">WhatsApp / telefone</label>
                        <input id="c-phone" type="tel" wire:model="phone" autocomplete="tel" placeholder="(51) 99999-0000" class="{{ $input }}">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="c-company" class="{{ $label }}">Empresa / projeto</label>
                        <input id="c-company" type="text" wire:model="company" autocomplete="organization" placeholder="Empresa Ltda." class="{{ $input }}">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="c-budget" class="{{ $label }}">Orçamento aproximado</label>
                        <input id="c-budget" type="text" wire:model="budget" placeholder="R$ 20k–50k" class="{{ $input }}">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="c-stack" class="{{ $label }}">Stack atual</label>
                        <input id="c-stack" type="text" wire:model="stack" placeholder="Laravel, MySQL, AWS…" class="{{ $input }}">
                    </div>
                </div>

                <div class="grid gap-1.5">
                    <label for="c-message" class="{{ $label }}">Descreva o problema ou desafio *</label>
                    <textarea id="c-message" rows="5" wire:model="message" placeholder="O que está quebrado, lento ou travando você? Pode ser tão técnico quanto quiser." class="{{ $input }}" @error('message') aria-invalid="true" @enderror></textarea>
                    @error('message') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <button type="button" wire:click="back" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-300 ring-1 ring-slate-800 hover:bg-slate-800/60">
                        <x-lucide-arrow-left class="size-4" /> Voltar
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/25 hover:bg-indigo-500 disabled:opacity-60">
                        <span wire:loading.remove wire:target="submit">Enviar mensagem</span>
                        <span wire:loading wire:target="submit">Enviando…</span>
                        <x-lucide-send class="size-4" />
                    </button>
                </div>
            </form>
        @endif
    @endif
</div>
