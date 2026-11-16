{{-- Layout do site público da NT Sistemas Web (mesmo padrão do site do NT Omni, em modo escuro). --}}
<!DOCTYPE html>
<html lang="pt-BR" class="dark scroll-smooth">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        @if (config('site.analytics.enabled'))
            <meta name="track-clicks" content="{{ route('track.click') }}" />
        @endif
        <title>@yield('title', 'NT Sistemas Web · Sistemas legados, manutenção e novos sistemas')</title>
        <meta name="description" content="@yield('description', 'Seu sistema trava, ninguém mais sabe mexer nele ou precisa ir para a web? Diagnóstico técnico, modernização de sistemas legados e manutenção mensal. Mais de 10 anos de engenharia.')" />
        <meta name="theme-color" content="#020617" />
        <link rel="canonical" href="{{ url()->current() }}" />
        <meta property="og:type" content="website" />
        <meta property="og:locale" content="pt_BR" />
        <meta property="og:site_name" content="NT Sistemas Web" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:title" content="@yield('title', 'NT Sistemas Web · Sistemas legados, manutenção e novos sistemas')" />
        <meta property="og:description" content="Seu sistema trava ou precisa ir para a web? Diagnóstico técnico com escopo e preço fechados, modernização e manutenção mensal." />
        <meta name="twitter:card" content="summary" />
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => 'NT Sistemas Web',
            'url' => url('/'),
            'email' => $site['contact_email'],
            'telephone' => $site['whatsapp'] ?: null,
            'areaServed' => 'BR',
            'description' => 'Modernização e manutenção de sistemas legados, migração para a web, correção de bugs críticos e desenvolvimento de novos sistemas.',
            'sameAs' => array_values(array_filter([$site['github_url'], $site['linkedin_url']])),
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 text-slate-100 antialiased">
        <header class="sticky top-0 z-40 border-b border-slate-800/70 bg-slate-950/80 backdrop-blur" x-data="{ open: false }">
            <div class="mx-auto flex max-w-6xl items-center gap-6 px-4 py-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <x-brand.mark class="size-8" />
                    <span class="font-heading text-lg font-bold whitespace-nowrap text-white">NT Sistemas <span class="text-indigo-400">Web</span></span>
                </a>
                <nav class="hidden gap-5 text-sm text-slate-400 md:flex">
                    <a href="#servicos" class="hover:text-white">Serviços</a>
                    <a href="#infraestrutura" class="hover:text-white">Infraestrutura</a>
                    <a href="#por-que" class="hover:text-white">Por que nós</a>
                    <a href="#produtos" class="hover:text-white">Produtos</a>
                    <a href="#perguntas" class="hover:text-white">Perguntas</a>
                </nav>
                <div class="ml-auto flex items-center gap-2">
                    <a href="#contato" class="hidden rounded-lg bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm shadow-indigo-600/30 hover:bg-indigo-500 sm:block">Fale com a gente</a>
                    <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-800 md:hidden" aria-label="Abrir menu" @click="open = !open">
                        <x-lucide-menu class="size-5" x-show="!open" />
                        <x-lucide-x class="size-5" x-show="open" x-cloak />
                    </button>
                </div>
            </div>
            <nav class="grid gap-1 border-t border-slate-800 px-4 py-3 text-sm text-slate-300 md:hidden" x-show="open" x-cloak @click="open = false">
                <a href="#servicos" class="rounded-lg px-3 py-2 hover:bg-slate-800">Serviços</a>
                <a href="#infraestrutura" class="rounded-lg px-3 py-2 hover:bg-slate-800">Infraestrutura</a>
                <a href="#por-que" class="rounded-lg px-3 py-2 hover:bg-slate-800">Por que nós</a>
                <a href="#produtos" class="rounded-lg px-3 py-2 hover:bg-slate-800">Produtos</a>
                <a href="#perguntas" class="rounded-lg px-3 py-2 hover:bg-slate-800">Perguntas</a>
                <a href="#contato" class="mt-1 rounded-lg bg-indigo-600 px-3 py-2 text-center font-semibold text-white">Fale com a gente</a>
            </nav>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="border-t border-slate-800 bg-slate-900/40">
            <div class="mx-auto grid max-w-6xl gap-6 px-4 py-10 text-sm text-slate-400 md:grid-cols-[1fr_auto]">
                <div class="grid gap-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-white">
                        <x-brand.mark class="size-7" /> <span class="font-heading font-bold">NT Sistemas Web</span>
                    </a>
                    <p>Engenharia de software, do legado à nuvem.</p>
                    <p>Contato: <a href="mailto:{{ $site['contact_email'] }}" class="hover:text-white hover:underline">{{ $site['contact_email'] }}</a></p>
                </div>
                <div class="flex flex-wrap items-end gap-4">
                    @if ($site['github_url'])
                        <a href="{{ $site['github_url'] }}" target="_blank" rel="noopener" class="hover:text-white">GitHub</a>
                    @endif
                    @if ($site['linkedin_url'])
                        <a href="{{ $site['linkedin_url'] }}" target="_blank" rel="noopener" class="hover:text-white">LinkedIn</a>
                    @endif
                    <a href="https://nt-omni.com.br" target="_blank" rel="noopener" class="hover:text-white">NT Omni</a>
                    <span>© {{ now()->year }} NT Sistemas Web</span>
                </div>
            </div>
        </footer>

        @if ($site['whatsapp'])
            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $site['whatsapp']) }}" target="_blank" rel="noopener" aria-label="Falar no WhatsApp"
               class="fixed right-4 bottom-4 z-40 flex size-12 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-500/30 hover:bg-emerald-400">
                <x-lucide-message-circle class="size-6" />
            </a>
        @endif
    </body>
</html>
