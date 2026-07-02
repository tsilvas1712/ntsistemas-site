import { useState, createContext, useContext } from "react";
import {
  ArrowRight, Code2, Layers, Zap, Server, GitBranch, Shield, Cloud,
  Terminal, ChevronRight, CheckCircle2, Menu, X, ExternalLink,
  Cpu, Database, Globe, Lock, Boxes, Activity,
} from "lucide-react";

/* ─── i18n ─── */
type Lang = "pt" | "en";

const translations = {
  pt: {
    nav: {
      services: "Serviços",
      expertise: "Expertise",
      portfolio: "Portfólio",
      infra: "Infraestrutura Cloud",
      cta: "Solicitar Avaliação Técnica",
    },
    hero: {
      badge: "Disponível para novos projetos",
      headline1: "Modernizando",
      headline2: "Codebases,",
      headline3: "Escalando Ideias.",
      sub: "10+ anos de experiência em engenharia de elite. Especializados em resgatar sistemas legados, eliminar bugs críticos, reescritas arquiteturais e MVPs greenfield do conceito à produção.",
      tags: ["Resgate de Legado", "Bugs Críticos", "Clean Architecture", "MVP Greenfield", "DevOps"],
      cta1: "Vamos Conversar",
      cta2: "Ver Stack",
      stat1: "Anos de Engenharia",
      stat2: "Projetos Entregues",
      stat3: "SLA de Uptime",
    },
    terminal: {
      label: "nt-stack ~ análise",
      cmd: "$ nt analyze --repo monolito-legado",
      scan: "Escaneando 142.890 linhas de código...",
      detected: "Framework detectado: Spring Boot 1.x → migração 3.x",
      critical: "■ Problemas críticos encontrados: 23",
      issues: [
        { sym: "✗", color: "#ef4444", text: "Vetores de SQL injection: 7" },
        { sym: "✗", color: "#f59e0b", text: "Padrões N+1 de query: 11" },
        { sym: "✗", color: "#ef4444", text: "I/O bloqueante no event loop: 5" },
      ],
      debt: "■ Dívida arquitetural:",
      debtItems: ["→ God classes: 14", "→ Dependências circulares: 38", "→ Cobertura de testes: 6%"],
      road1: "✓ Gerando roadmap de modernização...",
      road2: "✓ Esforço estimado: 8–12 sprints",
    },
    services: {
      badge: "Serviços Principais",
      heading1: "O que construímos,",
      heading2: "corrigimos e escalamos.",
      sub: "Serviços de engenharia de ponta a ponta para empresas que precisam de qualidade de nível expert — não de output júnior.",
      items: [
        {
          title: "Modernização de Código Legado",
          desc: "Eliminação sistemática de dívida técnica, anti-padrões e gambiarras. Refatoramos codebases para sistemas limpos, manuteníveis e de alto desempenho sem parar a entrega.",
          tags: ["Refactoring", "Dívida Técnica", "Anti-padrões"],
        },
        {
          title: "Correção de Bugs & Performance",
          desc: "Debug forense profundo em todo o stack. Caçamos race conditions, memory leaks e gargalos de performance que seu time não conseguiu resolver.",
          tags: ["Debug Forense", "Profiling", "APM"],
        },
        {
          title: "Redesign Arquitetural",
          desc: "Migração planejada de monólito para microserviços, DDD, CQRS, event sourcing e Clean Architecture — sem interrupção do negócio.",
          tags: ["Microserviços", "DDD", "Clean Arch"],
        },
        {
          title: "MVP & Desenvolvimento Greenfield",
          desc: "Do quadro branco ao software pronto para produção. Construímos MVPs escaláveis e seguros com o stack certo — não o da moda — com CI/CD desde o dia um.",
          tags: ["MVP", "Full-Stack", "Pronto p/ Launch"],
        },
        {
          title: "DevOps & Infraestrutura Zero-Ops",
          desc: "Gerenciamento completo do ciclo de vida na nuvem. AWS, GCP, Azure — deployamos, monitoramos, escalamos e gerenciamos sua infra para seu time focar no produto.",
          tags: ["AWS", "Kubernetes", "CI/CD"],
        },
        {
          title: "Hardening de Segurança",
          desc: "Revisões de segurança baseadas em OWASP, prep para pentest, gestão de secrets e implementação de SSDLC. Fechamos as brechas antes que virem incidentes.",
          tags: ["OWASP", "Pen Testing", "SSDLC"],
        },
      ],
    },
    infra: {
      badge: "Infraestrutura Full-Stack",
      heading1: "Você entrega código.",
      heading2: "Nós cuidamos do resto.",
      sub: "Além de estruturar e configurar projetos nas nuvens tradicionais — AWS, GCP, Digital Ocean — também construímos nuvens privadas a partir de qualquer VPS. Sem vendor lock-in. Infraestrutura sob sua soberania total.",
      features: [
        "AWS · GCP · Digital Ocean",
        "Nuvem privada em qualquer VPS",
        "Sem vendor lock-in",
        "Clusters com auto-scaling",
        "Backups e disaster recovery",
        "Deploys zero-downtime",
      ],
      stackLabel: "// STACK COMPROVADO",
      pipelineLabel: "// STATUS DO PIPELINE CI/CD",
    },
    profile: {
      badge: "Por que NT Sistemas Web",
      heading1: "Rigor de engenharia",
      heading2: "pragmatismo de negócio.",
      sub: "Operamos no nível de Staff/Principal Engineer. Sem juniores tocando seu codebase. Cada linha revisada, cada decisão documentada.",
      role: "10+ anos · Full-Stack & Cloud",
      pitch: "Operamos no nível de Staff/Principal Engineer. Sem juniores tocando seu codebase. Cada linha revisada, cada decisão documentada.",
      points: [
        "Background de Expert/Staff Engineer com padrão de qualidade enterprise",
        "Clean Code, SOLID, DRY — não só princípios, mas prática verificável",
        "Security-first: OWASP, RBAC, criptografia de dados, sanitização por padrão",
        "Escalabilidade horizontal incorporada em cada decisão de design desde o início",
        "Entrega previsível via sprints iterativos, estimativas claras e relatórios transparentes",
        "Comunicação técnica bilíngue — fluente em código e em contexto de negócio",
      ],
      lang: "Linguagens",
      fw: "Frameworks",
      db: "Bancos de Dados",
    },
    contact: {
      badge: "Escope seu Projeto",
      heading1: "Inicie a",
      heading2: "conversa.",
      sub: "Sem pitch de vendas. Uma avaliação técnica direta do seu problema, em até 48 horas.",
      painPoints: [
        { id: "legacy", label: "Resgate de Sistema Legado", icon: "🚑" },
        { id: "bugs", label: "Forense de Bugs Críticos", icon: "🐛" },
        { id: "arch", label: "Redesign Arquitetural", icon: "🏗️" },
        { id: "mvp", label: "Novo MVP / Greenfield", icon: "🚀" },
        { id: "devops", label: "DevOps & Infraestrutura", icon: "☁️" },
        { id: "security", label: "Hardening de Segurança", icon: "🔒" },
      ],
      step1: "01 / Problema",
      step2: "02 / Detalhes",
      selectLabel: "Selecione tudo que se aplica:",
      continue: "Continuar",
      fields: {
        name: "Nome Completo",
        namePh: "Maria Silva",
        email: "E-mail Corporativo",
        emailPh: "maria@empresa.com",
        company: "Empresa / Projeto",
        companyPh: "Empresa Ltda.",
        budget: "Orçamento Aproximado",
        budgetPh: "R$ 20k–50k",
        stack: "Stack Atual",
        stackPh: "ex: Java Spring Boot, MySQL, AWS EC2, React...",
        message: "Descreva seu problema / desafio",
        messagePh: "O que está quebrado, lento ou bloqueando você? Seja tão técnico quanto quiser.",
      },
      back: "Voltar",
      submit: "Enviar Brief Técnico",
      successTitle: "Brief recebido.",
      successSub: "Responderemos com uma avaliação técnica em até 48 horas.",
    },
    footer: {
      copy: "© 2024 NT Sistemas Web · Todos os direitos reservados · Engenharia de Elite",
    },
  },
  en: {
    nav: {
      services: "Services",
      expertise: "Expertise",
      portfolio: "Portfolio",
      infra: "Cloud Infrastructure",
      cta: "Request Technical Assessment",
    },
    hero: {
      badge: "Available for new engagements",
      headline1: "Modernizing",
      headline2: "Codebases,",
      headline3: "Scaling Ideas.",
      sub: "10+ years of elite engineering experience. Specializing in rescuing legacy systems, eliminating critical bugs, architectural rewrites, and greenfield MVPs from concept to production.",
      tags: ["Legacy Rescue", "Bug Forensics", "Clean Architecture", "Greenfield MVP", "DevOps"],
      cta1: "Let's Talk Tech",
      cta2: "View Stack",
      stat1: "Years Engineering",
      stat2: "Projects Delivered",
      stat3: "Uptime SLA",
    },
    terminal: {
      label: "nt-stack ~ analysis",
      cmd: "$ nt analyze --repo legacy-monolith",
      scan: "Scanning 142,890 lines of code...",
      detected: "Detected framework: Spring Boot 1.x → 3.x migration",
      critical: "■ Critical issues found: 23",
      issues: [
        { sym: "✗", color: "#ef4444", text: "SQL injection vectors: 7" },
        { sym: "✗", color: "#f59e0b", text: "N+1 query patterns: 11" },
        { sym: "✗", color: "#ef4444", text: "Blocking I/O in event loop: 5" },
      ],
      debt: "■ Architecture debt:",
      debtItems: ["→ God classes: 14", "→ Circular dependencies: 38", "→ Test coverage: 6%"],
      road1: "✓ Generating modernization roadmap...",
      road2: "✓ Estimated effort: 8–12 sprints",
    },
    services: {
      badge: "Core Services",
      heading1: "What we build,",
      heading2: "fix, and scale.",
      sub: "End-to-end software engineering services for companies that need expert-level quality — not junior output.",
      items: [
        {
          title: "Legacy Code Modernization",
          desc: "Systematic elimination of tech debt, anti-patterns, and 'gambiarras'. We refactor codebases to clean, maintainable, high-performance systems without stopping delivery.",
          tags: ["Refactoring", "Tech Debt", "Anti-patterns"],
        },
        {
          title: "Bug Fixing & Performance Tuning",
          desc: "Deep forensic debugging across the full stack. We hunt down elusive race conditions, memory leaks, and performance bottlenecks your team hasn't been able to solve.",
          tags: ["Forensic Debug", "Profiling", "APM"],
        },
        {
          title: "Architectural Redesign",
          desc: "Planned migration from monolith to microservices, domain-driven design, CQRS, event sourcing, and Clean Architecture — with zero business disruption.",
          tags: ["Microservices", "DDD", "Clean Arch"],
        },
        {
          title: "MVP & Greenfield Development",
          desc: "From whiteboard to production-ready software. We build scalable, secure MVPs using the right stack — not the trendy one — with proper CI/CD from day one.",
          tags: ["MVP", "Full-Stack", "Launch-Ready"],
        },
        {
          title: "DevOps & Zero-Ops Infrastructure",
          desc: "Complete cloud lifecycle management. AWS, GCP, Azure — we deploy, monitor, auto-scale, and manage your infrastructure so your team stays focused on product.",
          tags: ["AWS", "Kubernetes", "CI/CD"],
        },
        {
          title: "Security Hardening",
          desc: "OWASP-based security reviews, penetration testing prep, secrets management, and secure SDLC implementation. We close gaps before they become incidents.",
          tags: ["OWASP", "Pen Testing", "SSDLC"],
        },
      ],
    },
    infra: {
      badge: "Full-Stack Infrastructure",
      heading1: "You ship code.",
      heading2: "We handle the rest.",
      sub: "Beyond structuring and configuring projects on traditional clouds — AWS, GCP, Digital Ocean — we also build private clouds on top of any VPS infrastructure. No vendor lock-in. Full sovereignty over your stack.",
      features: [
        "AWS · GCP · Digital Ocean",
        "Private cloud on any VPS",
        "Zero vendor lock-in",
        "Auto-scaling clusters",
        "Backups & disaster recovery",
        "Zero-downtime deploys",
      ],
      stackLabel: "// PROVEN TECH STACK",
      pipelineLabel: "// CI/CD PIPELINE STATUS",
    },
    profile: {
      badge: "Why NT Sistemas Web",
      heading1: "Engineering rigor meets",
      heading2: "business pragmatism.",
      sub: "We operate at Staff/Principal Engineer level. No juniors touching your codebase. Every line reviewed, every decision documented.",
      role: "10+ years · Full-Stack & Cloud",
      pitch: "We operate at Staff/Principal Engineer level. No juniors touching your codebase. Every line reviewed, every decision documented.",
      points: [
        "Expert/Staff Engineer background with enterprise-grade quality bar",
        "Clean Code, SOLID, DRY — not just principles, but verifiable practice",
        "Security-first: OWASP, RBAC, data encryption, input sanitization by default",
        "Horizontal scalability baked into every design decision from day one",
        "Predictable delivery via iterative sprints, clear estimates, and transparent reporting",
        "Bilingual technical communication — fluent in both code and business context",
      ],
      lang: "Languages",
      fw: "Frameworks",
      db: "Databases",
    },
    contact: {
      badge: "Scope Your Project",
      heading1: "Start the",
      heading2: "conversation.",
      sub: "No sales pitch. A direct technical assessment of your problem, within 48 hours.",
      painPoints: [
        { id: "legacy", label: "Legacy System Rescue", icon: "🚑" },
        { id: "bugs", label: "Critical Bug Forensics", icon: "🐛" },
        { id: "arch", label: "Architectural Redesign", icon: "🏗️" },
        { id: "mvp", label: "New MVP / Greenfield", icon: "🚀" },
        { id: "devops", label: "DevOps & Infrastructure", icon: "☁️" },
        { id: "security", label: "Security Hardening", icon: "🔒" },
      ],
      step1: "01 / Pain Point",
      step2: "02 / Project Details",
      selectLabel: "Select all that apply to your situation:",
      continue: "Continue",
      fields: {
        name: "Full Name",
        namePh: "John Smith",
        email: "Work Email",
        emailPh: "john@company.com",
        company: "Company / Project",
        companyPh: "Acme Corp.",
        budget: "Approximate Budget",
        budgetPh: "$5k–20k",
        stack: "Current Tech Stack",
        stackPh: "e.g. Java Spring Boot, MySQL, AWS EC2, React...",
        message: "Describe your problem / challenge",
        messagePh: "What's broken, what's slow, what's blocking you? Be as technical as you want.",
      },
      back: "Back",
      submit: "Send Technical Brief",
      successTitle: "Brief received.",
      successSub: "We'll respond with a technical assessment within 48 hours.",
    },
    footer: {
      copy: "© 2024 NT Sistemas Web · All rights reserved · Elite Engineering",
    },
  },
} as const;

