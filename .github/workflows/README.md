# GitHub Actions Deploy (Kubernetes)

Pipeline principal: `.github/workflows/standart.yml`

## Fluxo

1. **quality** — instala dependências e valida o build Vite
2. **build-and-push-hml** — build do site, imagem Docker e push para GHCR
3. **build-and-push-prd** — build e push da imagem de produção (apenas push/`workflow_dispatch`)
4. **deploy-hml** — aplica manifests em homologação
5. **deploy-prd** — aplica manifests em produção (com aprovação manual)

## Triggers e ambientes

| Evento | Build tag (HML) | Build tag (PRD) | Deploy HML | Deploy PRD |
|---|---|---|---|---|
| PR aberto/atualizado → `main` | `sha-{commit}` da branch do PR | não executa | automático (código da branch) | não executa |
| Novo commit na branch com PR aberto | `sha-{commit}` | não executa | automático (atualiza HML) | não executa |
| Push na `main` (merge) | `hml` | `main` | automático | aguarda aprovação |
| `workflow_dispatch` | `hml` | `main` | automático | aguarda aprovação |

Em PRs, o pipeline usa o commit da **branch de origem** (`pull_request.head.sha`), não o código da `main`. Cada push na branch dispara o evento `synchronize` e republica a imagem com tag `sha-{commit}` antes do deploy em HML.

### Aprovação de produção

Configure no GitHub: **Settings → Environments → production → Required reviewers**.

O job `deploy-prd` só inicia após o deploy em HML concluir com sucesso e um revisor aprovar a execução.

## Recursos no cluster

### Homologação (HML)

| Recurso | Nome | Namespace |
|---|---|---|
| Namespace | `ntsistemas-web-hml` | — |
| Deployment | `hml-ntsistemas-web-app` | `ntsistemas-web-hml` |
| Service | `hml-ntsistemas-web-app` | `ntsistemas-web-hml` |
| Ingress | `hml-ntsistemas-web-app` | `ntsistemas-web-hml` |
| Host | `hml.ntsistemasweb.com.br` | — |
| Imagem (merge) | `ghcr.io/tsilvas1712/ntsistemas-site:hml` | — |
| Imagem (PR) | `ghcr.io/tsilvas1712/ntsistemas-site:sha-{commit}` | — |

Manifests: `k8s/hml/`

### Produção (PRD)

| Recurso | Nome | Namespace |
|---|---|---|
| Namespace | `ntsistemas-web` | — |
| Deployment | `ntsistemas-web-app` | `ntsistemas-web` |
| Service | `ntsistemas-web-app` | `ntsistemas-web` |
| Ingress | `ntsistemas-web-app` | `ntsistemas-web` |
| Host | `ntsistemasweb.com.br`, `www.ntsistemasweb.com.br` | — |
| Imagem | `ghcr.io/tsilvas1712/ntsistemas-site:main` | — |

Manifests: `k8s/`

## Stack de deploy

- **Build**: Vite 6 + React 19 (SPA estática)
- **Container**: nginx:1.27-alpine servindo `dist/`
- **Ingress**: Traefik com certResolver `letsencrypt`
- **Registry**: GHCR (`ghcr.io/tsilvas1712/ntsistemas-site`)

Script de deploy: `scripts/k8s-deploy.sh` (`bootstrap`, `deploy`, `all`)

## Secrets obrigatórios no GitHub

- `KUBE_CONFIG_DATA` — kubeconfig em texto puro ou base64
- `GHCR_PAT` — token com permissão de leitura de pacotes no GHCR

O `GITHUB_TOKEN` é usado automaticamente no push da imagem.

## Pré-requisitos no cluster (uma vez)

1. Garantir Traefik com `ingressClassName: traefik` e certResolver `letsencrypt`.
2. Apontar DNS para o IP do Ingress/Load Balancer (`185.245.182.25`):
   - `hml.ntsistemasweb.com.br` → A record
   - `ntsistemasweb.com.br` → A record
   - `www.ntsistemasweb.com.br` → A record (ou CNAME para `@`)
3. Criar environment `production` no GitHub com revisores obrigatórios.

## Verificação

```bash
# HML
kubectl get pods -n ntsistemas-web-hml -l app=hml-ntsistemas-web-app
kubectl logs deployment/hml-ntsistemas-web-app -n ntsistemas-web-hml --tail=100
kubectl rollout status deployment/hml-ntsistemas-web-app -n ntsistemas-web-hml

# PRD
kubectl get pods -n ntsistemas-web -l app=ntsistemas-web-app
kubectl logs deployment/ntsistemas-web-app -n ntsistemas-web --tail=100
kubectl rollout status deployment/ntsistemas-web-app -n ntsistemas-web
```
