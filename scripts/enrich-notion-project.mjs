#!/usr/bin/env node
/**
 * Enrich Notion project + tasks with detailed specs, ordered by Fase and Prioridade.
 *
 * Usage: NOTION_API_KEY=... node scripts/enrich-notion-project.mjs
 */

import { Client } from "@notionhq/client";

const notion = new Client({ auth: process.env.NOTION_API_KEY });
if (!process.env.NOTION_API_KEY) {
  console.error("Missing NOTION_API_KEY");
  process.exit(1);
}

const PROJECT_ID = "39226067c138811c9199e1ef26fe7128";
const TASKS_DS = "3f826067-c138-83e4-9f51-8763362c7a6d";
const SPEC_URL =
  "https://app.notion.com/p/Backend-Requirements-NT-Sistemas-Web-39226067c138810c9c97ecb645244bf8";

/** Ordem global: Fase ASC → Prioridade (Alta > Média > Baixa) → dependência lógica */
const TASKS = [
  {
    order: 1,
    name: "API base (Spring Boot) + PostgreSQL + Redis",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
Bootstrap do backend que sustenta todo o site e admin.

## O que fazer
- Criar repo/módulo API **Spring Boot 3** (Java 21, Maven/Gradle) com prefixo \`/api/v1\`
- Configurar **PostgreSQL** (Spring Data JPA / Hibernate) com migrations (Flyway/Liquibase)
- Configurar **Redis** (cache, rate limit, blacklist de refresh tokens)
- Implementar \`GET /api/v1/health\` para probes K8s
- Criar tabelas base: \`users\`, \`leads\`, \`settings\`, \`audit_log\`
- Configurar CORS para o domínio do frontend
- Variáveis de ambiente documentadas (\`.env.example\`)

## Arquivos / escopo
- Novo serviço \`api/\` ou repo separado
- \`k8s/api/\` — deployment, service, ingress
- Migrations SQL (seção 15 do spec)

## Critérios de aceite
- [ ] \`GET /health\` retorna \`{ status: "ok" }\`
- [ ] Migrations rodam em CI e local
- [ ] Redis conectado e testado
- [ ] Swagger/OpenAPI opcional mas recomendado

## Dependências
Nenhuma — **primeira task do projeto**.`,
  },
  {
    order: 2,
    name: "POST /public/leads + email notification + CAPTCHA",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
Persistir leads do formulário de contato e notificar a equipe.

## O que fazer
- Endpoint \`POST /api/v1/public/leads\`
- Validar payload: name, email, message, painPoints (1–6 IDs), lang, honeypot vazio
- Rate limit: **3 envios/hora por IP** (Redis)
- Integrar **Cloudflare Turnstile** ou reCAPTCHA (\`captchaToken\`)
- Persistir com status \`new\`
- Enviar email interno (Resend/SendGrid/SES) + confirmação ao lead
- Mapear pain points: \`legacy\`, \`bugs\`, \`arch\`, \`mvp\`, \`devops\`, \`security\`

## Endpoints
\`\`\`
POST /api/v1/public/leads
\`\`\`

## Critérios de aceite
- [ ] Lead salvo no PostgreSQL
- [ ] E-mail de notificação enviado
- [ ] CAPTCHA rejeita bots em produção
- [ ] Duplicata mesmo email 24h tratada

## Dependências
Task #1 (API base + tabela \`leads\`)`,
  },
  {
    order: 3,
    name: "POST /admin/auth/login + JWT",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
Autenticação real substituindo credenciais hardcoded.

## O que fazer
- \`POST /api/v1/admin/auth/login\` — email + senha → JWT
- \`POST /api/v1/admin/auth/refresh\` — renovar access token
- \`POST /api/v1/admin/auth/logout\` — revogar refresh (Redis)
- \`GET /api/v1/admin/auth/me\` — usuário autenticado
- Access token **15 min** + refresh **7 dias**
- bcrypt/argon2 para \`password_hash\`
- Roles: \`super_admin\`, \`admin\`, \`editor\`, \`viewer\`
- Seed de usuário admin inicial

## Critérios de aceite
- [ ] Login retorna \`accessToken\`, \`refreshToken\`, \`user\`
- [ ] Token inválido → 401
- [ ] Middleware \`validateJwt\` protege rotas admin

## Dependências
Task #1 (tabela \`users\`)`,
  },
  {
    order: 4,
    name: "GET /admin/leads + PATCH /admin/leads/:id",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
API de gestão de leads consumida pelo \`AdminDashboard.tsx\`.

## O que fazer
- \`GET /api/v1/admin/leads\` — listagem paginada
  - Query: \`search\`, \`status\`, \`page\`, \`limit\`, \`sort\`
- \`GET /api/v1/admin/leads/:id\` — detalhe completo
- \`PATCH /api/v1/admin/leads/:id\` — alterar status
- Status alinhados ao frontend: \`new\`, \`in_progress\`, \`replied\`, \`closed\`
- Resolver labels de pain points por idioma na resposta

## Critérios de aceite
- [ ] Formato JSON compatível com tipo \`Lead\` do frontend
- [ ] Busca funciona em name, email, company
- [ ] PATCH persiste status corretamente

## Dependências
Tasks #1, #2, #3`,
  },
  {
    order: 5,
    name: "GET /admin/dashboard/stats",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
Alimentar gráficos e métricas do Overview no admin.

## O que fazer
- \`GET /api/v1/admin/dashboard/stats?months=6\`
- Retornar:
  - Total de leads, novos, em andamento, convertidos
  - Leads por mês (gráfico bar chart)
  - Taxa de conversão por mês (line chart)
  - Tempo médio de resposta

## Critérios de aceite
- [ ] Dados batem com tabela \`leads\`
- [ ] Formato compatível com \`Overview()\` + recharts

## Dependências
Tasks #1, #2, #4`,
  },
  {
    order: 6,
    name: "Integrar Home.tsx → submit real",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
Formulário de contato envia dados reais incluindo pain points.

## O que fazer
- Criar \`src/services/api.ts\` ou \`src/api/leads.ts\`
- Configurar \`VITE_API_URL\` no \`.env\`
- Substituir \`setSubmitted(true)\` por \`fetch POST /public/leads\`
- Enviar **painPoints** do passo 1 (\`selected\`) — bug atual!
- Incluir \`lang\` do contexto PT/EN
- Loading, erro e sucesso na UI
- Integrar widget CAPTCHA no submit

## Arquivo principal
\`src/app/pages/Home.tsx\` → \`ContactForm()\`

## Critérios de aceite
- [ ] Submit cria lead visível no admin
- [ ] Pain points chegam no backend
- [ ] Erros de validação exibidos ao usuário

## Dependências
Task #2 (endpoint leads)`,
  },
  {
    order: 7,
    name: "Integrar AdminLogin.tsx → login real",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
Login admin via API JWT.

## O que fazer
- Substituir validação hardcoded por \`POST /admin/auth/login\`
- Remover bloco **DEMO CREDENTIALS** da UI
- Armazenar tokens (memória + refresh ou httpOnly cookie)
- Tratar 401 com mensagem de erro
- Redirect para \`/admin/dashboard\` após sucesso

## Arquivo
\`src/app/pages/AdminLogin.tsx\`

## Critérios de aceite
- [ ] Login funciona com usuário do banco
- [ ] Credenciais demo removidas do código e UI

## Dependências
Task #3 (auth API)`,
  },
  {
    order: 8,
    name: "Integrar AdminDashboard.tsx → leads e stats reais",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
Dashboard consome API em vez de mocks.

## O que fazer
- Substituir \`mockLeads\` por \`GET /admin/leads\`
- Substituir métricas mock por \`GET /admin/dashboard/stats\`
- \`PATCH /admin/leads/:id\` ao alterar status no modal
- Guard de rota via \`GET /admin/auth/me\` (não só sessionStorage)
- Header \`Authorization: Bearer\` em todas as chamadas
- Refresh token automático em 401

## Arquivo
\`src/app/pages/AdminDashboard.tsx\`

## Critérios de aceite
- [ ] Leads reais listados com busca/filtro
- [ ] Gráficos refletem dados do banco
- [ ] Status update persiste

## Dependências
Tasks #4, #5, #7`,
  },
  {
    order: 9,
    name: "Remover credenciais demo e sessionStorage mock",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
Eliminar riscos de segurança do MVP mock.

## O que fazer
- Remover \`admin@ntsistemas.com\` / \`nt@admin2024\` do código
- Remover \`sessionStorage.setItem("nt_admin", "1")\`
- Implementar logout real (\`POST /admin/auth/logout\`)
- Proteger \`/admin/dashboard\` com auth guard baseado em JWT
- Revisar \`index.html\` e envs — sem secrets no frontend

## Critérios de aceite
- [ ] Nenhuma credencial no bundle JS
- [ ] Rota admin inacessível sem token válido
- [ ] Logout limpa sessão

## Dependências
Tasks #7, #8`,
  },
  {
    order: 10,
    name: "Deploy API no K8s",
    phase: "Fase 1",
    priority: "Alta",
    markdown: `## Objetivo
API em produção/homologação no cluster existente.

## O que fazer
- Criar \`k8s/api/deployment.yaml\`, \`service.yaml\`
- ConfigMap + Secrets (DB, Redis, JWT secret, email, CAPTCHA)
- Ingress/proxy: \`/api/*\` → serviço API
- Health probes em \`/api/v1/health\`
- Pipeline CI/CD (GHCR) alinhado ao \`.github/workflows/\`
- Ambiente HML em \`k8s/hml/\`

## Critérios de aceite
- [ ] API acessível em HML
- [ ] Frontend HML aponta \`VITE_API_URL\` correto
- [ ] Migrations rodam no deploy

## Dependências
Tasks #1–#9 (MVP funcional)`,
  },
  {
    order: 11,
    name: "GET/PUT /admin/settings + integrar SettingsSection",
    phase: "Fase 2",
    priority: "Média",
    markdown: `## Objetivo
Settings do admin persistidos no banco.

## O que fazer
- \`GET /api/v1/admin/settings\` — perfil admin + notificações + site config
- \`PUT /api/v1/admin/settings\` — salvar alterações
- Tabela \`settings\` (key-value ou JSON document)
- Integrar \`SettingsSection()\` no dashboard — substituir save fake

## Chaves principais
\`site.available\`, \`contact.notificationEmail\`, toggles de notificação

## Critérios de aceite
- [ ] Save persiste e recarrega corretamente
- [ ] Apenas admin autenticado pode alterar

## Dependências
Fase 1 concluída`,
  },
  {
    order: 12,
    name: "GET /public/settings → footer social links",
    phase: "Fase 2",
    priority: "Média",
    markdown: `## Objetivo
Footer dinâmico com links sociais reais.

## O que fazer
- \`GET /api/v1/public/settings\` — sem auth
- Retornar: \`social.github\`, \`social.linkedin\`, \`social.email\`, \`available\`, \`responseSlaHours\`
- \`Home.tsx\` → \`Footer()\` busca settings no mount
- Substituir \`href="#"\` por URLs reais

## Critérios de aceite
- [ ] Links sociais editáveis via admin
- [ ] Badge "Disponível" reflete \`site.available\`

## Dependências
Task #11`,
  },
  {
    order: 13,
    name: "Remover noindex e configurar SEO",
    phase: "Fase 2",
    priority: "Média",
    markdown: `## Objetivo
Site indexável pelos buscadores.

## O que fazer
- Remover \`<meta robots content="noindex, nofollow">\` de \`index.html\`
- Tornar robots configurável via \`settings.seo.robots\`
- Meta title/description por idioma (preparar para CMS)
- Atualizar copyright dinâmico (hoje fixo \`© 2024\`)

## Arquivos
\`index.html\`, \`Home.tsx\` → \`Footer()\`

## Critérios de aceite
- [ ] Google pode indexar em produção
- [ ] Settings controlam indexação

## Dependências
Task #11`,
  },
  {
    order: 14,
    name: "GET /public/seo + sitemap/robots dinâmicos",
    phase: "Fase 2",
    priority: "Média",
    markdown: `## Objetivo
SEO técnico automatizado.

## O que fazer
- \`GET /api/v1/public/seo?lang=pt|en\` — title, description, og tags
- \`GET /sitemap.xml\` — home + cases publicados
- \`GET /robots.txt\` — baseado em settings
- Ingress serve sitemap/robots da API ou gera estático no build

## Critérios de aceite
- [ ] sitemap.xml válido
- [ ] robots.txt reflete ambiente (HML vs prod)

## Dependências
Tasks #11, #13`,
  },
  {
    order: 15,
    name: "Seed do conteúdo de Home.tsx para o banco",
    phase: "Fase 3",
    priority: "Média",
    markdown: `## Objetivo
Migrar conteúdo hardcoded para CMS.

## O que fazer
- Script de seed: extrair objeto \`translations\` de \`Home.tsx\`
- Inserir em \`content_sections\` por seção e idioma:
  - \`hero\`, \`services\`, \`infrastructure\`, \`profile\`, \`contact\`, \`footer\`, \`nav\`
- Status \`published\` para conteúdo atual
- Função \`seedContentFromHome()\` idempotente

## Critérios de aceite
- [ ] PT e EN seedados
- [ ] Conteúdo idêntico ao site atual

## Dependências
Fase 1–2, tabela \`content_sections\``,
  },
  {
    order: 16,
    name: "GET /public/content + refatorar Home.tsx",
    phase: "Fase 3",
    priority: "Média",
    markdown: `## Objetivo
Home carrega conteúdo da API.

## O que fazer
- \`GET /api/v1/public/content?lang=pt|en\`
- Refatorar \`Home.tsx\`: remover \`translations\` hardcoded
- Loading skeleton enquanto busca conteúdo
- Fallback se API indisponível
- Manter toggle PT/EN via query ou state

## Critérios de aceite
- [ ] Site renderiza igual ao atual com dados da API
- [ ] Troca de idioma funciona

## Dependências
Task #15`,
  },
  {
    order: 17,
    name: "Página admin /admin/content (criar)",
    phase: "Fase 3",
    priority: "Média",
    markdown: `## Objetivo
Editor CMS no painel admin.

## O que fazer
- Nova rota \`/admin/content\` em \`App.tsx\`
- UI: lista de seções → editor por seção/idioma
- Endpoints admin:
  - \`GET /admin/content\`
  - \`GET /admin/content/:section\`
  - \`PUT /admin/content/:section\`
  - \`POST /admin/content/:section/publish\`
- Preview draft vs published

## Critérios de aceite
- [ ] Admin edita hero em PT e publica
- [ ] Site público reflete após publish

## Dependências
Tasks #15, #16`,
  },
  {
    order: 18,
    name: "Versionamento e rollback",
    phase: "Fase 3",
    priority: "Média",
    markdown: `## Objetivo
Histórico e rollback de conteúdo CMS.

## O que fazer
- Tabela \`content_versions\`
- \`GET /admin/content/:section/history\`
- Incrementar \`version\` a cada save
- Rollback: restaurar versão anterior como draft
- Audit: quem editou e quando

## Critérios de aceite
- [ ] Histórico listado por seção
- [ ] Rollback restaura conteúdo correto

## Dependências
Task #17`,
  },
  {
    order: 19,
    name: "CRUD backend de cases",
    phase: "Fase 4",
    priority: "Baixa",
    markdown: `## Objetivo
Gestão de cases de portfólio.

## O que fazer
- Tabela \`portfolio_cases\` + traduções PT/EN
- Endpoints admin: CRUD + publish + reorder
- Endpoints públicos:
  - \`GET /public/portfolio?lang=&featured=\`
  - \`GET /public/portfolio/:slug\`
- Campos: slug, client, industry, techStack, cover, translations, featured, sortOrder

## Critérios de aceite
- [ ] CRUD completo via API
- [ ] Apenas cases \`published\` aparecem no público

## Dependências
Fase 3 concluída`,
  },
  {
    order: 20,
    name: "Upload de mídia (S3)",
    phase: "Fase 4",
    priority: "Baixa",
    markdown: `## Objetivo
Storage de imagens para portfólio.

## O que fazer
- \`POST /admin/media/upload\` (multipart, max 5MB)
- \`GET /admin/media\`, \`DELETE /admin/media/:id\`
- S3/MinIO + CDN URL
- Thumbnails automáticos
- Validar: jpeg, png, webp

## Critérios de aceite
- [ ] Upload retorna URL pública
- [ ] Delete bloqueia se imagem em uso (409)

## Dependências
Task #1 (infra)`,
  },
  {
    order: 21,
    name: "Componente Portfolio() em Home.tsx",
    phase: "Fase 4",
    priority: "Baixa",
    markdown: `## Objetivo
Seção \`#portfolio\` que hoje **não existe** no site.

## O que fazer
- Criar componente \`Portfolio()\` em \`Home.tsx\`
- Consumir \`GET /public/portfolio?featured=true\`
- Grid de cards: cover, title, client, tech tags
- Link nav \`#portfolio\` já aponta — implementar âncora
- i18n via \`lang\`

## Critérios de aceite
- [ ] Seção visível na home
- [ ] Cases do admin aparecem
- [ ] Layout consistente com design system

## Dependências
Tasks #19, #20`,
  },
  {
    order: 22,
    name: "Página admin /admin/portfolio (criar)",
    phase: "Fase 4",
    priority: "Baixa",
    markdown: `## Objetivo
CRUD visual de cases no admin.

## O que fazer
- Rota \`/admin/portfolio\`
- Listagem, create/edit form, upload cover via media
- Toggle featured, reorder drag-and-drop
- Publish/unpublish
- Formulário bilíngue (PT/EN)

## Critérios de aceite
- [ ] Criar case completo pelo admin
- [ ] Aparece na home após publish

## Dependências
Tasks #19, #20, #21`,
  },
  {
    order: 23,
    name: "Export CSV de leads",
    phase: "Fase 5",
    priority: "Baixa",
    markdown: `## Objetivo
Exportar leads para análise externa.

## O que fazer
- \`GET /admin/leads/export?format=csv&status=\`
- Botão "Exportar CSV" em \`LeadsSection\`
- Colunas: name, email, company, status, painPoints, date
- Filtro por status antes do export

## Critérios de aceite
- [ ] CSV válido abre no Excel/Sheets
- [ ] Respeita filtros ativos

## Dependências
Task #4`,
  },
  {
    order: 24,
    name: "Audit log",
    phase: "Fase 5",
    priority: "Baixa",
    markdown: `## Objetivo
Rastreabilidade de ações admin.

## O que fazer
- Tabela \`audit_log\`: user, action, entity, entityId, metadata, timestamp
- Registrar: login, alteração lead, publish CMS, settings
- Endpoint \`GET /admin/audit\` (super_admin)
- UI opcional no admin

## Critérios de aceite
- [ ] Ações críticas registradas
- [ ] Consulta paginada funciona

## Dependências
Fases 1–4`,
  },
  {
    order: 25,
    name: "Slack notifications",
    phase: "Fase 5",
    priority: "Baixa",
    markdown: `## Objetivo
Alertas em tempo real para novos leads.

## O que fazer
- Webhook Slack configurável em settings
- \`notifySlack()\` após \`createLead()\`
- Mensagem: nome, empresa, pain points, link admin
- Toggle on/off no settings

## Critérios de aceite
- [ ] Novo lead dispara mensagem Slack
- [ ] Falha Slack não bloqueia criação do lead

## Dependências
Tasks #2, #11`,
  },
  {
    order: 26,
    name: "2FA para super_admin",
    phase: "Fase 5",
    priority: "Baixa",
    markdown: `## Objetivo
Segurança extra para conta master.

## O que fazer
- TOTP (Google Authenticator) para role \`super_admin\`
- Setup QR code + backup codes
- Exigir 2FA no login se habilitado
- Endpoints: enable, verify, disable 2FA

## Critérios de aceite
- [ ] Login bloqueado sem código TOTP
- [ ] Apenas super_admin obrigatório

## Dependências
Task #3`,
  },
  {
    order: 27,
    name: "Testes E2E",
    phase: "Fase 5",
    priority: "Baixa",
    markdown: `## Objetivo
Confiança em deploys.

## O que fazer
- Playwright ou Cypress
- Fluxos críticos:
  1. Submit formulário contato → lead no admin
  2. Login admin → listar leads → alterar status
  3. Settings save
- Rodar em CI antes de deploy prod

## Critérios de aceite
- [ ] Suite verde em CI
- [ ] Cobertura dos fluxos MVP

## Dependências
Fase 1 mínimo`,
  },
  {
    order: 28,
    name: "Monitoramento (Sentry)",
    phase: "Fase 5",
    priority: "Baixa",
    markdown: `## Objetivo
Observabilidade de erros em prod.

## O que fazer
- Sentry no frontend (React) e backend (Spring Boot)
- Source maps no build
- Alertas para erros 5xx e unhandled exceptions
- Tags: environment, release version

## Critérios de aceite
- [ ] Erro teste aparece no Sentry
- [ ] HML e prod separados

## Dependências
Task #10 (deploy)`,
  },
];

