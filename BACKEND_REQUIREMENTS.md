# Levantamento de Backend — NT Sistemas Web

Documento de requisitos para tornar o site **100% funcional**: APIs, persistência, integrações e conexão com a área admin já existente no frontend.

**Data:** Julho/2026  
**Escopo:** Site institucional bilíngue (PT/EN) + painel admin  
**Estado atual:** Frontend com rotas, admin UI e dados mock — **sem backend real**

> **Nota:** Este arquivo é uma cópia local para desenvolvimento. A documentação viva do projeto também está no Notion: [Backend Requirements — NT Sistemas Web](https://app.notion.com/p/Backend-Requirements-NT-Sistemas-Web-39226067c138810c9c97ecb645244bf8). Em caso de divergência, priorize o Notion.

---

## Sumário

1. [Diagnóstico](#1-diagnóstico)
2. [Estrutura do frontend](#2-estrutura-do-frontend)
3. [Rotas e páginas](#3-rotas-e-páginas)
4. [Mapa frontend → backend](#4-mapa-frontend--backend)
5. [Arquitetura proposta](#5-arquitetura-proposta)
6. [Resumo de endpoints](#6-resumo-de-endpoints)
7. [Modelos de payload por endpoint](#7-modelos-de-payload-por-endpoint)
8. [Modelo de dados (banco)](#8-modelo-de-dados-banco)
9. [Segurança e infra](#9-segurança-e-infra)
10. [Gaps no frontend](#10-gaps-no-frontend)
11. [Fases de implementação](#11-fases-de-implementação)

---

## 1. Diagnóstico

### Estado do site

| Seção / Página | Rota / Âncora | Status |
|----------------|---------------|--------|
| Home — Hero, Serviços, Infra, Expertise | `/`, `#services`, etc. | ✅ UI pronta, conteúdo hardcoded |
| Home — Portfólio | `#portfolio` | ❌ Nav existe, seção ausente |
| Home — Contato | `#contact` | ⚠️ Submit mock (só `setSubmitted`) |
| Home — Footer | — | ⚠️ Links sociais com `href="#"` |
| Admin — Login | `/admin` | ⚠️ Credenciais hardcoded + `sessionStorage` |
| Admin — Dashboard | `/admin/dashboard` | ⚠️ Leads e gráficos mock |

### O que não existe

- Chamadas HTTP (`fetch`, `axios`)
- Pasta `src/services/` ou `src/api/`
- Backend no repositório
- Auth real (JWT)
- Persistência de leads
- `index.html` com `robots: noindex, nofollow`

### Bug no formulário

`Home.tsx` coleta pain points no passo 1, mas o submit não envia nada:

```typescript
// Atual — linha ~615 em Home.tsx
<form onSubmit={(e) => { e.preventDefault(); setSubmitted(true); }}>
```

---

## 2. Estrutura do frontend

```
web/
├── index.html
├── src/
│   ├── main.tsx
│   └── app/
│       ├── App.tsx                 # Router (3 rotas)
│       └── pages/
│           ├── Home.tsx            # Site público (~730 linhas)
│           ├── AdminLogin.tsx      # Login mock
│           └── AdminDashboard.tsx  # Dashboard mock
├── nginx.conf
├── Dockerfile
└── k8s/
```

---

## 3. Rotas e páginas

```typescript
// src/app/App.tsx
{ path: "/", Component: Home },
{ path: "/admin", Component: AdminLogin },
{ path: "/admin/dashboard", Component: AdminDashboard },
```

### Tipo `Lead` (já no frontend)

```typescript
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
```

### Pain point IDs (`Home.tsx`)

| ID | Label PT |
|----|----------|
| `legacy` | Resgate de Sistema Legado |
| `bugs` | Forense de Bugs Críticos |
| `arch` | Redesign Arquitetural |
| `mvp` | Novo MVP / Greenfield |
| `devops` | DevOps & Infraestrutura |
| `security` | Hardening de Segurança |

---

## 4. Mapa frontend → backend

| Ação | Arquivo | Endpoint | Prioridade |
|------|---------|----------|------------|
| Enviar formulário | `Home.tsx` | `POST /api/v1/public/leads` | 🔴 |
| Login admin | `AdminLogin.tsx` | `POST /api/v1/admin/auth/login` | 🔴 |
| Guard de sessão | `AdminDashboard.tsx` | `GET /api/v1/admin/auth/me` | 🔴 |
| Listar leads | `AdminDashboard.tsx` | `GET /api/v1/admin/leads` | 🔴 |
| Detalhe / status lead | `AdminDashboard.tsx` | `GET/PATCH /api/v1/admin/leads/:id` | 🔴 |
| Dashboard stats | `AdminDashboard.tsx` | `GET /api/v1/admin/dashboard/stats` | 🟡 |
| Settings | `AdminDashboard.tsx` | `GET/PUT /api/v1/admin/settings` | 🟡 |
| Conteúdo do site | `Home.tsx` | `GET /api/v1/public/content` | 🟡 |
| Portfólio | `Home.tsx` (criar) | `GET /api/v1/public/portfolio` | 🟢 |

---

## 5. Arquitetura proposta

```
Frontend (React/Vite)  ──HTTPS──▶  API REST (Spring Boot 3, Java 21, porta 8080)
                                        │
                         ┌──────────────┼──────────────┐
                         ▼              ▼              ▼
                    PostgreSQL        Redis         S3/MinIO
```

**Stack backend:**

| Camada | Tecnologia |
|--------|------------|
| API | Spring Boot 3 (Java 21), Maven/Gradle |
| Persistência | Spring Data JPA / Hibernate |
| Banco | PostgreSQL |
| Cache / rate limit | Redis |
| Auth | Spring Security + JWT (BCrypt) |
| Email | AWS SES / Resend |
| Storage | S3 / MinIO |
| Docs | SpringDoc OpenAPI |

**Deploy:** Ingress K8s — `/` → frontend (Nginx), `/api` → `ntsistemas-api:8080`

---

## 6. Resumo de endpoints

### Públicos (7)

| Método | Endpoint |
|--------|----------|
| GET | `/api/v1/health` |
| GET | `/api/v1/public/content?lang=pt\|en` |
| GET | `/api/v1/public/settings` |
| GET | `/api/v1/public/seo?lang=pt\|en` |
| GET | `/api/v1/public/portfolio` |
| GET | `/api/v1/public/portfolio/:slug` |
| POST | `/api/v1/public/leads` |

### Admin — JWT (24)

| Método | Endpoint |
|--------|----------|
| POST | `/api/v1/admin/auth/login` |
| POST | `/api/v1/admin/auth/refresh` |
| POST | `/api/v1/admin/auth/logout` |
| GET | `/api/v1/admin/auth/me` |
| GET | `/api/v1/admin/dashboard/stats` |
| GET | `/api/v1/admin/leads` |
| GET | `/api/v1/admin/leads/:id` |
| PATCH | `/api/v1/admin/leads/:id` |
| POST | `/api/v1/admin/leads/:id/notes` |
| GET | `/api/v1/admin/leads/export?format=csv` |
| GET | `/api/v1/admin/content` |
| GET | `/api/v1/admin/content/:section` |
| PUT | `/api/v1/admin/content/:section` |
| POST | `/api/v1/admin/content/:section/publish` |
| GET | `/api/v1/admin/content/:section/history` |
| GET | `/api/v1/admin/portfolio` |
| POST | `/api/v1/admin/portfolio` |
| PUT | `/api/v1/admin/portfolio/:id` |
| DELETE | `/api/v1/admin/portfolio/:id` |
| PATCH | `/api/v1/admin/portfolio/:id/publish` |
| PATCH | `/api/v1/admin/portfolio/reorder` |
| POST | `/api/v1/admin/media/upload` |
| GET | `/api/v1/admin/media` |
| DELETE | `/api/v1/admin/media/:id` |
| GET | `/api/v1/admin/settings` |
| PUT | `/api/v1/admin/settings` |

**Total: 31 endpoints**

---

## 7. Modelos de payload por endpoint

### Convenções

**Header admin:** `Authorization: Bearer <accessToken>`

**Erro padrão (4xx/5xx):**
```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Dados inválidos.",
    "details": [{ "field": "email", "message": "E-mail inválido." }]
  }
}
```

**Tipos compartilhados:**
```typescript
type Lang = "pt" | "en";
type LeadStatus = "new" | "in_progress" | "replied" | "closed";
type PainPointId = "legacy" | "bugs" | "arch" | "mvp" | "devops" | "security";
```

---

### 7.1 Públicos

#### `GET /api/v1/health`

**Response `200`:**
```json
{
  "status": "ok",
  "version": "1.0.0",
  "timestamp": "2026-07-07T23:00:00.000Z",
  "services": { "database": "ok", "redis": "ok" }
}
```

---

#### `GET /api/v1/public/content?lang=pt`

**Response `200`:**
```json
{
  "lang": "pt",
  "nav": {
    "services": "Serviços",
    "expertise": "Expertise",
    "portfolio": "Portfólio",
    "infra": "Infraestrutura Cloud",
    "cta": "Solicitar Avaliação Técnica"
  },
  "hero": {
    "badge": "Disponível para novos projetos",
    "headline1": "Modernizando",
    "headline2": "Codebases,",
    "headline3": "Escalando Ideias.",
    "sub": "10+ anos de experiência em engenharia de elite...",
    "tags": ["Resgate de Legado", "Bugs Críticos"],
    "cta1": "Vamos Conversar",
    "cta2": "Ver Stack",
    "stats": [
      { "value": "10+", "label": "Anos de Engenharia" },
      { "value": "80+", "label": "Projetos Entregues" },
      { "value": "99.9%", "label": "SLA de Uptime" }
    ],
    "available": true
  },
  "terminal": { "label": "nt-stack ~ análise", "cmd": "$ nt analyze --repo monolito-legado", "issues": [] },
  "services": {
    "badge": "Serviços Principais",
    "heading1": "O que construímos,",
    "heading2": "corrigimos e escalamos.",
    "sub": "...",
    "items": [{ "title": "Modernização de Código Legado", "desc": "...", "tags": ["Refactoring"] }]
  },
  "infrastructure": {
    "badge": "Infraestrutura Full-Stack",
    "heading1": "Você entrega código.",
    "heading2": "Nós cuidamos do resto.",
    "features": ["AWS · GCP · Digital Ocean"],
    "techStack": [{ "name": "AWS", "sub": "EC2, RDS, Lambda", "color": "#FF9900" }],
    "pipelineSteps": ["Lint", "Test", "Build", "Prod Deploy"]
  },
  "profile": {
    "badge": "Por que NT Sistemas Web",
    "heading1": "Rigor de engenharia,",
    "heading2": "pragmatismo de negócio.",
    "role": "10+ anos · Full-Stack & Cloud",
    "pitch": "...",
    "points": ["Background de Expert/Staff Engineer..."],
    "skills": {
      "languages": { "label": "Linguagens", "value": "TypeScript · Java · Go · Python" },
      "frameworks": { "label": "Frameworks", "value": "React · Spring Boot · Java" },
      "databases": { "label": "Bancos de Dados", "value": "PostgreSQL · MongoDB · Redis" }
    }
  },
  "contact": {
    "badge": "Escope seu Projeto",
    "heading1": "Inicie a",
    "heading2": "conversa.",
    "sub": "Avaliação técnica em até 48 horas.",
    "painPoints": [{ "id": "legacy", "label": "Resgate de Sistema Legado", "icon": "🚑" }],
    "fields": { "name": "Nome Completo", "email": "E-mail Corporativo", "message": "Descreva seu problema" },
    "successTitle": "Brief recebido.",
    "successSub": "Responderemos em até 48 horas.",
    "responseSlaHours": 48
  },
  "footer": { "copy": "© 2026 NT Sistemas Web · Todos os direitos reservados" }
}
```

---

#### `GET /api/v1/public/settings`

**Response `200`:**
```json
{
  "site": { "available": true, "defaultLang": "pt", "maintenanceMode": false },
  "contact": { "responseSlaHours": 48 },
  "social": {
    "github": "https://github.com/ntsistemasweb",
    "linkedin": "https://linkedin.com/company/ntsistemasweb",
    "email": "contato@ntsistemasweb.com.br"
  }
}
```

---

#### `GET /api/v1/public/seo?lang=pt`

**Response `200`:**
```json
{
  "lang": "pt",
  "title": "NT Sistemas Web",
  "description": "Engenharia de software de elite. Modernização de legado, MVPs e DevOps.",
  "robots": "index, follow",
  "ogImage": "https://ntsistemasweb.com.br/og-image.jpg",
  "canonicalUrl": "https://ntsistemasweb.com.br"
}
```

---

#### `GET /api/v1/public/portfolio?lang=pt&featured=true&page=1&limit=12`

**Response `200`:**
```json
{
  "data": [{
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "slug": "modernizacao-fintech-xp",
    "title": "Modernização de Plataforma Fintech",
    "client": "FinTech XP",
    "industry": "Fintech",
    "summary": "Migração de monólito Java 8 para microserviços.",
    "techStack": ["Java", "Spring Boot 3", "Kubernetes"],
    "coverImage": "https://cdn.ntsistemasweb.com.br/media/cover.jpg",
    "featured": true,
    "publishedAt": "2026-03-15T10:00:00.000Z"
  }],
  "total": 8,
  "page": 1,
  "limit": 12
}
```

---

#### `GET /api/v1/public/portfolio/:slug?lang=pt`

**Response `200`:**
```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "slug": "modernizacao-fintech-xp",
  "lang": "pt",
  "title": "Modernização de Plataforma Fintech",
  "client": "FinTech XP",
  "industry": "Fintech",
  "summary": "...",
  "challenge": "...",
  "solution": "...",
  "results": "60% redução no tempo de deploy.",
  "techStack": ["Java", "Spring Boot 3", "Kubernetes", "PostgreSQL"],
  "coverImage": "https://cdn.ntsistemasweb.com.br/media/cover.jpg",
  "gallery": [],
  "testimonial": { "quote": "...", "author": "Rodrigo Almeida", "role": "CTO" },
  "featured": true,
  "publishedAt": "2026-03-15T10:00:00.000Z"
}
```

---

#### `POST /api/v1/public/leads`

**Request:**
```json
{
  "name": "Maria Silva",
  "email": "maria@empresa.com",
  "company": "Empresa Ltda.",
  "budget": "R$ 20k–50k",
  "stack": "Java Spring Boot, MySQL, AWS EC2, React",
  "message": "Sistema legado com problemas de performance e dívida técnica.",
  "painPoints": ["legacy", "bugs", "arch"],
  "lang": "pt",
  "source": "website",
  "captchaToken": "0.ABCdef...",
  "honeypot": ""
}
```

| Campo | Regra |
|-------|-------|
| `name` | Obrigatório, 2–120 chars |
| `email` | Obrigatório, formato válido |
| `message` | Obrigatório, 20–5000 chars |
| `painPoints` | Obrigatório, 1–6 IDs válidos |
| `lang` | `pt` ou `en` |
| `honeypot` | Deve estar vazio |

**Response `201`:**
```json
{
  "id": "7c9e6679-7425-40de-944b-e07fc1f90ae7",
  "message": "Brief recebido.",
  "successTitle": "Brief recebido.",
  "successSub": "Responderemos com uma avaliação técnica em até 48 horas."
}
```

**Response `429`:**
```json
{
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Muitas tentativas. Tente novamente em 1 hora.",
    "retryAfter": 3600
  }
}
```

---

### 7.2 Auth admin

#### `POST /api/v1/admin/auth/login`

**Request:**
```json
{ "email": "admin@ntsistemas.com", "password": "sua-senha-segura" }
```

**Response `200`:**
```json
{
  "accessToken": "eyJhbGciOiJIUzI1NiIs...",
  "refreshToken": "eyJhbGciOiJIUzI1NiIs...",
  "expiresIn": 900,
  "tokenType": "Bearer",
  "user": {
    "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "name": "NT Sistemas Web",
    "email": "admin@ntsistemas.com",
    "role": "admin"
  }
}
```

---

#### `POST /api/v1/admin/auth/refresh`

**Request:** `{ "refreshToken": "eyJ..." }`

**Response `200`:** `{ "accessToken": "eyJ...", "expiresIn": 900, "tokenType": "Bearer" }`

---

#### `POST /api/v1/admin/auth/logout`

**Request:** `{ "refreshToken": "eyJ..." }`  
**Response `204`:** sem body

---

#### `GET /api/v1/admin/auth/me`

**Response `200`:**
```json
{
  "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "name": "NT Sistemas Web",
  "email": "admin@ntsistemas.com",
  "role": "admin",
  "active": true,
  "lastLoginAt": "2026-07-07T22:00:00.000Z"
}
```

---

### 7.3 Dashboard

#### `GET /api/v1/admin/dashboard/stats?months=6`

**Response `200`:**
```json
{
  "metrics": {
    "totalLeads": 42,
    "newLeads": 6,
    "inProgress": 3,
    "replied": 8,
    "closed": 25,
    "newLeadsThisMonth": 3,
    "conversionRate": 71
  },
  "leadsByMonth": [
    { "month": "Jan", "leads": 2 },
    { "month": "Fev", "leads": 4 },
    { "month": "Jun", "leads": 9 }
  ],
  "conversionByMonth": [
    { "month": "Jan", "taxa": 40 },
    { "month": "Jun", "taxa": 71 }
  ],
  "recentLeads": [{
    "id": "7c9e6679-7425-40de-944b-e07fc1f90ae7",
    "name": "Rodrigo Almeida",
    "email": "rodrigo@fintech-xp.com.br",
    "company": "FinTech XP",
    "painPoints": ["Legacy System Rescue"],
    "stack": "Java 8, Spring MVC",
    "budget": "R$ 50k–100k",
    "message": "...",
    "date": "2026-06-28",
    "status": "new"
  }]
}
```

---

### 7.4 Leads admin

#### `GET /api/v1/admin/leads?search=&status=all&page=1&limit=20&sort=createdAt:desc`

**Response `200`:**
```json
{
  "data": [{
    "id": "7c9e6679-7425-40de-944b-e07fc1f90ae7",
    "name": "Rodrigo Almeida",
    "email": "rodrigo@fintech-xp.com.br",
    "company": "FinTech XP",
    "painPoints": ["Legacy System Rescue", "Architectural Redesign"],
    "painPointIds": ["legacy", "arch"],
    "stack": "Java 8, Spring MVC, Oracle DB",
    "budget": "R$ 50k–100k",
    "message": "Sistema de pagamentos com 8 anos sem refatoração...",
    "lang": "pt",
    "status": "new",
    "date": "2026-06-28",
    "createdAt": "2026-06-28T14:30:00.000Z",
    "updatedAt": "2026-06-28T14:30:00.000Z",
    "assignedTo": null
  }],
  "total": 42,
  "page": 1,
  "limit": 20,
  "totalPages": 3
}
```

---

#### `GET /api/v1/admin/leads/:id`

**Response `200`:**
```json
{
  "id": "7c9e6679-7425-40de-944b-e07fc1f90ae7",
  "name": "Rodrigo Almeida",
  "email": "rodrigo@fintech-xp.com.br",
  "company": "FinTech XP",
  "painPoints": ["Legacy System Rescue"],
  "painPointIds": ["legacy"],
  "stack": "Java 8, Spring MVC",
  "budget": "R$ 50k–100k",
  "message": "...",
  "lang": "pt",
  "status": "new",
  "source": "website",
  "date": "2026-06-28",
  "createdAt": "2026-06-28T14:30:00.000Z",
  "notes": [{
    "id": "note-uuid",
    "content": "Cliente contactado por e-mail.",
    "author": { "id": "user-uuid", "name": "NT Sistemas Web" },
    "createdAt": "2026-06-29T09:00:00.000Z"
  }]
}
```

---

#### `PATCH /api/v1/admin/leads/:id`

**Request:**
```json
{ "status": "in_progress", "assignedTo": "a1b2c3d4-e5f6-7890-abcd-ef1234567890" }
```

**Response `200`:** lead atualizado (mesmo formato do item na listagem)

---

#### `POST /api/v1/admin/leads/:id/notes`

**Request:** `{ "content": "Agendar call técnica para quinta." }`

**Response `201`:**
```json
{
  "id": "note-uuid",
  "leadId": "7c9e6679-7425-40de-944b-e07fc1f90ae7",
  "content": "Agendar call técnica para quinta.",
  "author": { "id": "user-uuid", "name": "NT Sistemas Web" },
  "createdAt": "2026-06-29T11:00:00.000Z"
}
```

---

#### `GET /api/v1/admin/leads/export?format=csv&status=all`

**Response `200`:** `Content-Type: text/csv`

```csv
id,name,email,company,painPoints,stack,budget,status,lang,createdAt
7c9e6679-...,Rodrigo Almeida,rodrigo@...,FinTech XP,"legacy,arch",Java 8...,R$ 50k–100k,new,pt,2026-06-28T14:30:00.000Z
```

---

### 7.5 Settings admin

#### `GET /api/v1/admin/settings`

**Response `200`:**
```json
{
  "site": { "available": true, "defaultLang": "pt", "maintenanceMode": false },
  "contact": { "responseSlaHours": 48, "notificationEmail": "leads@ntsistemasweb.com.br" },
  "social": {
    "github": "https://github.com/ntsistemasweb",
    "linkedin": "https://linkedin.com/company/ntsistemasweb",
    "email": "contato@ntsistemasweb.com.br"
  },
  "seo": {
    "robots": "index, follow",
    "title": { "pt": "NT Sistemas Web", "en": "NT Sistemas Web" },
    "description": { "pt": "...", "en": "..." },
    "googleAnalyticsId": "G-XXXXXXXXXX"
  },
  "notifications": {
    "emailOnNewLead": true,
    "weeklyDigest": true,
    "staleLeadAlert": false,
    "staleLeadHours": 48,
    "slackWebhookUrl": null
  },
  "admin": {
    "profile": { "name": "NT Sistemas Web", "email": "admin@ntsistemas.com" }
  }
}
```

---

#### `PUT /api/v1/admin/settings`

**Request (parcial):**
```json
{
  "notifications": { "emailOnNewLead": true, "staleLeadAlert": true },
  "admin": { "profile": { "name": "NT Sistemas Web", "email": "admin@ntsistemas.com" } }
}
```

**Response `200`:** objeto completo de settings

---

### 7.6 CMS admin

#### `GET /api/v1/admin/content`

**Response `200`:**
```json
{
  "sections": [{
    "section": "hero",
    "langs": {
      "pt": { "status": "published", "version": 3, "updatedAt": "2026-06-20T10:00:00.000Z" },
      "en": { "status": "published", "version": 2, "updatedAt": "2026-06-18T10:00:00.000Z" }
    }
  }]
}
```

---

#### `GET /api/v1/admin/content/:section?lang=pt&status=draft`

Seções: `nav` | `hero` | `terminal` | `services` | `infrastructure` | `profile` | `contact` | `footer`

**Response `200`:**
```json
{
  "section": "hero",
  "lang": "pt",
  "status": "draft",
  "version": 4,
  "data": {
    "badge": "Disponível para novos projetos",
    "headline1": "Modernizando",
    "stats": [{ "value": "10+", "label": "Anos de Engenharia" }]
  },
  "updatedAt": "2026-07-02T12:00:00.000Z"
}
```

---

#### `PUT /api/v1/admin/content/:section?lang=pt`

**Request:** `{ "data": { "badge": "...", "headline1": "..." } }`  
**Response `200`:** seção salva como draft (version incrementada)

---

#### `POST /api/v1/admin/content/:section/publish?lang=pt`

**Response `200`:**
```json
{ "section": "hero", "lang": "pt", "status": "published", "version": 5, "publishedAt": "2026-07-02T18:05:00.000Z" }
```

---

#### `GET /api/v1/admin/content/:section/history?lang=pt&page=1&limit=10`

**Response `200`:**
```json
{
  "data": [
    { "version": 5, "status": "published", "createdBy": { "name": "NT Sistemas Web" }, "createdAt": "2026-07-02T18:05:00.000Z" }
  ],
  "total": 5,
  "page": 1,
  "limit": 10
}
```

---

### 7.7 Portfólio admin

#### `POST /api/v1/admin/portfolio`

**Request:**
```json
{
  "slug": "modernizacao-fintech-xp",
  "client": "FinTech XP",
  "industry": "Fintech",
  "techStack": ["Java", "Spring Boot 3", "Kubernetes"],
  "coverImageId": "media-uuid",
  "featured": true,
  "sortOrder": 1,
  "published": false,
  "translations": {
    "pt": {
      "title": "Modernização de Plataforma Fintech",
      "summary": "...",
      "challenge": "...",
      "solution": "...",
      "results": "..."
    },
    "en": {
      "title": "Fintech Platform Modernization",
      "summary": "...",
      "challenge": "...",
      "solution": "...",
      "results": "..."
    }
  }
}
```

**Response `201`:** case criado

---

#### `PUT /api/v1/admin/portfolio/:id`

**Request:** partial update (mesmos campos do POST)  
**Response `200`:** case atualizado

---

#### `DELETE /api/v1/admin/portfolio/:id`

**Response `204`**

---

#### `PATCH /api/v1/admin/portfolio/:id/publish`

**Request:** `{ "published": true }`  
**Response `200`:** `{ "id": "...", "published": true, "publishedAt": "..." }`

---

#### `PATCH /api/v1/admin/portfolio/reorder`

**Request:**
```json
{
  "items": [
    { "id": "550e8400-...", "sortOrder": 1 },
    { "id": "660e8400-...", "sortOrder": 2 }
  ]
}
```

**Response `200`:** `{ "updated": 2 }`

---

### 7.8 Mídia admin

#### `POST /api/v1/admin/media/upload`

**Request:** `multipart/form-data` — campo `file` (jpeg/png/webp, max 5MB)

**Response `201`:**
```json
{
  "id": "media-uuid",
  "filename": "cover-a1b2c3.jpg",
  "originalName": "cover.jpg",
  "mimeType": "image/jpeg",
  "sizeBytes": 245760,
  "url": "https://cdn.ntsistemasweb.com.br/media/cover-a1b2c3.jpg",
  "thumbnailUrl": "https://cdn.ntsistemasweb.com.br/media/thumb/cover-a1b2c3.jpg",
  "createdAt": "2026-07-02T18:00:00.000Z"
}
```

---

#### `GET /api/v1/admin/media?page=1&limit=20`

**Response `200`:** `{ "data": [...], "total": 15, "page": 1, "limit": 20 }`

---

#### `DELETE /api/v1/admin/media/:id`

**Response `204`** ou `409` se arquivo em uso

---

### 7.9 Tabela resumo

| Endpoint | Request | Response |
|----------|---------|----------|
| `GET /health` | — | `HealthResponse` |
| `GET /public/content` | query: `lang` | `PublicContentResponse` |
| `GET /public/settings` | — | `PublicSettingsResponse` |
| `GET /public/seo` | query: `lang` | `SeoResponse` |
| `GET /public/portfolio` | query: `lang`, `page` | `Paginated<PortfolioCard>` |
| `GET /public/portfolio/:slug` | query: `lang` | `PortfolioDetail` |
| `POST /public/leads` | `CreateLeadRequest` | `CreateLeadResponse` |
| `POST /admin/auth/login` | `LoginRequest` | `AuthResponse` |
| `POST /admin/auth/refresh` | `{ refreshToken }` | `{ accessToken, expiresIn }` |
| `POST /admin/auth/logout` | `{ refreshToken }` | `204` |
| `GET /admin/auth/me` | — | `AdminUser` |
| `GET /admin/dashboard/stats` | query: `months` | `DashboardStatsResponse` |
| `GET /admin/leads` | query: filtros | `Paginated<Lead>` |
| `GET /admin/leads/:id` | — | `LeadDetail` |
| `PATCH /admin/leads/:id` | `UpdateLeadRequest` | `Lead` |
| `POST /admin/leads/:id/notes` | `{ content }` | `LeadNote` |
| `GET /admin/leads/export` | query: `format` | CSV |
| `GET/PUT /admin/settings` | `Partial<Settings>` | `AdminSettingsResponse` |
| `GET/PUT /admin/content/:section` | `UpdateContentRequest` | `ContentSection` |
| `POST /admin/content/:section/publish` | — | `PublishContentResponse` |
| CRUD `/admin/portfolio` | `CreatePortfolioRequest` | `PortfolioCase` |
| `POST /admin/media/upload` | `multipart/form-data` | `MediaFile` |

---

## 8. Modelo de dados (banco)

```sql
users (id UUID PK, email, password_hash, name, role, active, last_login_at, created_at, updated_at)

leads (
  id UUID PK, name, email, company, budget, stack, message,
  pain_points JSONB, lang, status DEFAULT 'new',
  assigned_to UUID FK users, ip_hash, source DEFAULT 'website',
  created_at, updated_at
)

lead_notes (id UUID PK, lead_id FK, user_id FK, content, created_at)

content_sections (id, section, lang, data JSONB, status, version, published_at, updated_at)
content_versions (id, section, lang, data JSONB, version, created_by, created_at)

portfolio_cases (id, slug UNIQUE, client, industry, cover_image_id FK, tech_stack JSONB,
  featured, sort_order, published, published_at, created_at, updated_at)
portfolio_case_translations (id, case_id FK, lang, title, summary, challenge, solution, results, testimonial JSONB)

media (id, filename, mime_type, size_bytes, url, thumbnail_url, uploaded_by FK, created_at)
settings (key PK, value JSONB, updated_by FK, updated_at)
audit_logs (id, user_id FK, action, entity_type, entity_id, metadata JSONB, created_at)
refresh_tokens (id, user_id FK, token_hash, expires_at, revoked, created_at)
```

---

## 9. Segurança e infra

### Segurança

- Rate limit: leads 3/h por IP; login 5 tentativas / 15 min
- CAPTCHA (Turnstile) no formulário
- JWT access 15 min + refresh 7 dias
- Remover credenciais demo de `AdminLogin.tsx`
- IP armazenado como hash (LGPD)

### Variáveis de ambiente

**API (Spring Boot):**
```env
SPRING_DATASOURCE_URL=postgresql://...
SPRING_DATA_REDIS_URL=redis://...
JWT_SECRET=...
JWT_REFRESH_SECRET=...
AWS_SES_REGION=...
LEAD_NOTIFICATION_EMAIL=leads@ntsistemasweb.com.br
S3_BUCKET=ntsistemasweb-media
CAPTCHA_SECRET=...
CORS_ORIGINS=https://ntsistemasweb.com.br
SERVER_PORT=8080
```

**Frontend:**
```env
VITE_API_URL=https://ntsistemasweb.com.br/api
VITE_CAPTCHA_SITE_KEY=...
```

---

## 10. Gaps no frontend

| # | Gap | Arquivo | Ação |
|---|-----|---------|------|
| 1 | Formulário mock | `Home.tsx` | `POST /public/leads` + incluir `painPoints` |
| 2 | Portfólio ausente | `Home.tsx` | Criar seção `#portfolio` |
| 3 | Auth fake | `AdminLogin.tsx` | JWT real; remover demo credentials |
| 4 | Guard fraco | `AdminDashboard.tsx` | `GET /auth/me` |
| 5 | Leads mock | `AdminDashboard.tsx` | `GET /admin/leads` |
| 6 | Stats mock | `AdminDashboard.tsx` | `GET /admin/dashboard/stats` |
| 7 | Settings fake | `AdminDashboard.tsx` | `PUT /admin/settings` |
| 8 | Social links `#` | `Home.tsx` Footer | `GET /public/settings` |
| 9 | Sem `src/services/api.ts` | — | Criar client HTTP |

---

## 11. Fases de implementação

### Fase 1 — MVP (2–3 semanas)
- Spring Boot + PostgreSQL + Redis
- `POST /public/leads` + email + CAPTCHA
- Auth JWT + CRUD leads admin
- Integrar `Home.tsx`, `AdminLogin.tsx`, `AdminDashboard.tsx`
- Deploy API no K8s (porta 8080)

### Fase 2 — Settings + SEO (1–2 semanas)
- Settings públicas/admin
- Remover `noindex` do `index.html`
- Sitemap / robots dinâmicos

### Fase 3 — CMS (2–3 semanas)
- Seed do conteúdo de `Home.tsx`
- `GET /public/content` + editor admin

### Fase 4 — Portfólio (2 semanas)
- CRUD cases + upload mídia
- Seção `#portfolio` no site

### Fase 5 — Polimento
- Export CSV, audit log, Slack, 2FA, Sentry, testes E2E

---

## Conclusão

O frontend já tem **login**, **dashboard** e **formulário** prontos na UI — falta o backend Spring Boot conectando tudo. Prioridade: **leads + auth** (Fase 1).
