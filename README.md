# NT Sistemas Web

Site institucional e painel administrativo da NT Sistemas Web, em **Laravel 13 + Livewire 4 + Tailwind 4**.

- Visual no mesmo padrão do site do **NT Omni**, em modo escuro, com o índigo do AdminKit (`#4f46e5`).
- Logo em `resources/views/components/brand/mark.blade.php` (e `public/favicon.svg`).
- Painel baseado no **admin-kit-livewire**.

## Áreas

| URL | O que é |
|---|---|
| `/` | Site (serviços, infraestrutura, por que nós, produtos, perguntas e formulário de contato) |
| `/admin` | Dashboard com os números de contatos |
| `/admin/contatos` | Contatos do site: busca, filtro por status, exportação CSV, detalhe com status, observações, resposta por e-mail/WhatsApp e exclusão |
| `/admin/ajustes` | E-mail, WhatsApp, links, prazo de resposta e selo "Disponível", sem deploy |
| `/admin/usuarios` | Acessos ao painel (senha aleatória mostrada uma vez) |

O formulário de contato tem honeypot contra robôs, limite de 5 envios por hora por IP e envia aviso por e-mail para o endereço configurado.

## Rodando local

```bash
composer run setup        # instala, cria .env, SQLite, migrations + dados de exemplo e build
php artisan serve
```

Painel: `http://localhost:8000/admin` com `admin@ntsistemasweb.test` / `password` (só em dev).

Criar um admin: `php artisan site:admin voce@email.com`.

## Testes

```bash
php artisan test
```

## Deploy

Coolify (Docker Compose), igual ao NT Omni: veja [docs/deploy-coolify.md](docs/deploy-coolify.md).
