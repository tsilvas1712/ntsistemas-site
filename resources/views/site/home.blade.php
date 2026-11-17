@extends('layouts.site')

@section('content')
    {{-- Hero --}}
    <section id="inicio" class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(60rem_30rem_at_70%_-10%,rgba(79,70,229,0.28),transparent)]"></div>
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(rgba(99,102,241,0.06)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.06)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_at_top,black,transparent_70%)]"></div>
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 md:py-24 lg:grid-cols-[1.1fr_1fr]">
            <div class="grid gap-6">
                @if ($site['available'])
                    <span class="flex w-fit items-center gap-2 rounded-full bg-indigo-500/10 px-3 py-1 text-xs font-semibold text-indigo-300 ring-1 ring-indigo-500/30">
                        <span class="size-1.5 animate-pulse rounded-full bg-emerald-400"></span>
                        Disponível para novos projetos
                    </span>
                @endif
                <h1 class="font-heading text-4xl leading-tight font-bold tracking-tight text-balance text-white md:text-5xl">
                    Sistema travando ou sem ninguém para mexer nele? <span class="text-indigo-400">Assumimos, corrigimos e modernizamos.</span>
                </h1>
                <p class="max-w-xl text-lg text-slate-400">
                    Também levamos sistemas antigos para a web. Mais de 10 anos de engenharia de software, e tudo começa por um diagnóstico com escopo, prazo e preço fechados:
                    você entende o que tem antes de decidir o que fazer.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="#contato" class="rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-500">Pedir diagnóstico</a>
                    <a href="#servicos" class="rounded-lg px-5 py-3 font-semibold text-slate-200 ring-1 ring-slate-700 hover:bg-slate-900">Ver serviços</a>
                </div>
                <p class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-400">
                    <span class="flex items-center gap-1.5"><x-lucide-check class="size-4 text-emerald-400" /> Resposta em até {{ $site['response_hours'] }}h</span>
                    <span class="flex items-center gap-1.5"><x-lucide-check class="size-4 text-emerald-400" /> Você fala direto com quem programa</span>
                    <span class="flex items-center gap-1.5"><x-lucide-check class="size-4 text-emerald-400" /> Sem vendor lock-in</span>
                </p>
            </div>

            {{-- Porta de entrada: diagnóstico técnico --}}
            <div class="mx-auto grid w-full max-w-md gap-5 rounded-2xl bg-slate-900 p-6 shadow-2xl shadow-indigo-950/60 ring-1 ring-slate-800">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-indigo-500/15 text-indigo-300 ring-1 ring-indigo-500/30"><x-lucide-search-code class="size-5" /></span>
                    <div>
                        <p class="text-xs font-semibold tracking-wide text-indigo-300 uppercase">Por onde começar</p>
                        <p class="font-heading text-lg font-bold text-white">Diagnóstico técnico</p>
                    </div>
                </div>
                <ul class="grid gap-3 text-sm text-slate-300">
                    @foreach ([
                        'Leitura do código, do banco e da infraestrutura',
                        'Problemas e riscos em ordem de prioridade',
                        'O que vale manter, corrigir ou reescrever',
                        'Plano com etapas, prazo e estimativa',
                        'Relatório em linguagem de negócio, não só técnica',
                    ] as $point)
                        <li class="flex gap-2"><x-lucide-check class="mt-0.5 size-4 shrink-0 text-emerald-400" /> {{ $point }}</li>
                    @endforeach
                </ul>
                <p class="border-t border-slate-800 pt-4 text-sm text-slate-400">Escopo e preço fechados antes de começar. Se fizer sentido seguir, o diagnóstico vira o plano do projeto.</p>
                <a href="#contato" data-track="Hero: pedir diagnóstico" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-indigo-500">Pedir diagnóstico</a>
            </div>
        </div>
    </section>

    {{-- Serviços --}}
    <section id="servicos" class="scroll-mt-16 border-t border-slate-800/70 bg-slate-900/40">
        <div class="mx-auto max-w-6xl px-4 py-16 md:py-20">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="font-heading text-3xl font-bold tracking-tight text-white">O que construímos, corrigimos e escalamos</h2>
                <p class="mt-3 text-slate-400">Para empresas que dependem de um sistema e não têm um time de TI para cuidar dele.</p>
            </div>
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['git-branch', 'Modernização de sistemas legados', 'Assumimos o sistema que ninguém mais quer mexer, eliminamos gambiarras e levamos sistemas desktop (Delphi, VB, Access) e PHP antigo para a web, sem parar a operação.', ['Legado', 'Migração para web', 'Refactoring']],
                    ['zap', 'Bugs críticos e performance', 'Debug forense em todo o stack: race conditions, vazamentos de memória e gargalos que seu time não conseguiu resolver.', ['Debug forense', 'Profiling', 'APM']],
                    ['life-buoy', 'Manutenção mensal', 'Um plano mensal para o sistema que não pode parar: horas de suporte incluídas, monitoramento, backups e atualizações de segurança.', ['Suporte', 'Monitoramento', 'Backups']],
                    ['code-xml', 'MVP e greenfield', 'Do quadro branco ao software em produção, com o stack certo (não o da moda) e CI/CD desde o primeiro dia.', ['MVP', 'Full-stack']],
                    ['cloud', 'DevOps e infraestrutura', 'AWS, GCP e Digital Ocean, ou nuvem privada em qualquer VPS. Kubernetes, Coolify e deploys sem downtime.', ['Kubernetes', 'CI/CD', 'IaC']],
                    ['shield-check', 'Hardening de segurança', 'Revisões baseadas em OWASP, preparação para pentest, gestão de secrets e SSDLC. Fechamos as brechas antes do incidente.', ['OWASP', 'LGPD', 'SSDLC']],
                ] as [$icon, $title, $text, $tags])
                    <div class="group rounded-2xl bg-slate-900 p-6 ring-1 ring-slate-800 transition hover:-translate-y-0.5 hover:ring-indigo-500/50">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300"><x-dynamic-component :component="'lucide-'.$icon" class="size-5" /></span>
                        <h3 class="mt-4 font-heading font-semibold text-white">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm text-slate-400">{{ $text }}</p>
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            @foreach ($tags as $tag)
                                <span class="rounded-md bg-slate-800 px-2 py-0.5 font-mono text-[0.7rem] text-slate-400">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Infraestrutura --}}
    <section id="infraestrutura" class="mx-auto max-w-6xl scroll-mt-16 px-4 py-16 md:py-20">
        <div class="grid items-center gap-10 lg:grid-cols-[1fr_1.2fr] lg:gap-14">
            <div class="grid gap-4">
                <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300"><x-lucide-server class="size-5" /></span>
                <h2 class="font-heading text-3xl font-bold tracking-tight text-white">Você entrega código. Nós cuidamos do resto.</h2>
                <p class="text-slate-400">
                    Estruturamos projetos nas nuvens tradicionais e também construímos nuvens privadas a partir de qualquer VPS.
                    Sem vendor lock-in: a infraestrutura fica sob o seu controle.
                </p>
                <ul class="grid gap-2 text-sm text-slate-300 sm:grid-cols-2">
                    @foreach (['AWS · GCP · Digital Ocean', 'Nuvem privada em qualquer VPS', 'Sem vendor lock-in', 'Clusters com auto-scaling', 'Backups e disaster recovery', 'Deploys sem downtime'] as $point)
                        <li class="flex items-start gap-2"><x-lucide-check class="mt-0.5 size-4 shrink-0 text-emerald-400" /> {{ $point }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="grid gap-4">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ([
                        ['AWS', 'EC2, RDS, Lambda', '#FF9900'], ['Docker', 'Containers', '#2496ED'], ['Kubernetes', 'Orquestração', '#326CE5'],
                        ['Terraform', 'IaC', '#7B42BC'], ['GitHub Actions', 'CI/CD', '#22c55e'], ['Prometheus', 'Observabilidade', '#E6522C'],
                        ['Grafana', 'Dashboards', '#F46800'], ['PostgreSQL', 'Banco principal', '#6b9bd2'], ['Redis', 'Cache e filas', '#DC382D'],
                    ] as [$name, $sub, $color])
                        <div class="rounded-xl bg-slate-900 p-3.5 ring-1 ring-slate-800">
                            <p class="flex items-center gap-2 text-sm font-semibold text-white"><span class="size-2 rounded-full" style="background: {{ $color }}"></span>{{ $name }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $sub }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="rounded-xl bg-slate-900 p-4 ring-1 ring-slate-800">
                    <p class="font-mono text-xs text-slate-500">// pipeline CI/CD</p>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5">
                        @foreach (['Lint', 'Testes', 'Build', 'Security scan', 'Homologação', 'Smoke tests', 'Produção'] as $stage)
                            <span class="flex items-center gap-1 rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 ring-1 ring-emerald-500/20">
                                <x-lucide-circle-check class="size-3.5" /> {{ $stage }}
                            </span>
                            @unless ($loop->last) <x-lucide-chevron-right class="size-3.5 text-slate-600" /> @endunless
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Como trabalhamos --}}
    <section class="mx-auto max-w-6xl px-4 pb-16 md:pb-20">
        <h2 class="text-center font-heading text-3xl font-bold tracking-tight text-white">Como trabalhamos</h2>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['search-code', 'Diagnóstico', 'Analisamos o código, a infraestrutura e o problema. Você recebe um relatório técnico direto, sem pitch de vendas.'],
                ['map', 'Plano', 'Roadmap com prioridades, riscos e estimativa por sprint. Você sabe o que vai ser feito e quando.'],
                ['rocket', 'Entrega', 'Sprints curtos, código revisado, deploys automáticos e relatórios transparentes até a produção.'],
            ] as $i => [$icon, $title, $text])
                <div class="grid gap-3">
                    <span class="flex size-11 items-center justify-center rounded-full bg-indigo-600 font-heading font-bold text-white">{{ $i + 1 }}</span>
                    <h3 class="flex items-center gap-2 font-heading text-lg font-semibold text-white"><x-dynamic-component :component="'lucide-'.$icon" class="size-5 text-indigo-400" /> {{ $title }}</h3>
                    <p class="text-slate-400">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Por que nós: faixa clara, o "invertido" da faixa escura do NT Omni --}}
    <section id="por-que" class="scroll-mt-16 bg-indigo-50 text-slate-900">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 md:py-20 lg:grid-cols-[1fr_1.2fr]">
            <div class="grid content-start gap-4">
                <h2 class="font-heading text-3xl font-bold tracking-tight">Rigor de engenharia, pragmatismo de negócio.</h2>
                <p class="text-slate-600">Trabalhamos no nível de Staff/Principal Engineer. Cada linha revisada, cada decisão documentada.</p>
                <dl class="mt-2 grid gap-3 text-sm">
                    @foreach ([
                        ['Linguagens', 'PHP · TypeScript · Java · Go · Python'],
                        ['Frameworks', 'Laravel · Livewire · React · Vue · Spring · NestJS'],
                        ['Bancos', 'PostgreSQL · MySQL · MongoDB · Redis · Kafka'],
                    ] as [$label, $value])
                        <div class="rounded-xl bg-white p-4 ring-1 ring-indigo-100">
                            <dt class="font-mono text-xs text-indigo-600">{{ $label }}</dt>
                            <dd class="mt-1 font-medium">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    ['award', 'Padrão enterprise', 'Experiência de Staff Engineer com barra de qualidade alta.'],
                    ['braces', 'Clean Code de verdade', 'SOLID e DRY como prática verificável, não só discurso.'],
                    ['lock', 'Segurança primeiro', 'OWASP, RBAC, criptografia e sanitização por padrão.'],
                    ['trending-up', 'Feito para escalar', 'Escalabilidade horizontal pensada desde o primeiro dia.'],
                    ['calendar-check', 'Entrega previsível', 'Sprints, estimativas claras e relatórios transparentes.'],
                    ['messages-square', 'Fala as duas línguas', 'Fluente em código e em contexto de negócio.'],
                ] as [$icon, $title, $text])
                    <div class="rounded-2xl bg-white p-5 ring-1 ring-indigo-100">
                        <x-dynamic-component :component="'lucide-'.$icon" class="size-6 text-indigo-600" />
                        <h3 class="mt-3 font-semibold">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Produtos --}}
    <section id="produtos" class="mx-auto max-w-6xl scroll-mt-16 px-4 py-16 md:py-20">
        <div class="grid items-center gap-8 rounded-3xl bg-slate-900 p-6 ring-1 ring-slate-800 md:grid-cols-[auto_1fr_auto] md:p-10">
            <svg viewBox="0 0 40 40" fill="none" class="size-16" aria-hidden="true">
                <rect width="40" height="40" rx="10" fill="#4f46e5" />
                <path d="M10 13.5A4.5 4.5 0 0 1 14.5 9h11A4.5 4.5 0 0 1 30 13.5v8a4.5 4.5 0 0 1-4.5 4.5H19l-5.2 4.3c-.65.54-1.63.08-1.63-.77V26A4.5 4.5 0 0 1 10 21.5v-8Z" fill="#fff" fill-opacity=".14" stroke="#fff" stroke-width="1.6" stroke-linejoin="round" />
                <path d="M15.5 17.5h3.2l2.6-3.2m-2.6 3.2 2.6 3.2" stroke="#a5f3fc" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="15.5" cy="17.5" r="1.8" fill="#fff" /><circle cx="23.8" cy="14.3" r="1.8" fill="#a5f3fc" /><circle cx="23.8" cy="20.7" r="1.8" fill="#34d399" />
            </svg>
            <div class="grid gap-2">
                <p class="font-mono text-xs text-indigo-300">Nosso produto</p>
                <h2 class="font-heading text-2xl font-bold tracking-tight text-white">NT Omni: atendimento no WhatsApp que agenda, vende e responde.</h2>
                <p class="text-slate-400">Central de atendimento com fluxos automáticos, agenda, funil de vendas e IA opcional. Feito pela NT Sistemas Web.</p>
            </div>
            <a href="https://nt-omni.com.br" target="_blank" rel="noopener" data-track="Saída: NT Omni" class="inline-flex items-center gap-2 justify-self-start rounded-lg px-5 py-3 font-semibold text-slate-200 ring-1 ring-slate-700 hover:bg-slate-800">
                Conhecer <x-lucide-external-link class="size-4" />
            </a>
        </div>
    </section>

    {{-- Perguntas --}}
    <section id="perguntas" class="scroll-mt-16 border-t border-slate-800/70 bg-slate-900/40">
        <div class="mx-auto max-w-3xl px-4 py-16 md:py-20">
            <h2 class="text-center font-heading text-3xl font-bold tracking-tight text-white">Perguntas frequentes</h2>
            <div class="mt-10 grid gap-3">
                @foreach ([
                    ['Vocês atendem empresas pequenas?', 'Sim. Boa parte dos clientes são pequenas e médias empresas que dependem de um sistema interno e não têm time de TI. Você fala direto com quem vai programar.'],
                    ['Vocês pegam sistemas que outra equipe começou?', 'Sim, é boa parte do que fazemos. Começamos com um diagnóstico do código e da infraestrutura e dizemos com clareza o que vale manter, refatorar ou reescrever.'],
                    ['Precisa parar o sistema para modernizar?', 'Não. A migração é feita em etapas, com o sistema rodando, testes automatizados e deploys sem downtime.'],
                    ['Como funciona o orçamento?', 'Depois do diagnóstico você recebe um plano com escopo, sprints e valor. Também atendemos por hora em casos de emergência, como bugs em produção.'],
                    ['Tem plano de manutenção mensal?', 'Sim. Um valor fixo por mês com horas de suporte, monitoramento, backups e atualizações. Bom para quem não pode ficar sem o sistema e não quer depender de um programador avulso.'],
                    ['Vocês cuidam da infraestrutura depois da entrega?', 'Sim. Montamos a infraestrutura (AWS, GCP, Digital Ocean ou VPS própria com Kubernetes ou Coolify) e podemos acompanhar com monitoramento e manutenção.'],
                    ['Em quanto tempo vocês respondem?', 'Em até '.$site['response_hours'].' horas úteis com uma avaliação técnica inicial. Mande pelo formulário abaixo.'],
                ] as [$question, $answer])
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
                <h2 class="font-heading text-3xl font-bold tracking-tight text-white">Vamos conversar sobre o seu projeto.</h2>
                <p class="text-slate-400">Sem pitch de vendas. Uma avaliação técnica direta do seu problema, em até {{ $site['response_hours'] }} horas.</p>
                <ul class="mt-2 grid gap-3 text-sm">
                    <li>
                        <a href="mailto:{{ $site['contact_email'] }}" class="flex items-center gap-3 text-slate-300 hover:text-white">
                            <span class="flex size-9 items-center justify-center rounded-lg bg-indigo-500/15 text-indigo-300"><x-lucide-mail class="size-4" /></span>
                            {{ $site['contact_email'] }}
                        </a>
                    </li>
                    @if ($site['whatsapp'])
                        <li>
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $site['whatsapp']) }}" target="_blank" rel="noopener" class="flex items-center gap-3 text-slate-300 hover:text-white">
                                <span class="flex size-9 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-300"><x-lucide-message-circle class="size-4" /></span>
                                WhatsApp
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <livewire:site.contact-form />
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-6xl px-4 pb-16">
        <div class="grid gap-6 rounded-3xl bg-indigo-600 px-6 py-12 text-center text-white md:px-12">
            <h2 class="font-heading text-3xl font-bold tracking-tight">Seu sistema não precisa continuar travando o negócio.</h2>
            <p class="mx-auto max-w-xl text-indigo-100">Conte o problema hoje e receba uma avaliação inicial em até {{ $site['response_hours'] }} horas.</p>
            <a href="#contato" class="mx-auto rounded-lg bg-white px-6 py-3 font-semibold text-indigo-700 hover:bg-indigo-50">Pedir diagnóstico</a>
        </div>
    </section>
@endsection