const PROJECT_MARKDOWN = `# Backend NT Sistemas Web

## Visão geral
Implementar o backend completo para o site institucional bilíngue (PT/EN) e painel admin, conectando o frontend React existente a uma API REST real.

| Item | Detalhe |
|------|---------|
| **Stack** | Spring Boot 3 · Java 21 · PostgreSQL · Redis · S3/MinIO · JWT |
| **Endpoints** | 31 (7 públicos + 24 admin) |
| **Duração estimada** | 8–12 semanas em 5 fases |
| **Spec completa** | [Backend Requirements](${SPEC_URL}) |

## Estado atual do frontend
- ✅ UI pronta: Home, AdminLogin, AdminDashboard
- ⚠️ Tudo mock: leads, auth, settings, conteúdo hardcoded
- ❌ Seção \`#portfolio\` ausente
- ❌ Formulário não envia pain points

## Roadmap por fase

### Fase 1 — MVP: leads + auth (2–3 sem) · Prioridade **Alta**
Conectar frontend ao backend. Leads reais, login JWT, dashboard funcional, deploy API.

### Fase 2 — Settings + SEO (1–2 sem) · Prioridade **Média**
Settings persistidos, footer dinâmico, SEO indexável, sitemap/robots.

### Fase 3 — CMS (2–3 sem) · Prioridade **Média**
Conteúdo da Home no banco, editor admin, versionamento.

### Fase 4 — Portfólio (2 sem) · Prioridade **Baixa**
CRUD cases, upload mídia, seção Portfolio na home, admin portfolio.

### Fase 5 — Polimento (1–2 sem) · Prioridade **Baixa**
CSV export, audit log, Slack, 2FA, E2E, Sentry.

---

## Tasks ordenadas (Fase → Prioridade)

${TASKS.map(
  (t) =>
    `### ${t.order}. [${t.phase}] ${t.name}\n**Prioridade:** ${t.priority}`
).join("\n\n")}

