import { useState, useEffect } from "react";
import { useNavigate } from "react-router";
import {
  LayoutDashboard, Inbox, Settings, LogOut, Bell, Search,
  TrendingUp, Users, Clock, CheckCircle2, ChevronRight, Filter,
  ExternalLink, MoreHorizontal, Mail, Building2, Tag, Calendar,
  X, Eye, Trash2, BarChart3, Globe, Menu,
} from "lucide-react";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, LineChart, Line, CartesianGrid } from "recharts";

/* ─── Logo ─── */
function NTLogo({ size = 26 }: { size?: number }) {
  return (
    <svg width={size} height={size} viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="logoGradD" x1="0" y1="0" x2="36" y2="36" gradientUnits="userSpaceOnUse">
          <stop stopColor="#7C3AED" /><stop offset="1" stopColor="#A78BFA" />
        </linearGradient>
      </defs>
      <path d="M6 4 L2 4 L2 32 L6 32" stroke="url(#logoGradD)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <path d="M30 4 L34 4 L34 32 L30 32" stroke="url(#logoGradD)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <path d="M9 26 L9 10 L16 22 L16 10" stroke="url(#logoGradD)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <path d="M19 10 L27 10 M23 10 L23 26" stroke="url(#logoGradD)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
    </svg>
  );
}

/* ─── Mock data ─── */
type Lead = {
  id: string;
  name: string;
  email: string;
  company: string;
  painPoints: string[];
  stack: string;
  budget: string;
  message: string;
  date: string;
  status: "new" | "in_progress" | "replied" | "closed";
};

const mockLeads: Lead[] = [
  { id: "L001", name: "Rodrigo Almeida", email: "rodrigo@fintech-xp.com.br", company: "FinTech XP", painPoints: ["Legacy System Rescue", "Architectural Redesign"], stack: "Java 8, Spring MVC, Oracle DB, JBoss", budget: "R$ 50k–100k", message: "Sistema de pagamentos com 8 anos sem refatoração, acumulando 40k+ linhas de código legado. Precisamos migrar para microserviços.", date: "2024-06-28", status: "new" },
  { id: "L002", name: "Camila Torres", email: "camila@logistica-br.com", company: "LogísticaBR", painPoints: ["Bug Forensics", "Performance Tuning"], stack: "Node.js, MongoDB, AWS Lambda", budget: "R$ 20k–50k", message: "API de rastreamento de fretes caindo em produção em dias de pico. Nenhuma stack trace útil. Precisamos de diagnóstico urgente.", date: "2024-06-26", status: "in_progress" },
  { id: "L003", name: "Felipe Nascimento", email: "felipe@saasplatform.io", company: "SaaS Platform", painPoints: ["MVP Greenfield"], stack: "A definir", budget: "R$ 30k–60k", message: "Quero construir um SaaS B2B para gestão de obras. Tenho o produto mapeado, preciso de uma equipe técnica para executar.", date: "2024-06-24", status: "replied" },
  { id: "L004", name: "Ana Paula Ferreira", email: "ana@healthtech.med.br", company: "HealthTech Med", painPoints: ["Security Hardening", "DevOps"], stack: "Python Django, PostgreSQL, Heroku", budget: "R$ 15k–30k", message: "Aplicação médica que precisa de compliance LGPD completo e migração do Heroku para infraestrutura controlada.", date: "2024-06-22", status: "new" },
  { id: "L005", name: "Gustavo Moraes", email: "gmoraes@ecommerce360.com", company: "eCommerce 360", painPoints: ["Legacy System Rescue", "Performance Tuning"], stack: "PHP 5.6, CodeIgniter, MySQL, cPanel", budget: "R$ 80k–150k", message: "E-commerce com 500k produtos, PHP 5.6 sem suporte há anos. Loja cai todo Black Friday. Precisamos modernizar com urgência.", date: "2024-06-20", status: "in_progress" },
  { id: "L006", name: "Mariana Souza", email: "mariana@edtech.com.br", company: "EdTech Brasil", painPoints: ["MVP Greenfield", "DevOps"], stack: "React, deseja Next.js, precisa de backend", budget: "R$ 40k–70k", message: "Plataforma de cursos online com gamificação. Protótipo no Figma pronto. Preciso de time full-stack para construir do zero.", date: "2024-06-18", status: "closed" },
];

const chartData = [
  { month: "Jan", leads: 2 }, { month: "Fev", leads: 4 }, { month: "Mar", leads: 3 },
  { month: "Abr", leads: 7 }, { month: "Mai", leads: 5 }, { month: "Jun", leads: 9 },
];

const conversionData = [
  { month: "Jan", taxa: 40 }, { month: "Fev", taxa: 55 }, { month: "Mar", taxa: 48 },
  { month: "Abr", taxa: 62 }, { month: "Mai", taxa: 58 }, { month: "Jun", taxa: 71 },
];

const statusConfig = {
  new: { label: "Novo", color: "#7C3AED", bg: "rgba(124,58,237,0.15)", border: "rgba(124,58,237,0.35)" },
  in_progress: { label: "Em Andamento", color: "#f59e0b", bg: "rgba(245,158,11,0.12)", border: "rgba(245,158,11,0.3)" },
  replied: { label: "Respondido", color: "#22c55e", bg: "rgba(34,197,94,0.12)", border: "rgba(34,197,94,0.3)" },
  closed: { label: "Encerrado", color: "#475569", bg: "rgba(71,85,105,0.15)", border: "rgba(71,85,105,0.3)" },
};

/* ─── Shared styles ─── */
const cardStyle: React.CSSProperties = { borderColor: "rgba(124,58,237,0.18)", background: "rgba(30,27,75,0.4)" };
const mutedText = "#64748B";
const bodyText = "#94A3B8";

/* ─── Status badge ─── */
function StatusBadge({ status }: { status: Lead["status"] }) {
  const cfg = statusConfig[status];
  return (
    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold border"
      style={{ color: cfg.color, background: cfg.bg, borderColor: cfg.border }}>
      {cfg.label}
    </span>
  );
}

/* ─── Lead detail modal ─── */
function LeadModal({ lead, onClose, onStatusChange }: { lead: Lead; onClose: () => void; onStatusChange: (id: string, s: Lead["status"]) => void }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4" style={{ background: "rgba(0,0,0,0.7)", backdropFilter: "blur(6px)" }}>
      <div className="w-full max-w-2xl rounded-2xl border overflow-hidden"
        style={{ borderColor: "rgba(124,58,237,0.3)", background: "#0F172A", boxShadow: "0 0 60px rgba(124,58,237,0.2)" }}>
        {/* Header */}
        <div className="flex items-center justify-between px-6 py-4 border-b" style={{ borderColor: "rgba(124,58,237,0.15)" }}>
          <div>
            <div className="font-bold text-base" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>{lead.name}</div>
            <div className="text-xs font-mono mt-0.5" style={{ color: mutedText }}>{lead.id} · {lead.date}</div>
          </div>
          <div className="flex items-center gap-3">
            <StatusBadge status={lead.status} />
            <button onClick={onClose} className="p-2 rounded-lg transition-colors" style={{ color: mutedText }}
              onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.color = "#A78BFA"; }}
              onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.color = mutedText; }}>
              <X size={18} />
            </button>
          </div>
        </div>

        <div className="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
          {/* Contact info */}
          <div className="grid grid-cols-2 gap-3">
            {[
              { icon: Mail, label: "E-mail", value: lead.email },
              { icon: Building2, label: "Empresa", value: lead.company },
              { icon: Tag, label: "Orçamento", value: lead.budget },
              { icon: Calendar, label: "Data", value: lead.date },
            ].map((f) => (
              <div key={f.label} className="p-3 rounded-xl border" style={cardStyle}>
                <div className="flex items-center gap-1.5 mb-1">
                  <f.icon size={11} style={{ color: "#7C3AED" }} />
                  <span className="text-[10px] font-mono" style={{ color: mutedText }}>{f.label}</span>
                </div>
                <div className="text-sm" style={{ color: "#CBD5E1" }}>{f.value}</div>
              </div>
            ))}
          </div>

          {/* Pain points */}
          <div>
            <div className="text-xs font-mono mb-2" style={{ color: mutedText }}>PROBLEMAS SELECIONADOS</div>
            <div className="flex flex-wrap gap-2">
              {lead.painPoints.map((p) => (
                <span key={p} className="text-xs px-2.5 py-1 rounded-full border font-mono"
                  style={{ color: "#A78BFA", background: "rgba(124,58,237,0.1)", borderColor: "rgba(124,58,237,0.25)" }}>{p}</span>
              ))}
            </div>
          </div>

          {/* Stack */}
          <div className="p-3 rounded-xl border" style={cardStyle}>
            <div className="text-[10px] font-mono mb-1.5" style={{ color: mutedText }}>STACK ATUAL</div>
            <div className="text-sm font-mono" style={{ color: bodyText }}>{lead.stack}</div>
          </div>

          {/* Message */}
          <div className="p-4 rounded-xl border" style={cardStyle}>
            <div className="text-[10px] font-mono mb-2" style={{ color: mutedText }}>MENSAGEM</div>
            <p className="text-sm leading-relaxed" style={{ color: bodyText }}>{lead.message}</p>
          </div>

          {/* Actions */}
          <div className="flex gap-3 pt-2">
            <a href={`mailto:${lead.email}`} className="flex-1 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 border transition-colors"
              style={{ borderColor: "rgba(124,58,237,0.3)", color: "#A78BFA", background: "rgba(124,58,237,0.08)" }}>
              <Mail size={14} />Responder
            </a>
            {lead.status !== "in_progress" && (
              <button onClick={() => onStatusChange(lead.id, "in_progress")} className="flex-1 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-all"
                style={{ background: "linear-gradient(135deg, #7C3AED, #6D28D9)", color: "#fff", boxShadow: "0 0 20px rgba(124,58,237,0.35)" }}>
                <ChevronRight size={14} />Mover: Em Andamento
              </button>
            )}
            {lead.status !== "closed" && (
              <button onClick={() => onStatusChange(lead.id, "closed")} className="px-4 py-2.5 rounded-xl text-sm font-medium border transition-colors"
                style={{ borderColor: "rgba(71,85,105,0.3)", color: mutedText, background: "transparent" }}>
                Encerrar
              </button>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

/* ─── Sidebar ─── */
type Section = "overview" | "leads" | "settings";

function Sidebar({ active, setActive, onLogout, collapsed, setCollapsed }: {
  active: Section; setActive: (s: Section) => void; onLogout: () => void; collapsed: boolean; setCollapsed: (v: boolean) => void;
}) {
  const items: { id: Section; icon: typeof LayoutDashboard; label: string }[] = [
    { id: "overview", icon: LayoutDashboard, label: "Visão Geral" },
    { id: "leads", icon: Inbox, label: "Leads / Briefs" },
    { id: "settings", icon: Settings, label: "Configurações" },
  ];

  return (
    <aside className="flex flex-col border-r transition-all duration-300"
      style={{ width: collapsed ? 64 : 220, borderColor: "rgba(124,58,237,0.15)", background: "#080E1C", minHeight: "100vh", flexShrink: 0 }}>
      {/* Logo area */}
      <div className="flex items-center justify-between px-4 h-16 border-b" style={{ borderColor: "rgba(124,58,237,0.15)" }}>
        {!collapsed && (
          <div className="flex items-center gap-2">
            <NTLogo size={24} />
            <span className="text-xs font-semibold" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#94A3B8" }}>NT Sistemas Web</span>
          </div>
        )}
        <button onClick={() => setCollapsed(!collapsed)} className="p-1.5 rounded-lg ml-auto transition-colors" style={{ color: mutedText }}
          onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.color = "#A78BFA"; }}
          onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.color = mutedText; }}>
          <Menu size={16} />
        </button>
      </div>

      {/* Nav */}
      <nav className="flex-1 px-2 py-4 space-y-1">
        {items.map((item) => (
          <button key={item.id} onClick={() => setActive(item.id)}
            className="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 text-left"
            style={{
              background: active === item.id ? "rgba(124,58,237,0.2)" : "transparent",
              color: active === item.id ? "#A78BFA" : bodyText,
              borderLeft: active === item.id ? "2px solid #7C3AED" : "2px solid transparent",
            }}
            title={collapsed ? item.label : undefined}>
            <item.icon size={17} className="shrink-0" />
            {!collapsed && <span className="text-sm font-medium">{item.label}</span>}
          </button>
        ))}
      </nav>

      {/* Logout */}
      <div className="px-2 pb-4 border-t pt-4" style={{ borderColor: "rgba(124,58,237,0.1)" }}>
        <button onClick={onLogout}
          className="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors"
          style={{ color: mutedText }}
          onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.color = "#ef4444"; (e.currentTarget as HTMLElement).style.background = "rgba(239,68,68,0.08)"; }}
          onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.color = mutedText; (e.currentTarget as HTMLElement).style.background = "transparent"; }}
          title={collapsed ? "Sair" : undefined}>
          <LogOut size={17} className="shrink-0" />
          {!collapsed && <span className="text-sm font-medium">Sair</span>}
        </button>
      </div>
    </aside>
  );
}

