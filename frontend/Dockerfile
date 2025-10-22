# Build stage
FROM node:22-alpine AS builder

WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build

# Production stage - minimal Alpine image just to hold the built files
FROM alpine:latest

WORKDIR /usr/share/nginx/html

COPY --from=builder /app/dist .

CMD ["tail", "-f", "/dev/null"]