/* ─── Context ─── */
const LangContext = createContext<{ lang: Lang; t: typeof translations["pt"] }>({
  lang: "pt",
  t: translations.pt,
});
const useLang = () => useContext(LangContext);

/* ─── Logo Mark ─── */
function NTLogo({ size = 36 }: { size?: number }) {
  return (
    <svg width={size} height={size} viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="logoGrad" x1="0" y1="0" x2="36" y2="36" gradientUnits="userSpaceOnUse">
          <stop stopColor="#7C3AED" />
          <stop offset="1" stopColor="#A78BFA" />
        </linearGradient>
      </defs>
      <path d="M6 4 L2 4 L2 32 L6 32" stroke="url(#logoGrad)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <path d="M30 4 L34 4 L34 32 L30 32" stroke="url(#logoGrad)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <path d="M9 26 L9 10 L16 22 L16 10" stroke="url(#logoGrad)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <path d="M19 10 L27 10 M23 10 L23 26" stroke="url(#logoGrad)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
    </svg>
  );
}

/* ─── Language Toggle ─── */
function LangToggle({ lang, setLang }: { lang: Lang; setLang: (l: Lang) => void }) {
  return (
    <div className="flex items-center rounded-lg p-0.5 border" style={{ borderColor: "rgba(124,58,237,0.25)", background: "rgba(124,58,237,0.07)" }}>
      {(["pt", "en"] as Lang[]).map((l) => (
        <button
          key={l}
          onClick={() => setLang(l)}
          className="px-2.5 py-1 rounded-md text-xs font-mono font-semibold transition-all duration-150"
          style={{
            background: lang === l ? "rgba(124,58,237,0.5)" : "transparent",
            color: lang === l ? "#fff" : "#64748B",
          }}
        >
          {l === "pt" ? "PT" : "EN"}
        </button>
      ))}
    </div>
  );
}

/* ─── Mesh Gradient ─── */
function MeshGradient() {
  return (
    <div className="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden>
      <div className="absolute -top-40 -right-40 w-[700px] h-[700px] rounded-full opacity-[0.12]" style={{ background: "radial-gradient(circle, #7C3AED 0%, transparent 70%)" }} />
      <div className="absolute top-1/2 -left-60 w-[500px] h-[500px] rounded-full opacity-[0.08]" style={{ background: "radial-gradient(circle, #6D28D9 0%, transparent 70%)" }} />
      <div className="absolute -bottom-20 right-1/3 w-[400px] h-[400px] rounded-full opacity-[0.06]" style={{ background: "radial-gradient(circle, #A78BFA 0%, transparent 70%)" }} />
      <div className="absolute inset-0 opacity-[0.03]" style={{ backgroundImage: "linear-gradient(rgba(124,58,237,1) 1px, transparent 1px), linear-gradient(90deg, rgba(124,58,237,1) 1px, transparent 1px)", backgroundSize: "60px 60px" }} />
    </div>
  );
}

/* ─── Pill Badge ─── */
function PillBadge({ children }: { children: React.ReactNode }) {
  return (
    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-medium border" style={{ background: "rgba(124,58,237,0.12)", borderColor: "rgba(124,58,237,0.35)", color: "#A78BFA" }}>
      {children}
    </span>
  );
}

/* ─── Navbar ─── */
function Navbar({ lang, setLang }: { lang: Lang; setLang: (l: Lang) => void }) {
  const { t } = useLang();
  const [open, setOpen] = useState(false);
  const links = [t.nav.services, t.nav.expertise, t.nav.portfolio, t.nav.infra];
  const hrefs = ["#services", "#expertise", "#portfolio", "#infrastructure"];

  return (
    <nav className="fixed top-0 left-0 right-0 z-50 border-b" style={{ borderColor: "rgba(124,58,237,0.15)", background: "rgba(15,23,42,0.85)", backdropFilter: "blur(16px)" }}>
      <div className="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="#" className="flex items-center gap-3">
          <NTLogo size={34} />
          <div>
            <span className="font-display text-white font-semibold text-base tracking-tight" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>NT Sistemas Web</span>
            <div className="text-[10px] font-mono" style={{ color: "#7C3AED", letterSpacing: "0.1em" }}>ELITE ENGINEERING</div>
          </div>
        </a>

        <div className="hidden md:flex items-center gap-7">
          {links.map((l, i) => (
            <a key={l} href={hrefs[i]} className="text-sm font-medium transition-colors duration-200" style={{ color: "#94A3B8" }}
              onMouseEnter={(e) => ((e.currentTarget as HTMLElement).style.color = "#A78BFA")}
              onMouseLeave={(e) => ((e.currentTarget as HTMLElement).style.color = "#94A3B8")}>
              {l}
            </a>
          ))}
        </div>

        <div className="hidden md:flex items-center gap-3">
          <LangToggle lang={lang} setLang={setLang} />
          <a href="#contact" className="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 border"
            style={{ background: "linear-gradient(135deg, #7C3AED, #6D28D9)", borderColor: "rgba(167,139,250,0.3)", color: "#fff", boxShadow: "0 0 20px rgba(124,58,237,0.4)" }}
            onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.boxShadow = "0 0 32px rgba(124,58,237,0.65)"; }}
            onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.boxShadow = "0 0 20px rgba(124,58,237,0.4)"; }}>
            {t.nav.cta}
          </a>
        </div>

        <div className="md:hidden flex items-center gap-2">
          <LangToggle lang={lang} setLang={setLang} />
          <button className="p-2 rounded-lg" style={{ color: "#94A3B8" }} onClick={() => setOpen(!open)}>
            {open ? <X size={20} /> : <Menu size={20} />}
          </button>
        </div>
      </div>

      {open && (
        <div className="md:hidden border-t px-6 py-4 flex flex-col gap-4" style={{ borderColor: "rgba(124,58,237,0.15)", background: "rgba(15,23,42,0.95)" }}>
          {links.map((l, i) => (
            <a key={l} href={hrefs[i]} className="text-sm font-medium" style={{ color: "#94A3B8" }} onClick={() => setOpen(false)}>{l}</a>
          ))}
          <a href="#contact" className="px-4 py-2 text-sm font-semibold rounded-lg text-center" style={{ background: "linear-gradient(135deg, #7C3AED, #6D28D9)", color: "#fff" }} onClick={() => setOpen(false)}>
            {t.nav.cta}
          </a>
        </div>
      )}
    </nav>
  );
}