/* ─── Overview section ─── */
function Overview({ leads }: { leads: Lead[] }) {
  const newLeads = leads.filter((l) => l.status === "new").length;
  const inProgress = leads.filter((l) => l.status === "in_progress").length;
  const replied = leads.filter((l) => l.status === "replied").length;

  const metrics = [
    { label: "Total de Leads", value: leads.length.toString(), sub: "+3 este mês", icon: Users, color: "#7C3AED" },
    { label: "Novos", value: newLeads.toString(), sub: "Aguardando resposta", icon: Bell, color: "#A78BFA" },
    { label: "Em Andamento", value: inProgress.toString(), sub: "Em negociação", icon: Clock, color: "#f59e0b" },
    { label: "Respondidos", value: replied.toString(), sub: "Taxa 71%", icon: CheckCircle2, color: "#22c55e" },
  ];

  const customTooltipStyle = { background: "#0F172A", border: "1px solid rgba(124,58,237,0.3)", borderRadius: 8, color: "#E2E8F0", fontSize: 12 };

  return (
    <div className="space-y-6">
      {/* Metrics */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {metrics.map((m) => (
          <div key={m.label} className="p-5 rounded-2xl border" style={cardStyle}>
            <div className="flex items-start justify-between mb-3">
              <div className="w-9 h-9 rounded-xl flex items-center justify-center"
                style={{ background: `${m.color}22`, border: `1px solid ${m.color}44` }}>
                <m.icon size={16} style={{ color: m.color }} />
              </div>
              <TrendingUp size={12} style={{ color: "#22c55e" }} />
            </div>
            <div className="text-2xl font-extrabold" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>{m.value}</div>
            <div className="text-xs font-medium mt-0.5" style={{ color: bodyText }}>{m.label}</div>
            <div className="text-[10px] font-mono mt-1" style={{ color: mutedText }}>{m.sub}</div>
          </div>
        ))}
      </div>

      {/* Charts */}
      <div className="grid lg:grid-cols-2 gap-4">
        <div className="p-5 rounded-2xl border" style={cardStyle}>
          <div className="flex items-center justify-between mb-5">
            <div>
              <div className="text-sm font-bold" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#E2E8F0" }}>Leads por Mês</div>
              <div className="text-xs font-mono mt-0.5" style={{ color: mutedText }}>Jan–Jun 2024</div>
            </div>
            <BarChart3 size={16} style={{ color: "#7C3AED" }} />
          </div>
          <ResponsiveContainer width="100%" height={180}>
            <BarChart data={chartData} barSize={28}>
              <XAxis dataKey="month" tick={{ fill: "#475569", fontSize: 11, fontFamily: "JetBrains Mono" }} axisLine={false} tickLine={false} />
              <YAxis tick={{ fill: "#475569", fontSize: 11 }} axisLine={false} tickLine={false} />
              <Tooltip contentStyle={customTooltipStyle} cursor={{ fill: "rgba(124,58,237,0.06)" }} />
              <Bar dataKey="leads" fill="url(#barGrad)" radius={[6, 6, 0, 0]} />
              <defs>
                <linearGradient id="barGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stopColor="#7C3AED" />
                  <stop offset="100%" stopColor="#6D28D9" />
                </linearGradient>
              </defs>
            </BarChart>
          </ResponsiveContainer>
        </div>

        <div className="p-5 rounded-2xl border" style={cardStyle}>
          <div className="flex items-center justify-between mb-5">
            <div>
              <div className="text-sm font-bold" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#E2E8F0" }}>Taxa de Conversão</div>
              <div className="text-xs font-mono mt-0.5" style={{ color: mutedText }}>% leads respondidos</div>
            </div>
            <Globe size={16} style={{ color: "#A78BFA" }} />
          </div>
          <ResponsiveContainer width="100%" height={180}>
            <LineChart data={conversionData}>
              <CartesianGrid strokeDasharray="3 3" stroke="rgba(124,58,237,0.1)" />
              <XAxis dataKey="month" tick={{ fill: "#475569", fontSize: 11, fontFamily: "JetBrains Mono" }} axisLine={false} tickLine={false} />
              <YAxis tick={{ fill: "#475569", fontSize: 11 }} axisLine={false} tickLine={false} unit="%" />
              <Tooltip contentStyle={customTooltipStyle} formatter={(v) => [`${v}%`, "Taxa"]} />
              <Line type="monotone" dataKey="taxa" stroke="#A78BFA" strokeWidth={2.5} dot={{ fill: "#7C3AED", strokeWidth: 0, r: 4 }} activeDot={{ r: 6, fill: "#A78BFA" }} />
            </LineChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Recent leads preview */}
      <div className="p-5 rounded-2xl border" style={cardStyle}>
        <div className="flex items-center justify-between mb-4">
          <div className="text-sm font-bold" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#E2E8F0" }}>Leads Recentes</div>
        </div>
        <div className="space-y-2">
          {leads.slice(0, 4).map((lead) => (
            <div key={lead.id} className="flex items-center gap-4 p-3 rounded-xl border transition-colors"
              style={{ borderColor: "rgba(124,58,237,0.1)", background: "rgba(15,23,42,0.4)" }}>
              <div className="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                style={{ background: "rgba(124,58,237,0.2)", color: "#A78BFA" }}>
                {lead.name.charAt(0)}
              </div>
              <div className="flex-1 min-w-0">
                <div className="text-sm font-medium truncate" style={{ color: "#E2E8F0" }}>{lead.name}</div>
                <div className="text-xs font-mono truncate" style={{ color: mutedText }}>{lead.company} · {lead.date}</div>
              </div>
              <StatusBadge status={lead.status} />
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

/* ─── Leads section ─── */
function LeadsSection({ leads, onStatusChange }: { leads: Lead[]; onStatusChange: (id: string, s: Lead["status"]) => void }) {
  const [search, setSearch] = useState("");
  const [filter, setFilter] = useState<Lead["status"] | "all">("all");
  const [selected, setSelected] = useState<Lead | null>(null);

  const filtered = leads.filter((l) => {
    const matchSearch = l.name.toLowerCase().includes(search.toLowerCase()) ||
      l.company.toLowerCase().includes(search.toLowerCase()) ||
      l.email.toLowerCase().includes(search.toLowerCase());
    const matchFilter = filter === "all" || l.status === filter;
    return matchSearch && matchFilter;
  });

  return (
    <div>
      {selected && <LeadModal lead={selected} onClose={() => setSelected(null)}
        onStatusChange={(id, s) => { onStatusChange(id, s); setSelected(null); }} />}

      {/* Toolbar */}
      <div className="flex flex-col sm:flex-row gap-3 mb-5">
        <div className="relative flex-1">
          <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2" style={{ color: mutedText }} />
          <input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Buscar por nome, empresa, e-mail..."
            className="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm border outline-none"
            style={{ borderColor: "rgba(124,58,237,0.2)", background: "rgba(15,23,42,0.6)", color: "#E2E8F0" }}
            onFocus={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.5)"; }}
            onBlur={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.2)"; }} />
        </div>
        <div className="flex items-center gap-2">
          <Filter size={14} style={{ color: mutedText }} />
          {(["all", "new", "in_progress", "replied", "closed"] as const).map((f) => (
            <button key={f} onClick={() => setFilter(f)} className="px-3 py-2 rounded-lg text-xs font-mono transition-colors"
              style={{
                background: filter === f ? "rgba(124,58,237,0.25)" : "rgba(30,27,75,0.4)",
                color: filter === f ? "#A78BFA" : mutedText,
                border: filter === f ? "1px solid rgba(124,58,237,0.4)" : "1px solid rgba(124,58,237,0.1)",
              }}>
              {f === "all" ? "Todos" : statusConfig[f].label}
            </button>
          ))}
        </div>
      </div>

      {/* Count */}
      <div className="text-xs font-mono mb-3" style={{ color: mutedText }}>{filtered.length} lead{filtered.length !== 1 ? "s" : ""}</div>

      {/* Table */}
      <div className="rounded-2xl border overflow-hidden" style={{ borderColor: "rgba(124,58,237,0.18)", background: "rgba(30,27,75,0.35)" }}>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b text-left" style={{ borderColor: "rgba(124,58,237,0.12)" }}>
                {["Lead", "Empresa", "Problema", "Orçamento", "Status", "Data", ""].map((h) => (
                  <th key={h} className="px-4 py-3 text-[10px] font-mono uppercase tracking-wider" style={{ color: mutedText }}>{h}</th>
                ))}
              </tr>
            </thead>
            <tbody>
              {filtered.map((lead, i) => (
                <tr key={lead.id} className="border-b transition-colors cursor-pointer"
                  style={{ borderColor: "rgba(124,58,237,0.08)" }}
                  onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.background = "rgba(124,58,237,0.05)"; }}
                  onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.background = ""; }}
                  onClick={() => setSelected(lead)}>
                  <td className="px-4 py-3">
                    <div className="flex items-center gap-2.5">
                      <div className="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                        style={{ background: "rgba(124,58,237,0.2)", color: "#A78BFA" }}>
                        {lead.name.charAt(0)}
                      </div>
                      <div>
                        <div className="text-sm font-medium" style={{ color: "#E2E8F0" }}>{lead.name}</div>
                        <div className="text-[10px] font-mono" style={{ color: mutedText }}>{lead.email}</div>
                      </div>
                    </div>
                  </td>
                  <td className="px-4 py-3 text-sm" style={{ color: bodyText }}>{lead.company}</td>
                  <td className="px-4 py-3">
                    <div className="flex flex-wrap gap-1">
                      {lead.painPoints.slice(0, 2).map((p) => (
                        <span key={p} className="text-[9px] font-mono px-1.5 py-0.5 rounded"
                          style={{ background: "rgba(124,58,237,0.1)", color: "#7C3AED", border: "1px solid rgba(124,58,237,0.2)" }}>
                          {p.split(" ")[0]}
                        </span>
                      ))}
                    </div>
                  </td>
                  <td className="px-4 py-3 text-sm font-mono" style={{ color: bodyText }}>{lead.budget}</td>
                  <td className="px-4 py-3"><StatusBadge status={lead.status} /></td>
                  <td className="px-4 py-3 text-xs font-mono" style={{ color: mutedText }}>{lead.date}</td>
                  <td className="px-4 py-3">
                    <div className="flex items-center gap-1" onClick={(e) => e.stopPropagation()}>
                      <button onClick={() => setSelected(lead)} className="p-1.5 rounded-lg transition-colors" style={{ color: mutedText }}
                        onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.color = "#A78BFA"; }}
                        onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.color = mutedText; }}>
                        <Eye size={14} />
                      </button>
                      <a href={`mailto:${lead.email}`} className="p-1.5 rounded-lg transition-colors" style={{ color: mutedText }}
                        onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.color = "#22c55e"; }}
                        onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.color = mutedText; }}>
                        <ExternalLink size={14} />
                      </a>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        {filtered.length === 0 && (
          <div className="text-center py-12 text-sm font-mono" style={{ color: mutedText }}>Nenhum lead encontrado.</div>
        )}
      </div>
    </div>
  );
}

