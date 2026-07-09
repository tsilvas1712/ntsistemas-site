#!/usr/bin/env node
/**
 * Create Backend project + tasks in Notion "Projetos & Tarefas" template.
 *
 * Usage:
 *   NOTION_API_KEY=... node scripts/create-notion-project.mjs
 */

import { Client } from "@notionhq/client";

const API_KEY = process.env.NOTION_API_KEY;
if (!API_KEY) {
  console.error("Missing NOTION_API_KEY");
  process.exit(1);
}

const notion = new Client({ auth: API_KEY });

const PROJECTS_DS = "6b726067-c138-83eb-a8d1-079d673f872a";
const TASKS_DS = "3f826067-c138-83e4-9f51-8763362c7a6d";
const SPEC_URL =
  "https://app.notion.com/p/Backend-Requirements-NT-Sistemas-Web-39226067c138810c9c97ecb645244bf8";
const PLAN_URL =
  "https://app.notion.com/p/Implementation-Plan-Backend-NT-Sistemas-Web-39226067c138811fbed4d060878a083d";

const TASKS = [
  { name: "API base (Spring Boot) + PostgreSQL + Redis", phase: "Fase 1", priority: "Alta" },
  { name: "POST /public/leads + email notification + CAPTCHA", phase: "Fase 1", priority: "Alta" },
  { name: "POST /admin/auth/login + JWT", phase: "Fase 1", priority: "Alta" },
  { name: "GET /admin/leads + PATCH /admin/leads/:id", phase: "Fase 1", priority: "Alta" },
  { name: "GET /admin/dashboard/stats", phase: "Fase 1", priority: "Alta" },
  { name: "Integrar Home.tsx → submit real", phase: "Fase 1", priority: "Alta" },
  { name: "Integrar AdminLogin.tsx → login real", phase: "Fase 1", priority: "Alta" },
  { name: "Integrar AdminDashboard.tsx → leads e stats reais", phase: "Fase 1", priority: "Alta" },
  { name: "Remover credenciais demo e sessionStorage mock", phase: "Fase 1", priority: "Alta" },
  { name: "Deploy API no K8s", phase: "Fase 1", priority: "Alta" },
  { name: "GET/PUT /admin/settings + integrar SettingsSection", phase: "Fase 2", priority: "Média" },
  { name: "GET /public/settings → footer social links", phase: "Fase 2", priority: "Média" },
  { name: "Remover noindex e configurar SEO", phase: "Fase 2", priority: "Média" },
  { name: "GET /public/seo + sitemap/robots dinâmicos", phase: "Fase 2", priority: "Média" },
  { name: "Seed do conteúdo de Home.tsx para o banco", phase: "Fase 3", priority: "Média" },
  { name: "GET /public/content + refatorar Home.tsx", phase: "Fase 3", priority: "Média" },
  { name: "Página admin /admin/content (criar)", phase: "Fase 3", priority: "Média" },
  { name: "Versionamento e rollback", phase: "Fase 3", priority: "Média" },
  { name: "CRUD backend de cases", phase: "Fase 4", priority: "Baixa" },
  { name: "Upload de mídia (S3)", phase: "Fase 4", priority: "Baixa" },
  { name: "Componente Portfolio() em Home.tsx", phase: "Fase 4", priority: "Baixa" },
  { name: "Página admin /admin/portfolio (criar)", phase: "Fase 4", priority: "Baixa" },
  { name: "Export CSV de leads", phase: "Fase 5", priority: "Baixa" },
  { name: "Audit log", phase: "Fase 5", priority: "Baixa" },
  { name: "Slack notifications", phase: "Fase 5", priority: "Baixa" },
  { name: "2FA para super_admin", phase: "Fase 5", priority: "Baixa" },
  { name: "Testes E2E", phase: "Fase 5", priority: "Baixa" },
  { name: "Monitoramento (Sentry)", phase: "Fase 5", priority: "Baixa" },
];

function rt(text) {
  return [{ type: "text", text: { content: text.slice(0, 2000) } }];
}

async function ensurePhaseTags() {
  const ds = await notion.dataSources.retrieve({ data_source_id: TASKS_DS });
  const existing = ds.properties.Tags.multi_select.options.map((o) => o.name);
  const phases = ["Fase 1", "Fase 2", "Fase 3", "Fase 4", "Fase 5"];
  const toAdd = phases.filter((p) => !existing.includes(p));
  if (!toAdd.length) return;

  await notion.dataSources.update({
    data_source_id: TASKS_DS,
    properties: {
      Tags: {
        multi_select: {
          options: [
            ...ds.properties.Tags.multi_select.options,
            ...toAdd.map((name) => ({ name, color: "gray" })),
          ],
        },
      },
    },
  });
}

async function main() {
  await ensurePhaseTags();

  const start = new Date();
  const end = new Date(start);
  end.setMonth(end.getMonth() + 3);

  console.log("Creating project...");
  const project = await notion.pages.create({
    parent: { type: "data_source_id", data_source_id: PROJECTS_DS },
    icon: { type: "emoji", emoji: "🚀" },
    properties: {
      "Nome do projeto": { title: rt("Backend NT Sistemas Web") },
      Status: { status: { name: "Em andamento" } },
      Prioridade: { select: { name: "Alta" } },
      Resumo: {
        rich_text: rt(
          "Implementação do backend (Spring Boot 3 + Java 21 + PostgreSQL) para o site institucional e painel admin. Notion = documentação oficial. 5 fases, ~8–12 semanas."
        ),
      },
      Datas: {
        date: {
          start: start.toISOString().slice(0, 10),
          end: end.toISOString().slice(0, 10),
        },
      },
    },
    markdown: `# Backend NT Sistemas Web

## Objetivo
Tornar o site **100% funcional** com API REST, persistência de dados e integração com o frontend React existente.

## Documentação
- [Backend Requirements (spec completa)](${SPEC_URL})
- [Implementation Plan](${PLAN_URL})

## Stack
Spring Boot 3 · Java 21 · PostgreSQL · Redis · S3/MinIO · JWT · K8s

## Fases
1. **MVP** — leads + auth (2–3 sem)
2. **Settings + SEO** (1–2 sem)
3. **CMS de conteúdo** (2–3 sem)
4. **Portfólio** (2 sem)
5. **Polimento** (1–2 sem)
`,
  });

  console.log(`Project: ${project.url}`);

  console.log(`Creating ${TASKS.length} tasks...`);
  for (const task of TASKS) {
    await notion.pages.create({
      parent: { type: "data_source_id", data_source_id: TASKS_DS },
      properties: {
        "Nome da tarefa": { title: rt(task.name) },
        Status: { status: { name: "Não iniciada" } },
        Prioridade: { select: { name: task.priority } },
        Projeto: { relation: [{ id: project.id }] },
        Tags: {
          multi_select: [{ name: "Website" }, { name: task.phase }],
        },
      },
    });
    console.log("  ✓", task.name);
  }

  console.log("\n✅ Done!");
  console.log(`Projeto: ${project.url}`);
  console.log(`Tarefas: ${TASKS.length} vinculadas`);
}

main().catch((err) => {
  console.error("Error:", err.body ?? err.message ?? err);
  process.exit(1);
});