/* ─── Hero ─── */
function Hero() {
  const { t } = useLang();
  const h = t.hero;
  const term = t.terminal;

  return (
    <section className="relative min-h-screen flex items-center pt-16 overflow-hidden" style={{ background: "#0F172A" }}>
      <MeshGradient />
      <div className="relative max-w-7xl mx-auto px-6 py-24 grid lg:grid-cols-2 gap-16 items-center">
        <div>
          <PillBadge>
            <span className="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse" />
            {h.badge}
          </PillBadge>

          <h1 className="mt-8 text-5xl lg:text-6xl xl:text-7xl font-extrabold leading-[1.05] tracking-tight" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>
            {h.headline1}{" "}
            <span style={{ background: "linear-gradient(135deg, #7C3AED 0%, #A78BFA 50%, #C4B5FD 100%)", WebkitBackgroundClip: "text", WebkitTextFillColor: "transparent" }}>
              {h.headline2}
            </span>
            <br />
            {h.headline3}
          </h1>

          <p className="mt-6 text-lg leading-relaxed max-w-xl" style={{ color: "#94A3B8" }}>{h.sub}</p>

          <div className="mt-4 flex flex-wrap gap-2">
            {h.tags.map((tag) => (
              <span key={tag} className="text-xs font-mono px-2.5 py-1 rounded border" style={{ borderColor: "rgba(124,58,237,0.25)", color: "#7C3AED", background: "rgba(124,58,237,0.08)" }}>
                {tag}
              </span>
            ))}
          </div>

          <div className="mt-10 flex flex-wrap gap-4">
            <a href="#contact" className="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-200"
              style={{ background: "linear-gradient(135deg, #7C3AED, #6D28D9)", color: "#fff", boxShadow: "0 0 30px rgba(124,58,237,0.45), inset 0 1px 0 rgba(255,255,255,0.1)" }}
              onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.boxShadow = "0 0 50px rgba(124,58,237,0.7), inset 0 1px 0 rgba(255,255,255,0.1)"; }}
              onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.boxShadow = "0 0 30px rgba(124,58,237,0.45), inset 0 1px 0 rgba(255,255,255,0.1)"; }}>
              {h.cta1} <ArrowRight size={16} />
            </a>
            <a href="#services" className="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-200 border"
              style={{ borderColor: "rgba(124,58,237,0.4)", color: "#A78BFA", background: "rgba(124,58,237,0.06)" }}
              onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.background = "rgba(124,58,237,0.14)"; }}
              onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.background = "rgba(124,58,237,0.06)"; }}>
              <Terminal size={16} /> {h.cta2}
            </a>
          </div>

          <div className="mt-14 flex gap-10 flex-wrap">
            {[
              { value: "10+", label: h.stat1 },
              { value: "80+", label: h.stat2 },
              { value: "99.9%", label: h.stat3 },
            ].map((s) => (
              <div key={s.label}>
                <div className="text-3xl font-extrabold" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#A78BFA" }}>{s.value}</div>
                <div className="text-xs font-mono mt-1" style={{ color: "#64748B" }}>{s.label}</div>
              </div>
            ))}
          </div>
        </div>

        {/* Terminal card */}
        <div className="hidden lg:block">
          <div className="rounded-2xl border overflow-hidden" style={{ borderColor: "rgba(124,58,237,0.25)", background: "rgba(30,27,75,0.6)", boxShadow: "0 0 60px rgba(124,58,237,0.15), inset 0 1px 0 rgba(255,255,255,0.04)" }}>
            <div className="flex items-center gap-2 px-4 py-3 border-b" style={{ borderColor: "rgba(124,58,237,0.15)", background: "rgba(15,23,42,0.5)" }}>
              <span className="w-3 h-3 rounded-full" style={{ background: "#ef4444" }} />
              <span className="w-3 h-3 rounded-full" style={{ background: "#f59e0b" }} />
              <span className="w-3 h-3 rounded-full" style={{ background: "#22c55e" }} />
              <span className="ml-3 text-xs font-mono" style={{ color: "#475569" }}>{term.label}</span>
            </div>
            <div className="p-6 font-mono text-sm" style={{ color: "#94A3B8" }}>
              <div style={{ color: "#7C3AED" }}>{term.cmd}</div>
              <div className="mt-3" style={{ color: "#64748B" }}>{term.scan}</div>
              <div className="mt-2" style={{ color: "#64748B" }}>{term.detected}</div>
              <br />
              <div style={{ color: "#A78BFA" }}>{term.critical}</div>
              <div className="mt-1 ml-4">
                {term.issues.map((i) => (
                  <div key={i.text} style={{ color: i.color }}>{i.sym} {i.text}</div>
                ))}
              </div>
              <br />
              <div style={{ color: "#A78BFA" }}>{term.debt}</div>
              <div className="mt-1 ml-4 space-y-0.5" style={{ color: "#64748B" }}>
                {term.debtItems.map((d) => <div key={d}>{d}</div>)}
              </div>
              <br />
              <div style={{ color: "#22c55e" }}>{term.road1}</div>
              <div className="mt-1" style={{ color: "#22c55e" }}>{term.road2}</div>
              <br />
              <div className="flex items-center gap-1" style={{ color: "#7C3AED" }}>
                <span>$ </span>
                <span className="w-2 h-4 animate-pulse" style={{ background: "#7C3AED" }} />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

