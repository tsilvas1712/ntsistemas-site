# Plano de melhorias · site NT Sistemas Web

Validação feita em 20/11/2026 em https://ntsistemasweb.com.br (celular e desktop) e no código. As mesmas tarefas estão no Notion, em **NT Sistemas Web › Plano de ação** (filtro Projeto = Site NT Sistemas Web).

## O que está bom

- Título e meta description fortes, falando da dor ("Sistema travando ou sem ninguém para mexer nele?").
- `canonical`, `og:title`, `og:description`, `og:locale` e `twitter:card` presentes.
- JSON-LD `ProfessionalService` com e-mail e telefone.
- Formulário de contato em etapas, WhatsApp e e-mail visíveis, painel /admin com contatos e acessos (já registra `utm_source` das visitas).
- Bom no celular (sem rolagem lateral, ~140 KB) e com HTTPS + HSTS.
- Seção do NT Omni como prova de produto próprio em produção.

## P1 · urgente

1. **og:image**: o link aparece sem imagem nos posts do LinkedIn e no WhatsApp, os dois canais principais. Gerar uma imagem de 1200×630 (logo + "Sistema travando? Assumimos, corrigimos e modernizamos.") e adicionar `og:image` e `twitter:image` em `resources/views/partials/head.blade.php`. Testar no LinkedIn Post Inspector.

## P2 · importante

2. **sitemap.xml**: hoje `/sitemap.xml` redireciona para a home. Criar a rota, colocar `Sitemap:` no `robots.txt` e cadastrar no Google Search Console.
3. **Menos jargão técnico.** O público é o dono de PME com sistema em Delphi ou Access, mas boa parte da página fala com engenheiros: "Kubernetes", "Terraform", "Staff/Principal Engineer", "Clean Code", "SOLID e DRY", "pipeline CI/CD". O próprio posicionamento promete "sem jargão".
   Trocar os blocos "Rigor de engenharia" e "Você entrega código" por resultados para o dono (sistema que não para, backup testado, fala direto com quem programa). Deixar a stack numa seção recolhida "Para equipes técnicas".
4. **Seção "Quem sou" com foto, nome e LinkedIn.** A promessa é "você fala direto com quem programa", mas o site não mostra quem é.
5. **Páginas de serviço para o Google** (o site é de uma página só): `/migracao-de-sistemas-desktop-para-web`, `/manutencao-de-sistemas`, `/diagnostico-de-sistema`. Cada uma com título, description, dor, como funciona, FAQ e CTA (SEO Prático: cauda longa e estrutura do site).
6. **JSON-LD mais completo**: cidade e UF (`address`, `areaServed`), `sameAs` com LinkedIn, Instagram e Perfil da Empresa no Google, e um `FAQPage` com as perguntas da página.

## P3 · quando der

7. **Guardar a origem (UTM) também no contato do formulário**, não só na visita, e mostrar no detalhe do contato no /admin.
8. **Casos reais** (quando houver o primeiro) e uma página "Como é o diagnóstico" com um relatório de exemplo anonimizado.

## Fora do site

- Criar o Perfil da Empresa no Google (texto pronto no Notion, documento "Plano de 4 semanas").
- Criar a página da empresa no LinkedIn só para o logo aparecer na sua experiência.
