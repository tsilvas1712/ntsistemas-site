import { useState } from "react";
import { useNavigate } from "react-router";
import { Lock, Eye, EyeOff, Terminal, AlertCircle } from "lucide-react";

function NTLogo({ size = 36 }: { size?: number }) {
  return (
    <svg width={size} height={size} viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="logoGradL" x1="0" y1="0" x2="36" y2="36" gradientUnits="userSpaceOnUse">
          <stop stopColor="#7C3AED" /><stop offset="1" stopColor="#A78BFA" />
        </linearGradient>
      </defs>
      <path d="M6 4 L2 4 L2 32 L6 32" stroke="url(#logoGradL)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <path d="M30 4 L34 4 L34 32 L30 32" stroke="url(#logoGradL)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <path d="M9 26 L9 10 L16 22 L16 10" stroke="url(#logoGradL)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
      <path d="M19 10 L27 10 M23 10 L23 26" stroke="url(#logoGradL)" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
    </svg>
  );
}

export default function AdminLogin() {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPw, setShowPw] = useState(false);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setLoading(true);
    setTimeout(() => {
      if (email === "admin@ntsistemas.com" && password === "nt@admin2024") {
        sessionStorage.setItem("nt_admin", "1");
        navigate("/admin/dashboard");
      } else {
        setError("Credenciais inválidas. Tente novamente.");
        setLoading(false);
      }
    }, 800);
  };

  const inputBase: React.CSSProperties = {
    background: "rgba(15,23,42,0.7)",
    borderColor: "rgba(124,58,237,0.25)",
    color: "#E2E8F0",
  };

  return (
    <div className="min-h-screen flex items-center justify-center relative overflow-hidden"
      style={{ background: "#0F172A", fontFamily: "'Inter', sans-serif" }}>

      {/* Background glows */}
      <div className="absolute inset-0 pointer-events-none" aria-hidden>
        <div className="absolute -top-40 -right-40 w-[600px] h-[600px] rounded-full opacity-[0.1]"
          style={{ background: "radial-gradient(circle, #7C3AED 0%, transparent 70%)" }} />
        <div className="absolute -bottom-40 -left-40 w-[500px] h-[500px] rounded-full opacity-[0.08]"
          style={{ background: "radial-gradient(circle, #6D28D9 0%, transparent 70%)" }} />
        <div className="absolute inset-0 opacity-[0.025]"
          style={{ backgroundImage: "linear-gradient(rgba(124,58,237,1) 1px, transparent 1px), linear-gradient(90deg, rgba(124,58,237,1) 1px, transparent 1px)", backgroundSize: "60px 60px" }} />
      </div>

      <div className="relative w-full max-w-md px-6">
        {/* Card */}
        <div className="rounded-2xl border p-8"
          style={{ borderColor: "rgba(124,58,237,0.25)", background: "rgba(30,27,75,0.55)", backdropFilter: "blur(20px)", boxShadow: "0 0 60px rgba(124,58,237,0.12), inset 0 1px 0 rgba(255,255,255,0.04)" }}>

          {/* Logo */}
          <div className="flex flex-col items-center mb-8">
            <div className="flex items-center gap-3">
              <NTLogo size={40} />
              <div>
                <div className="font-bold text-base" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>NT Sistemas Web</div>
                <div className="text-[10px] font-mono" style={{ color: "#7C3AED", letterSpacing: "0.08em" }}>ELITE ENGINEERING</div>
              </div>
            </div>
            <div className="mt-3 flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono border"
              style={{ background: "rgba(124,58,237,0.1)", borderColor: "rgba(124,58,237,0.3)", color: "#A78BFA" }}>
              <Terminal size={10} /> Painel Administrativo
            </div>
          </div>

          <h1 className="text-xl font-bold text-center mb-1" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif", color: "#F1F5F9" }}>
            Acesso Restrito
          </h1>
          <p className="text-sm text-center mb-8" style={{ color: "#64748B" }}>
            Autentique-se para acessar o painel.
          </p>

          <form onSubmit={handleSubmit} className="space-y-4">
            {/* Email */}
            <div>
              <label className="block text-xs font-mono mb-1.5" style={{ color: "#64748B" }}>E-mail</label>
              <input type="email" value={email} onChange={(e) => setEmail(e.target.value)}
                placeholder="admin@ntsistemas.com" required
                className="w-full px-4 py-3 rounded-xl text-sm border outline-none transition-colors"
                style={inputBase}
                onFocus={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.6)"; }}
                onBlur={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.25)"; }} />
            </div>

            {/* Password */}
            <div>
              <label className="block text-xs font-mono mb-1.5" style={{ color: "#64748B" }}>Senha</label>
              <div className="relative">
                <input type={showPw ? "text" : "password"} value={password} onChange={(e) => setPassword(e.target.value)}
                  placeholder="••••••••••" required
                  className="w-full px-4 py-3 pr-11 rounded-xl text-sm border outline-none transition-colors"
                  style={inputBase}
                  onFocus={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.6)"; }}
                  onBlur={(e) => { (e.target as HTMLElement).style.borderColor = "rgba(124,58,237,0.25)"; }} />
                <button type="button" onClick={() => setShowPw(!showPw)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded transition-colors"
                  style={{ color: "#475569" }}
                  onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.color = "#A78BFA"; }}
                  onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.color = "#475569"; }}>
                  {showPw ? <EyeOff size={16} /> : <Eye size={16} />}
                </button>
              </div>
            </div>

            {/* Error */}
            {error && (
              <div className="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm"
                style={{ background: "rgba(239,68,68,0.1)", border: "1px solid rgba(239,68,68,0.25)", color: "#fca5a5" }}>
                <AlertCircle size={14} />
                {error}
              </div>
            )}

            {/* Submit */}
            <button type="submit" disabled={loading}
              className="w-full py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 transition-all duration-200 mt-2 disabled:opacity-60"
              style={{ background: "linear-gradient(135deg, #7C3AED, #6D28D9)", color: "#fff", boxShadow: "0 0 25px rgba(124,58,237,0.4)" }}
              onMouseEnter={(e) => { if (!loading) (e.currentTarget as HTMLElement).style.boxShadow = "0 0 40px rgba(124,58,237,0.65)"; }}
              onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.boxShadow = "0 0 25px rgba(124,58,237,0.4)"; }}>
              {loading ? (
                <span className="flex items-center gap-2">
                  <span className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                  Autenticando...
                </span>
              ) : (
                <span className="flex items-center gap-2"><Lock size={15} />Entrar no Painel</span>
              )}
            </button>
          </form>

          {/* Demo hint */}
          <div className="mt-6 p-3 rounded-xl" style={{ background: "rgba(124,58,237,0.07)", border: "1px solid rgba(124,58,237,0.15)" }}>
            <div className="text-[10px] font-mono mb-1" style={{ color: "#475569" }}>// DEMO CREDENTIALS</div>
            <div className="text-xs font-mono" style={{ color: "#64748B" }}>
              admin@ntsistemas.com<br />nt@admin2024
            </div>
          </div>
        </div>

        {/* Back link */}
        <a href="/" className="block text-center mt-6 text-xs font-mono transition-colors" style={{ color: "#475569" }}
          onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.color = "#A78BFA"; }}
          onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.color = "#475569"; }}>
          ← Voltar ao site
        </a>
      </div>
    </div>
  );
}
