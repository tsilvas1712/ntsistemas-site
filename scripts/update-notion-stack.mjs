#!/usr/bin/env node
/**
 * Atualiza documentação no Notion: Spring Boot (Java) + Notion como fonte oficial.
 * Usage: NOTION_API_KEY=... node scripts/update-notion-stack.mjs
 */

import { Client } from "@notionhq/client";

const notion = new Client({ auth: process.env.NOTION_API_KEY });
if (!process.env.NOTION_API_KEY) {
  console.error("Missing NOTION_API_KEY");
  process.exit(1);
}

const PROJECT_ID = "39226067c138811c9199e1ef26fe7128";
const SPEC_ID = "39226067c138810c9c97ecb645244bf8";
const PLAN_ID = "39226067-c138-811f-bed4-d060878a083d";
const TASKS_DS = "3f826067-c138-83e4-9f51-8763362c7a6d";

const SSOT_BANNER = `> **📌 Documentação oficial:** este workspace Notion é a **fonte da verdade** do projeto.
> O arquivo \`BACKEND_REQUIREMENTS.md\` foi descontinuado — toda atualização de spec, stack e tasks deve ser feita **apenas no Notion**.

`;

const REPLACEMENTS = [
  [/NestJS \/ FastAPI/g, "Spring Boot (Java)"],
  [/NestJS \(TypeScript\)/g, "Spring Boot 3 (Java 21)"],
  [/\*\*NestJS\*\* \(TypeScript\)/g, "**Spring Boot 3** (Java 21)"],
  [/API \*\*NestJS\*\*/g, "API **Spring Boot 3**"],
  [/API base \(NestJS\)/g, "API base (Spring Boot)"],
  [/\(NestJS, porta 3000\)/g, "(Spring Boot, porta 8080)"],
  [/ntsistemas-api \(NestJS, porta 3000\)/g, "ntsistemas-api (Spring Boot, porta 8080)"],
  [/Deployment: ntsistemas-api \(NestJS[^)]*\)/g, "Deployment: ntsistemas-api (Spring Boot, porta 8080)"],
  [/\|\s*\*\*NestJS\*\*[^\n]*/g, "| **Spring Boot 3** (Java 21) — REST API, alinhado ao ecossistema Java/Spring do portfólio |"],
  [/NestJS · PostgreSQL/g, "Spring Boot · PostgreSQL"],
  [/NestJS \+ PostgreSQL/g, "Spring Boot + PostgreSQL"],
  [/backend \(NestJS\)/gi, "backend (Spring Boot)"],
  [/Criar repo\/módulo API \*\*NestJS\*\* \(TypeScript\)/g, "Criar repo/módulo API **Spring Boot 3** (Java 21, Maven/Gradle)"],
  [/Sentry no frontend \(React\) e backend \(NestJS\)/g, "Sentry no frontend (React) e backend (Spring Boot)"],
  [/React · Next\.js · Spring · FastAPI · NestJS/g, "React · Next.js · Spring Boot · Java"],
  [/FastAPI · NestJS/g, "Spring Boot · Java"],
  [/NestJS/g, "Spring Boot"],
  [/nestjs/g, "spring-boot"],
  [/\(TypeScript\) com prefixo/g, "(Java) com prefixo"],
  [/TypeORM\/Prisma/g, "Spring Data JPA / Hibernate"],
  [/bcrypt\/argon2/g, "BCrypt (Spring Security)"],
  [/Middleware `validateJwt`/g, "Filter/Interceptor JWT (Spring Security)"],
  [/Swagger\/OpenAPI opcional/g, "SpringDoc OpenAPI (recomendado)"],
];

function applyReplacements(text) {
  let out = text;
  for (const [from, to] of REPLACEMENTS) out = out.replace(from, to);
  return out;
}

async function updatePageMarkdown(pageId, { prependBanner = false } = {}) {
  const { markdown } = await notion.pages.retrieveMarkdown({ page_id: pageId });
  let updated = applyReplacements(markdown);

  if (prependBanner && !updated.includes("Documentação oficial")) {
    updated = SSOT_BANNER + updated;
  }

  if (updated === markdown && !prependBanner) return false;

  await notion.pages.updateMarkdown({
    page_id: pageId,
    type: "replace_content",
    replace_content: { new_str: updated, allow_deleting_content: true },
  });
  return true;
}

async function updateTaskTitle(pageId, oldName, newName) {
  await notion.pages.update({
    page_id: pageId,
    properties: { "Nome da tarefa": { title: [{ type: "text", text: { content: newName } }] } },
  });
}

async function main() {
  console.log("Updating spec page...");
  await updatePageMarkdown(SPEC_ID, { prependBanner: true });

  console.log("Updating project page...");
  await updatePageMarkdown(PROJECT_ID, { prependBanner: true });

  console.log("Updating plan page...");
  await updatePageMarkdown(PLAN_ID, { prependBanner: true });

  console.log("Updating Resumo on project...");
  const project = await notion.pages.retrieve({ page_id: PROJECT_ID });
  const resumo = project.properties.Resumo?.rich_text?.[0]?.plain_text || "";
  if (resumo.includes("NestJS") || !resumo.includes("Spring Boot")) {
    await notion.pages.update({
      page_id: PROJECT_ID,
      properties: {
        Resumo: {
          rich_text: [
            {
              type: "text",
              text: {
                content:
                  "Implementação do backend (Spring Boot 3 + Java 21 + PostgreSQL) para o site institucional e painel admin. Notion = documentação oficial. 5 fases, ~8–12 semanas.",
              },
            },
          ],
        },
      },
    });
  }

  const tasks = await notion.dataSources.query({
    data_source_id: TASKS_DS,
    filter: { property: "Projeto", relation: { contains: PROJECT_ID } },
    page_size: 100,
  });

  console.log(`Updating ${tasks.results.length} tasks...`);
  for (const page of tasks.results) {
    const name = page.properties["Nome da tarefa"]?.title?.[0]?.plain_text || "";
    const newName = applyReplacements(name);
    if (newName !== name) await updateTaskTitle(page.id, name, newName);
    await updatePageMarkdown(page.id);
    console.log("  ✓", newName || name);
  }

  console.log("\n✅ Notion atualizado: Spring Boot (Java) + SSOT");
  console.log(`Spec:    https://app.notion.com/p/Backend-Requirements-NT-Sistemas-Web-${SPEC_ID}`);
  console.log(`Projeto: https://app.notion.com/p/Backend-NT-Sistemas-Web-${PROJECT_ID}`);
}

main().catch((err) => {
  console.error("Error:", err.body ?? err.message ?? err);
  process.exit(1);
});
