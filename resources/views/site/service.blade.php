@extends('layouts.site')

@section('title', $service['title'])
@section('description', $service['description'])

@push('head')
    <script type="application/ld+json">{!! json_encode([
        '@'.'context' => 'https://schema.org',
        '@'.'type' => 'FAQPage',
        'mainEntity' => array_map(fn (array $item) => [
            '@'.'type' => 'Question',
            'name' => $item[0],
            'acceptedAnswer' => ['@'.'type' => 'Answer', 'text' => $item[1]],
        ], $service['faq']),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    {{-- Topo --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(60rem_30rem_at_70%_-10%,rgba(79,70,229,0.28),transparent)]"></div>
        <div class="mx-auto grid max-w-4xl gap-6 px-4 py-16 md:py-24">
            <a href="{{ route('home') }}#servicos" class="flex w-fit items-center gap-1.5 text-sm text-slate-400 hover:text-white"><x-lucide-arrow-left class="size-4" /> Serviços</a>
            <span class="flex size-12 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300"><x-dynamic-component :component="'lucide-'.$service['icon']" class="size-6" /></span>
            <h1 class="font-heading text-4xl leading-tight font-bold tracking-tight text-balance text-white md:text-5xl">{{ $service['headline'] }}</h1>
            <p class="max-w-3xl text-lg text-slate-400">{{ $service['lead'] }}</p>
            <div class="flex flex-wrap gap-3">
                <a href="#contato" data-track="Serviço: {{ $slug }}" class="rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-500">Pedir diagnóstico</a>
                @if ($site['whatsapp'])
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $site['whatsapp']) }}?text={{ rawurlencode('Oi! Vim pelo site da NT Sistemas Web e quero falar sobre um projeto.') }}" target="_blank" rel="noopener" class="rounded-lg px-5 py-3 font-semibold text-slate-200 ring-1 ring-slate-700 hover:bg-slate-900">Falar no WhatsApp</a>
                @endif
            </div>
        </div>
    </section>

    {{-- Sinais --}}
    <section class="border-t border-slate-800/70 bg-slate-900/40">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 lg:grid-cols-2">
            <div class="grid content-start gap-4">
                <h2 class="font-heading text-3xl font-bold tracking-tight text-white">Reconhece algum destes?</h2>
                <ul class="grid gap-3 text-slate-300">
                    @foreach ($service['signs'] as $sign)
                        <li class="flex gap-3"><x-lucide-circle-alert class="mt-0.5 size-5 shrink-0 text-amber-400" /> {{ $sign }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="grid content-start gap-4 rounded-2xl bg-slate-900 p-6 ring-1 ring-slate-800">
                <h2 class="font-heading text-xl font-bold text-white">O que está incluído</h2>
                <ul class="grid gap-3 text-sm text-slate-300">
                    @foreach ($service['included'] as $item)
                        <li class="flex gap-2"><x-lucide-check class="mt-0.5 size-4 shrink-0 text-emerald-400" /> {{ $item }}</li>
                    @endforeach
                </ul>
                <p class="border-t border-slate-800 pt-4 text-sm text-slate-400">Escopo, prazo e preço fechados antes de começar.</p>
            </div>
        </div>
    </section>

    {{-- Como funciona --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <h2 class="text-center font-heading text-3xl font-bold tracking-tight text-white">Como funciona</h2>
        <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($service['steps'] as $i => [$title, $text])
                <div class="grid content-start gap-3">
                    <span class="flex size-11 items-center justify-center rounded-full bg-indigo-600 font-heading font-bold text-white">{{ $i + 1 }}</span>
                    <h3 class="font-heading text-lg font-semibold text-white">{{ $title }}</h3>
                    <p class="text-slate-400">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Perguntas --}}
    <section class="border-t border-slate-800/70 bg-slate-900/40">
        <div class="mx-auto max-w-3xl px-4 py-16">
            <h2 class="text-center font-heading text-3xl font-bold tracking-tight text-white">Perguntas frequentes</h2>
            <div class="mt-10 grid gap-3">
                @foreach ($service['faq'] as [$question, $answer])
                    <details class="group rounded-xl bg-slate-900 p-5 ring-1 ring-slate-800 open:ring-indigo-500/50">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-medium text-white">
                            {{ $question }}
                            <x-lucide-chevron-down class="size-4 shrink-0 text-slate-500 transition group-open:rotate-180" />
                        </summary>
                        <p class="mt-3 text-sm text-slate-400">{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Contato --}}
    <section id="contato" class="mx-auto max-w-6xl scroll-mt-16 px-4 py-16 md:py-20">
        <div class="grid gap-10 lg:grid-cols-[1fr_1.4fr]">
            <div class="grid content-start gap-4">
                <h2 class="font-heading text-3xl font-bold tracking-tight text-white">Conte o que está acontecendo.</h2>
                <p class="text-slate-400">Você recebe uma avaliação inicial em até {{ $site['response_hours'] }} horas, direto de quem programa.</p>
            </div>
            <livewire:site.contact-form />
        </div>
    </section>
@endsection