/* ─── Settings section ─── */
function SettingsSection() {
  const [saved, setSaved] = useState(false);

  return (
    <div className="max-w-xl space-y-5">
      <div className="p-6 rounded-2xl border" style={cardStyle}>
        <div className="text-sm font-bold mb-4" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#E2E8F0" }}>Perfil do Administrador</div>
        <div className="space-y-4">
          {[
            { label: "Nome", value: "NT Sistemas Web", type: "text" },
            { label: "E-mail", value: "admin@ntsistemas.com", type: "email" },
          ].map((f) => (
            <div key={f.label}>
              <label className="block text-xs font-mono mb-1.5" style={{ color: mutedText }}>{f.label}</label>
              <input type={f.type} defaultValue={f.value} className="w-full px-4 py-2.5 rounded-xl text-sm border outline-none"
                style={{ borderColor: "rgba(124,58,237,0.2)", background: "rgba(15,23,42,0.6)", color: "#E2E8F0" }}
                onFocus={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.5)"; }}
                onBlur={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.2)"; }} />
            </div>
          ))}
        </div>
      </div>

      <div className="p-6 rounded-2xl border" style={cardStyle}>
        <div className="text-sm font-bold mb-4" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#E2E8F0" }}>Notificações</div>
        {[
          { label: "E-mail ao receber novo lead", defaultChecked: true },
          { label: "Resumo semanal de leads", defaultChecked: true },
          { label: "Alertas de leads sem resposta (48h+)", defaultChecked: false },
        ].map((item) => (
          <label key={item.label} className="flex items-center justify-between py-3 border-b cursor-pointer" style={{ borderColor: "rgba(124,58,237,0.08)" }}>
            <span className="text-sm" style={{ color: bodyText }}>{item.label}</span>
            <input type="checkbox" defaultChecked={item.defaultChecked} className="accent-violet-600 w-4 h-4" />
          </label>
        ))}
      </div>

      <button onClick={() => { setSaved(true); setTimeout(() => setSaved(false), 2000); }}
        className="px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2"
        style={{ background: "linear-gradient(135deg, #7C3AED, #6D28D9)", color: "#fff", boxShadow: "0 0 20px rgba(124,58,237,0.35)" }}>
        {saved ? <><CheckCircle2 size={15} />Salvo!</> : "Salvar alterações"}
      </button>
    </div>
  );
}

