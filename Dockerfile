FROM nginx:1.27-alpine
LABEL authors="thiago"

COPY nginx.conf /etc/nginx/conf.d/default.conf

# Build artifact (dist/) is produced by `npm run build` in the CI pipeline.
COPY dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
