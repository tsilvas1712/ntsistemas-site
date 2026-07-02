#!/usr/bin/env bash
set -euo pipefail

PLACEHOLDER_IMAGE_DEFAULT="ghcr.io/tsilvas1712/ntsistemas-site:hml"

usage() {
  cat <<'EOF'
Usage:
  k8s-deploy.sh bootstrap <namespace>
  k8s-deploy.sh deploy <namespace> <image> <manifests_dir> <deployment_name> [placeholder_image]
  k8s-deploy.sh all <namespace> <image> <manifests_dir> <deployment_name> [placeholder_image]

Requires GHCR_USERNAME and GHCR_PASSWORD for bootstrap.
EOF
  exit 1
}

ACTION="${1:-}"
shift || usage

bootstrap() {
  local namespace="$1"

  kubectl create namespace "$namespace" --dry-run=client -o yaml | kubectl apply -f -

  kubectl create secret docker-registry ghcr-pull \
    --namespace "$namespace" \
    --docker-server=ghcr.io \
    --docker-username="$GHCR_USERNAME" \
    --docker-password="$GHCR_PASSWORD" \
    --dry-run=client -o yaml | kubectl apply -f -
}

deploy_app() {
  local namespace="$1"
  local image="$2"
  local manifests_dir="$3"
  local deployment_name="$4"
  local placeholder_image="${5:-$PLACEHOLDER_IMAGE_DEFAULT}"

  sed "s|${placeholder_image}|${image}|" "$manifests_dir/deployment.yaml" | kubectl apply -f -
  kubectl apply -f "$manifests_dir/service.yaml"
  kubectl apply -f "$manifests_dir/ingress.yaml"

  kubectl set image "deployment/${deployment_name}" "app=${image}" --namespace "$namespace"
  kubectl rollout status "deployment/${deployment_name}" --namespace "$namespace" --timeout=180s
}

all() {
  local namespace="$1"
  local image="$2"
  local manifests_dir="$3"
  local deployment_name="$4"
  local placeholder_image="${5:-$PLACEHOLDER_IMAGE_DEFAULT}"

  bootstrap "$namespace"
  deploy_app "$namespace" "$image" "$manifests_dir" "$deployment_name" "$placeholder_image"
}

case "$ACTION" in
  bootstrap)
    [ $# -eq 1 ] || usage
    bootstrap "$1"
    ;;
  deploy)
    [ $# -ge 4 ] || usage
    deploy_app "$1" "$2" "$3" "$4" "${5:-}"
    ;;
  all)
    [ $# -ge 4 ] || usage
    all "$1" "$2" "$3" "$4" "${5:-}"
    ;;
  *)
    usage
    ;;
esac