---

## Ordem de execução recomendada
1. Completar **Fase 1** inteira antes de avançar (dependências em cadeia)
2. Fases 2 e 3 podem ter overlap parcial após MVP
3. Fase 4 depende de mídia (S3) — pode iniciar backend em paralelo à Fase 3
4. Fase 5 é contínua — E2E e Sentry cedo em HML, resto antes de prod
`;

async function ensureOrdemProperty() {
  const ds = await notion.dataSources.retrieve({ data_source_id: TASKS_DS });
  if (ds.properties.Ordem) return;

  await notion.dataSources.update({
    data_source_id: TASKS_DS,
    properties: {
      Ordem: { number: { format: "number" } },
    },
  });
}

async function getProjectTasks() {
  const res = await notion.dataSources.query({
    data_source_id: TASKS_DS,
    filter: { property: "Projeto", relation: { contains: PROJECT_ID } },
    page_size: 100,
  });
  const byName = new Map();
  for (const page of res.results) {
    const name = page.properties["Nome da tarefa"]?.title?.[0]?.plain_text;
    if (name) byName.set(name, page.id);
  }
  return byName;
}

async function main() {
  await ensureOrdemProperty();

  console.log("Updating project...");
  await notion.pages.updateMarkdown({
    page_id: PROJECT_ID,
    type: "replace_content",
    replace_content: { new_str: PROJECT_MARKDOWN, allow_deleting_content: true },
  });

  const byName = await getProjectTasks();
  console.log(`Updating ${TASKS.length} tasks...`);

  for (const task of TASKS) {
    const pageId = byName.get(task.name);
    if (!pageId) {
      console.warn("  ⚠ Not found:", task.name);
      continue;
    }

    await notion.pages.update({
      page_id: pageId,
      properties: {
        Ordem: { number: task.order },
        Prioridade: { select: { name: task.priority } },
        Tags: {
          multi_select: [{ name: "Website" }, { name: task.phase }],
        },
      },
    });

    const body = `${task.markdown}

---
**Ordem:** ${task.order} · **Fase:** ${task.phase} · **Prioridade:** ${task.priority}
📄 [Spec completa](${SPEC_URL})`;

    await notion.pages.updateMarkdown({
      page_id: pageId,
      type: "replace_content",
      replace_content: { new_str: body, allow_deleting_content: true },
    });

    console.log(`  ✓ ${task.order}. ${task.name}`);
  }

  console.log("\n✅ Projeto e tasks detalhados!");
  console.log(
    `Projeto: https://app.notion.com/p/Backend-NT-Sistemas-Web-${PROJECT_ID}`
  );
  console.log("Dica: no board Tarefas, ordene por coluna Ordem (1→28).");
}

main().catch((err) => {
  console.error("Error:", err.body ?? err.message ?? err);
  process.exit(1);
});