/* ─── Services ─── */
const serviceIcons = [GitBranch, Zap, Layers, Code2, Cloud, Shield];
const serviceColors = ["#7C3AED", "#8B5CF6", "#6D28D9", "#7C3AED", "#8B5CF6", "#6D28D9"];

function Services() {
  const { t } = useLang();
  const s = t.services;

  return (
    <section id="services" className="relative py-28 overflow-hidden" style={{ background: "#0F172A" }}>
      <div className="absolute inset-0 opacity-[0.04]" style={{ backgroundImage: "linear-gradient(rgba(124,58,237,1) 1px, transparent 1px), linear-gradient(90deg, rgba(124,58,237,1) 1px, transparent 1px)", backgroundSize: "60px 60px" }} />
      <div className="relative max-w-7xl mx-auto px-6">
        <div className="max-w-2xl">
          <PillBadge><Server size={11} /> {s.badge}</PillBadge>
          <h2 className="mt-5 text-4xl lg:text-5xl font-extrabold leading-tight" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>
            {s.heading1}<br />
            <span style={{ background: "linear-gradient(135deg, #7C3AED, #A78BFA)", WebkitBackgroundClip: "text", WebkitTextFillColor: "transparent" }}>{s.heading2}</span>
          </h2>
          <p className="mt-4 text-base leading-relaxed" style={{ color: "#64748B" }}>{s.sub}</p>
        </div>

        <div className="mt-16 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
          {s.items.map((item, idx) => {
            const Icon = serviceIcons[idx];
            return (
              <div key={item.title} className="group relative p-6 rounded-2xl border transition-all duration-300 cursor-default"
                style={{ borderColor: "rgba(124,58,237,0.18)", background: "rgba(30,27,75,0.35)" }}
                onMouseEnter={(e) => { const el = e.currentTarget as HTMLElement; el.style.borderColor = "rgba(124,58,237,0.5)"; el.style.background = "rgba(30,27,75,0.6)"; el.style.transform = "translateY(-2px)"; el.style.boxShadow = "0 0 30px rgba(124,58,237,0.12)"; }}
                onMouseLeave={(e) => { const el = e.currentTarget as HTMLElement; el.style.borderColor = "rgba(124,58,237,0.18)"; el.style.background = "rgba(30,27,75,0.35)"; el.style.transform = "translateY(0)"; el.style.boxShadow = "none"; }}>
                <div className="absolute top-6 right-6 w-2 h-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity" style={{ background: serviceColors[idx], boxShadow: `0 0 8px ${serviceColors[idx]}` }} />
                <div className="w-10 h-10 rounded-xl flex items-center justify-center mb-5" style={{ background: "rgba(124,58,237,0.15)", border: "1px solid rgba(124,58,237,0.25)" }}>
                  <Icon size={18} style={{ color: "#A78BFA" }} />
                </div>
                <h3 className="text-base font-bold mb-2" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#E2E8F0" }}>{item.title}</h3>
                <p className="text-sm leading-relaxed mb-5" style={{ color: "#64748B" }}>{item.desc}</p>
                <div className="flex flex-wrap gap-1.5">
                  {item.tags.map((tag) => (
                    <span key={tag} className="text-[10px] font-mono px-2 py-0.5 rounded" style={{ background: "rgba(124,58,237,0.1)", color: "#7C3AED", border: "1px solid rgba(124,58,237,0.2)" }}>{tag}</span>
                  ))}
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}

/* ─── Infrastructure ─── */
const techStack = [
  { name: "AWS", sub: "EC2, RDS, Lambda, EKS", color: "#FF9900" },
  { name: "Docker", sub: "Containerization", color: "#2496ED" },
  { name: "Kubernetes", sub: "Orchestration", color: "#326CE5" },
  { name: "Terraform", sub: "IaC", color: "#7B42BC" },
  { name: "GitHub Actions", sub: "CI/CD Pipelines", color: "#22c55e" },
  { name: "Prometheus", sub: "Observability", color: "#E6522C" },
  { name: "Grafana", sub: "Dashboards", color: "#F46800" },
  { name: "Nginx / Caddy", sub: "Reverse Proxy", color: "#009639" },
  { name: "Redis", sub: "Caching Layer", color: "#DC382D" },
  { name: "PostgreSQL", sub: "Primary DB", color: "#336791" },
  { name: "Kafka", sub: "Event Streaming", color: "#A78BFA" },
  { name: "Vault", sub: "Secrets Mgmt", color: "#FFD814" },
];
const infraIcons = [Globe, Activity, Lock, Cpu, Database, Boxes];
const pipelineSteps = ["Lint", "Test", "Build", "Security Scan", "Stage Deploy", "Smoke Tests", "Prod Deploy"];

function Infrastructure() {
  const { t } = useLang();
  const inf = t.infra;

  return (
    <section id="infrastructure" className="relative py-28 overflow-hidden" style={{ background: "#080E1C" }}>
      <div className="absolute top-0 left-0 right-0 h-px" style={{ background: "linear-gradient(90deg, transparent, rgba(124,58,237,0.5), transparent)" }} />
      <div className="absolute bottom-0 left-0 right-0 h-px" style={{ background: "linear-gradient(90deg, transparent, rgba(124,58,237,0.5), transparent)" }} />
      <div className="relative max-w-7xl mx-auto px-6">
        <div className="grid lg:grid-cols-2 gap-16 items-start">
          <div>
            <PillBadge><Cloud size={11} /> {inf.badge}</PillBadge>
            <h2 className="mt-5 text-4xl lg:text-5xl font-extrabold leading-tight" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>
              {inf.heading1}<br />
              <span style={{ background: "linear-gradient(135deg, #7C3AED, #A78BFA)", WebkitBackgroundClip: "text", WebkitTextFillColor: "transparent" }}>{inf.heading2}</span>
            </h2>
            <p className="mt-4 text-base leading-relaxed" style={{ color: "#64748B" }}>{inf.sub}</p>
            <div className="mt-10 grid grid-cols-2 gap-3">
              {inf.features.map((f, i) => {
                const Icon = infraIcons[i];
                return (
                  <div key={f} className="flex items-center gap-3 p-3 rounded-xl border" style={{ borderColor: "rgba(124,58,237,0.15)", background: "rgba(30,27,75,0.3)" }}>
                    <Icon size={15} style={{ color: "#7C3AED" }} />
                    <span className="text-sm font-medium" style={{ color: "#CBD5E1" }}>{f}</span>
                  </div>
                );
              })}
            </div>
          </div>

          <div>
            <div className="text-xs font-mono mb-5" style={{ color: "#475569" }}>{inf.stackLabel}</div>
            <div className="grid grid-cols-3 gap-3">
              {techStack.map((tech) => (
                <div key={tech.name} className="p-3 rounded-xl border text-center transition-all duration-200 cursor-default"
                  style={{ borderColor: "rgba(124,58,237,0.15)", background: "rgba(30,27,75,0.35)" }}
                  onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.borderColor = "rgba(124,58,237,0.4)"; (e.currentTarget as HTMLElement).style.background = "rgba(30,27,75,0.6)"; }}
                  onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.borderColor = "rgba(124,58,237,0.15)"; (e.currentTarget as HTMLElement).style.background = "rgba(30,27,75,0.35)"; }}>
                  <div className="w-2 h-2 rounded-full mx-auto mb-2" style={{ background: tech.color, boxShadow: `0 0 6px ${tech.color}` }} />
                  <div className="text-xs font-bold" style={{ color: "#E2E8F0" }}>{tech.name}</div>
                  <div className="text-[10px] font-mono mt-0.5" style={{ color: "#475569" }}>{tech.sub}</div>
                </div>
              ))}
            </div>
            <div className="mt-6 p-4 rounded-xl border" style={{ borderColor: "rgba(124,58,237,0.2)", background: "rgba(30,27,75,0.4)" }}>
              <div className="text-[10px] font-mono mb-3" style={{ color: "#475569" }}>{inf.pipelineLabel}</div>
              <div className="flex items-center gap-2 flex-wrap">
                {pipelineSteps.map((step, i, arr) => (
                  <div key={step} className="flex items-center gap-2">
                    <div className="flex items-center gap-1.5">
                      <span className="w-2 h-2 rounded-full animate-pulse" style={{ background: "#22c55e", boxShadow: "0 0 6px #22c55e", animationDelay: `${i * 0.2}s` }} />
                      <span className="text-[10px] font-mono" style={{ color: "#94A3B8" }}>{step}</span>
                    </div>
                    {i < arr.length - 1 && <ChevronRight size={10} style={{ color: "#334155" }} />}
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

/* ─── Profile ─── */
function DeveloperProfile() {
  const { t } = useLang();
  const p = t.profile;

  return (
    <section id="expertise" className="relative py-28 overflow-hidden" style={{ background: "#0F172A" }}>
      <div className="absolute -right-40 top-0 w-[500px] h-[500px] rounded-full opacity-[0.07] pointer-events-none" style={{ background: "radial-gradient(circle, #7C3AED 0%, transparent 70%)" }} />
      <div className="relative max-w-7xl mx-auto px-6">
        <div className="grid lg:grid-cols-5 gap-12 items-center">
          <div className="lg:col-span-2">
            <div className="relative inline-block">
              <div className="w-48 h-48 rounded-3xl flex items-center justify-center" style={{ background: "linear-gradient(135deg, #1E1B4B, #2E1065)", border: "1px solid rgba(124,58,237,0.3)", boxShadow: "0 0 60px rgba(124,58,237,0.2)" }}>
                <NTLogo size={72} />
              </div>
              <div className="absolute -bottom-3 -right-3 px-3 py-1.5 rounded-full text-xs font-mono font-semibold border" style={{ background: "rgba(34,197,94,0.1)", borderColor: "rgba(34,197,94,0.3)", color: "#22c55e" }}>
                <span className="inline-block w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse mr-1.5" />
                Staff Engineer
              </div>
            </div>
            <div className="mt-8">
              <div className="text-2xl font-bold" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>NT Sistemas Web</div>
              <div className="text-sm font-mono mt-1" style={{ color: "#7C3AED" }}>{p.role}</div>
              <p className="text-sm mt-4 leading-relaxed" style={{ color: "#64748B" }}>{p.pitch}</p>
            </div>
          </div>

          <div className="lg:col-span-3">
            <PillBadge><Shield size={11} /> {p.badge}</PillBadge>
            <h2 className="mt-5 text-3xl lg:text-4xl font-extrabold leading-tight" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>
              {p.heading1}<br />
              <span style={{ background: "linear-gradient(135deg, #7C3AED, #A78BFA)", WebkitBackgroundClip: "text", WebkitTextFillColor: "transparent" }}>{p.heading2}</span>
            </h2>
            <div className="mt-8 space-y-4">
              {p.points.map((point) => (
                <div key={point} className="flex items-start gap-3">
                  <CheckCircle2 size={17} className="shrink-0 mt-0.5" style={{ color: "#7C3AED" }} />
                  <span className="text-sm leading-relaxed" style={{ color: "#94A3B8" }}>{point}</span>
                </div>
              ))}
            </div>
            <div className="mt-10 flex gap-4 flex-wrap">
              {[
                { label: p.lang, value: "TypeScript · Java · Go · Python · Rust" },
                { label: p.fw, value: "React · Next.js · Spring · FastAPI · NestJS" },
                { label: p.db, value: "PostgreSQL · MongoDB · Redis · Kafka" },
              ].map((item) => (
                <div key={item.label} className="flex-1 min-w-[200px] p-4 rounded-xl border" style={{ borderColor: "rgba(124,58,237,0.18)", background: "rgba(30,27,75,0.3)" }}>
                  <div className="text-[10px] font-mono mb-1.5" style={{ color: "#7C3AED" }}>{item.label}</div>
                  <div className="text-xs leading-relaxed" style={{ color: "#94A3B8" }}>{item.value}</div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

/* ─── Contact ─── */
function ContactForm() {
  const { t } = useLang();
  const c = t.contact;

  const [selected, setSelected] = useState<string[]>([]);
  const [step, setStep] = useState(1);
  const [form, setForm] = useState({ name: "", email: "", company: "", stack: "", message: "", budget: "" });
  const [submitted, setSubmitted] = useState(false);

  const toggle = (id: string) => setSelected((prev) => prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]);

  const handleSubmit = (e: React.FormEvent) => { e.preventDefault(); setSubmitted(true); };

  const inputStyle = { borderColor: "rgba(124,58,237,0.25)", background: "rgba(15,23,42,0.6)", color: "#E2E8F0" };

  return (
    <section id="contact" className="relative py-28 overflow-hidden" style={{ background: "#080E1C" }}>
      <div className="absolute -left-40 bottom-0 w-[500px] h-[500px] rounded-full opacity-[0.08] pointer-events-none" style={{ background: "radial-gradient(circle, #7C3AED 0%, transparent 70%)" }} />
      <div className="relative max-w-3xl mx-auto px-6">
        <div className="text-center mb-12">
          <PillBadge><ExternalLink size={11} /> {c.badge}</PillBadge>
          <h2 className="mt-5 text-4xl lg:text-5xl font-extrabold leading-tight" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>
            {c.heading1}{" "}
            <span style={{ background: "linear-gradient(135deg, #7C3AED, #A78BFA)", WebkitBackgroundClip: "text", WebkitTextFillColor: "transparent" }}>{c.heading2}</span>
          </h2>
          <p className="mt-3 text-sm" style={{ color: "#64748B" }}>{c.sub}</p>
        </div>

        {submitted ? (
          <div className="text-center py-16 rounded-2xl border" style={{ borderColor: "rgba(124,58,237,0.25)", background: "rgba(30,27,75,0.4)" }}>
            <div className="text-5xl mb-4">✓</div>
            <div className="text-xl font-bold mb-2" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#A78BFA" }}>{c.successTitle}</div>
            <p className="text-sm" style={{ color: "#64748B" }}>{c.successSub}</p>
          </div>
        ) : (
          <div className="rounded-2xl border overflow-hidden" style={{ borderColor: "rgba(124,58,237,0.25)", background: "rgba(30,27,75,0.4)" }}>
            <div className="flex border-b" style={{ borderColor: "rgba(124,58,237,0.15)" }}>
              {[1, 2].map((s) => (
                <button key={s} onClick={() => setStep(s)} className="flex-1 py-4 text-sm font-mono font-medium transition-colors duration-150"
                  style={{ color: step === s ? "#A78BFA" : "#475569", borderBottom: step === s ? "2px solid #7C3AED" : "2px solid transparent", background: "transparent" }}>
                  {s === 1 ? c.step1 : c.step2}
                </button>
              ))}
            </div>

            <form onSubmit={handleSubmit} className="p-8">
              {step === 1 && (
                <div>
                  <p className="text-sm font-mono mb-5" style={{ color: "#64748B" }}>{c.selectLabel}</p>
                  <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    {c.painPoints.map((pp) => (
                      <button key={pp.id} type="button" onClick={() => toggle(pp.id)} className="p-4 rounded-xl border text-left transition-all duration-150"
                        style={{ borderColor: selected.includes(pp.id) ? "rgba(124,58,237,0.6)" : "rgba(124,58,237,0.18)", background: selected.includes(pp.id) ? "rgba(124,58,237,0.15)" : "rgba(30,27,75,0.3)" }}>
                        <div className="text-xl mb-2">{pp.icon}</div>
                        <div className="text-xs font-semibold leading-tight" style={{ color: selected.includes(pp.id) ? "#A78BFA" : "#94A3B8" }}>{pp.label}</div>
                      </button>
                    ))}
                  </div>
                  <button type="button" onClick={() => setStep(2)} disabled={selected.length === 0}
                    className="mt-8 w-full py-3.5 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 transition-all duration-200 disabled:opacity-40"
                    style={{ background: "linear-gradient(135deg, #7C3AED, #6D28D9)", color: "#fff", boxShadow: selected.length > 0 ? "0 0 25px rgba(124,58,237,0.4)" : "none" }}>
                    {c.continue} <ArrowRight size={15} />
                  </button>
                </div>
              )}

              {step === 2 && (
                <div className="space-y-4">
                  <div className="grid sm:grid-cols-2 gap-4">
                    {[
                      { key: "name", label: c.fields.name, ph: c.fields.namePh, type: "text" },
                      { key: "email", label: c.fields.email, ph: c.fields.emailPh, type: "email" },
                      { key: "company", label: c.fields.company, ph: c.fields.companyPh, type: "text" },
                      { key: "budget", label: c.fields.budget, ph: c.fields.budgetPh, type: "text" },
                    ].map((f) => (
                      <div key={f.key}>
                        <label className="block text-xs font-mono mb-1.5" style={{ color: "#64748B" }}>{f.label}</label>
                        <input type={f.type} placeholder={f.ph} value={form[f.key as keyof typeof form]}
                          onChange={(e) => setForm({ ...form, [f.key]: e.target.value })}
                          className="w-full px-4 py-2.5 rounded-xl text-sm border outline-none transition-colors" style={inputStyle}
                          onFocus={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.6)"; }}
                          onBlur={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.25)"; }} />
                      </div>
                    ))}
                  </div>
                  <div>
                    <label className="block text-xs font-mono mb-1.5" style={{ color: "#64748B" }}>{c.fields.stack}</label>
                    <input type="text" placeholder={c.fields.stackPh} value={form.stack} onChange={(e) => setForm({ ...form, stack: e.target.value })}
                      className="w-full px-4 py-2.5 rounded-xl text-sm border outline-none transition-colors" style={inputStyle}
                      onFocus={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.6)"; }}
                      onBlur={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.25)"; }} />
                  </div>
                  <div>
                    <label className="block text-xs font-mono mb-1.5" style={{ color: "#64748B" }}>{c.fields.message}</label>
                    <textarea rows={5} placeholder={c.fields.messagePh} value={form.message} onChange={(e) => setForm({ ...form, message: e.target.value })}
                      className="w-full px-4 py-2.5 rounded-xl text-sm border outline-none transition-colors resize-none" style={inputStyle}
                      onFocus={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.6)"; }}
                      onBlur={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.25)"; }} />
                  </div>
                  <div className="flex gap-3 pt-2">
                    <button type="button" onClick={() => setStep(1)} className="px-5 py-3 rounded-xl text-sm font-medium border transition-colors"
                      style={{ borderColor: "rgba(124,58,237,0.25)", color: "#64748B", background: "transparent" }}>
                      {c.back}
                    </button>
                    <button type="submit" className="flex-1 py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 transition-all duration-200"
                      style={{ background: "linear-gradient(135deg, #7C3AED, #6D28D9)", color: "#fff", boxShadow: "0 0 25px rgba(124,58,237,0.4)" }}
                      onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.boxShadow = "0 0 40px rgba(124,58,237,0.65)"; }}
                      onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.boxShadow = "0 0 25px rgba(124,58,237,0.4)"; }}>
                      {c.submit} <ArrowRight size={15} />
                    </button>
                  </div>
                </div>
              )}
            </form>
          </div>
        )}
      </div>
    </section>
  );
}

/* ─── Footer ─── */
function Footer() {
  const { t } = useLang();
  return (
    <footer className="border-t py-10" style={{ borderColor: "rgba(124,58,237,0.15)", background: "#080E1C" }}>
      <div className="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div className="flex items-center gap-3">
          <NTLogo size={26} />
          <span className="text-sm font-semibold" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#475569" }}>NT Sistemas Web</span>
        </div>
        <div className="text-xs font-mono" style={{ color: "#334155" }}>{t.footer.copy}</div>
        <div className="flex gap-4">
          {["GitHub", "LinkedIn", "Email"].map((l) => (
            <a key={l} href="#" className="text-xs font-mono transition-colors" style={{ color: "#475569" }}
              onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.color = "#7C3AED"; }}
              onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.color = "#475569"; }}>
              {l}
            </a>
          ))}
        </div>
      </div>
    </footer>
  );
}

/* ─── Root ─── */
export default function App() {
  const [lang, setLang] = useState<Lang>("pt");
  const t = translations[lang];

  return (
    <LangContext.Provider value={{ lang, t }}>
      <div className="min-h-screen" style={{ fontFamily: "'Inter', sans-serif", background: "#0F172A" }}>
        <Navbar lang={lang} setLang={setLang} />
        <Hero />
        <Services />
        <Infrastructure />
        <DeveloperProfile />
        <ContactForm />
        <Footer />
      </div>
    </LangContext.Provider>
  );
}
