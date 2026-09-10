# For Champions — Site Reconstruído

Reconstrução do site **forchampions.com.br** em PHP puro, com compra via WhatsApp e painel admin.

## Demo online (GitHub Pages)

**https://rox351.github.io/forchampions-php/**

Versão estática para apresentação à empresa (sem PHP/MySQL). Inclui homepage, loja, produtos, páginas institucionais e FAQ.

Para regenerar a demo estática:

```powershell
docker exec -e STATIC_BASE=/forchampions-php fc-php-web php /var/www/html/scripts/build-github-pages.php
```

## Demo completa (Docker + PHP)

```powershell
git clone https://github.com/Rox351/forchampions-php.git
cd forchampions-php
copy .env.example .env
docker compose up -d --build
```

- **Site:** http://localhost:8081
- **Admin:** http://localhost:8081/admin/login.php
- **Login admin:** `admin` / `admin123`

## O que está incluído

- Homepage (banner, vitrine, FAQ, categorias)
- Loja, páginas institucionais e detalhe de produto
- Painel admin (produtos e configurações)
- Tema escuro fiel ao site original
- Imagens de produtos
- WhatsApp: `(51) 99188-6097`
- E-mail: `contato@forchampions.com.br`

## Estrutura

```
forchampions-php/
├── docs/            # Demo estatica (GitHub Pages)
├── public/          # Site PHP
├── src/             # Bootstrap, helpers, views
├── database/        # Schema inicial
└── scripts/         # Build da demo e importacao local
```

## Comandos úteis

```powershell
docker compose down
docker compose logs -f web
```

## Deploy cPanel

1. Envie `public/`, `src/` e `database/schema.sql`
2. Aponte o domínio para a pasta `public/`
3. Garanta permissão de escrita em `public/uploads/` e `public/assets/uploads/`