/* ─── Dashboard Root ─── */
export default function AdminDashboard() {
  const navigate = useNavigate();
  const [active, setActive] = useState<Section>("overview");
  const [leads, setLeads] = useState<Lead[]>(mockLeads);
  const [collapsed, setCollapsed] = useState(false);

  useEffect(() => {
    if (!sessionStorage.getItem("nt_admin")) {
      navigate("/admin");
    }
  }, [navigate]);

  const handleLogout = () => {
    sessionStorage.removeItem("nt_admin");
    navigate("/admin");
  };

  const handleStatusChange = (id: string, status: Lead["status"]) => {
    setLeads((prev) => prev.map((l) => l.id === id ? { ...l, status } : l));
  };

  const sectionTitles: Record<Section, string> = {
    overview: "Visão Geral",
    leads: "Leads & Briefs",
    settings: "Configurações",
  };

  return (
    <div className="flex min-h-screen" style={{ background: "#0F172A", fontFamily: "'Inter', sans-serif" }}>
      <Sidebar active={active} setActive={setActive} onLogout={handleLogout} collapsed={collapsed} setCollapsed={setCollapsed} />

      <div className="flex-1 flex flex-col min-w-0">
        {/* Top bar */}
        <header className="h-16 flex items-center justify-between px-6 border-b shrink-0"
          style={{ borderColor: "rgba(124,58,237,0.15)", background: "rgba(8,14,28,0.8)", backdropFilter: "blur(12px)" }}>
          <div>
            <div className="text-base font-bold" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>
              {sectionTitles[active]}
            </div>
            <div className="text-[10px] font-mono" style={{ color: mutedText }}>
              NT Sistemas Web · Painel Admin
            </div>
          </div>
          <div className="flex items-center gap-3">
            <div className="relative">
              <Bell size={18} style={{ color: mutedText }} className="cursor-pointer" />
              <span className="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full text-[8px] flex items-center justify-center font-bold"
                style={{ background: "#7C3AED", color: "#fff" }}>
                {leads.filter((l) => l.status === "new").length}
              </span>
            </div>
            <div className="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
              style={{ background: "rgba(124,58,237,0.25)", color: "#A78BFA", border: "1px solid rgba(124,58,237,0.35)" }}>
              NT
            </div>
          </div>
        </header>

        {/* Content */}
        <main className="flex-1 p-6 overflow-auto">
          {active === "overview" && <Overview leads={leads} />}
          {active === "leads" && <LeadsSection leads={leads} onStatusChange={handleStatusChange} />}
          {active === "settings" && <SettingsSection />}
        </main>
      </div>
    </div>
  );
}
