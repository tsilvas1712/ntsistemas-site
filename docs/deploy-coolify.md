# Deploy da NT Sistemas Web no Coolify

Mesmo modelo do NT Omni: **Docker Compose** com o arquivo `docker-compose.coolify.yml` (serviços `app` + `postgres`).

## 1. DNS (registro.br)

Registros **A** apontando para o IP do servidor do Coolify:

| Nome | Tipo | Valor |
|---|---|---|
| *(vazio, a raiz)* | A | IP do servidor |
| `www` | A | IP do servidor |

Confira antes de seguir (o HTTPS só é emitido quando o DNS já aponta para o servidor):

```bash
dig +short ntsistemasweb.com.br
```

> Hoje `ntsistemasweb.com.br` mostra a página "This site is not available at the moment" do Coolify:
> o domínio aponta para o servidor, mas não está ligado a nenhum recurso saudável. O passo 3 resolve isso.

## 2. Repositório

Suba este projeto para um repositório no GitHub e dê acesso a ele no GitHub App do Coolify (**Sources**).

## 3. Recurso no Coolify

1. **Projects › + Add** → projeto **NT Sistemas Web** (ambiente *production*).
2. **+ New Resource › Private Repository (with GitHub App)**
   - Branch: `main`
   - Build Pack: **Docker Compose**
   - Arquivo: **`/docker-compose.coolify.yml`**
3. **Domínio do serviço `app`:**
   `https://ntsistemasweb.com.br:8080,https://www.ntsistemasweb.com.br:8080`
   (o `:8080` é a porta interna do container; `postgres` fica sem domínio).
4. Se houver um recurso antigo (o site React) usando esses domínios, **remova os domínios dele** antes.

## 4. Variáveis de ambiente

| Variável | Valor |
|---|---|
| `APP_KEY` | `php artisan key:generate --show` |
| `SITE_DOMAIN` | `ntsistemasweb.com.br` |
| `DB_PASSWORD` | senha forte (`openssl rand -hex 24`) |
| `SITE_CONTACT_EMAIL` | e-mail exibido no site |
| `SITE_NOTIFICATION_EMAIL` | quem recebe o aviso de novo contato |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | SMTP (Brevo, SES, Resend…). Sem isso o aviso vai só para o log, mas o contato aparece no painel |

E-mail, WhatsApp, links e o selo "Disponível" também podem ser trocados depois em **/admin › Ajustes do site**, sem deploy.

## 5. Deploy e primeiro acesso

Clique em **Deploy**. O container roda as migrations sozinho ao subir.

Depois, em **app › Terminal**:

```bash
php artisan site:admin voce@ntsistemasweb.com.br --name="Thiago Silva"   # pede a senha
```

O mesmo comando troca a senha de um admin existente.

## 6. Conferência

- [ ] `https://ntsistemasweb.com.br` abre o site.
- [ ] `https://www.ntsistemasweb.com.br` redireciona para o domínio sem `www`.
- [ ] Enviar o formulário de contato → o contato aparece em `https://ntsistemasweb.com.br/admin/contatos`.
- [ ] Com SMTP configurado, chega o e-mail de aviso.

## 7. Backup

Agende o backup do serviço `postgres` (Coolify › Backups, destino S3). É lá que ficam os contatos.

## Problemas comuns

| Sintoma | Causa provável |
|---|---|
| "This site is not available at the moment" | Domínio sem o `:8080`, colocado no serviço `postgres`, container *unhealthy*, ou alterado sem **Redeploy** |
| Erro 419 ao entrar no painel | Acesso por `http://`. O cookie de sessão é só HTTPS |
| CSS sem carregar / links `http://` | `APP_URL`/`SITE_DOMAIN` errados |
