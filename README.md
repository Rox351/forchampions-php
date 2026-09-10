# For Champions — Site Reconstruído

Reconstrução do site **forchampions.com.br** em PHP puro, com compra via WhatsApp e painel admin.

## Demo rápida (Docker)

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
├── public/          # Site e assets
├── src/             # PHP (bootstrap, views, helpers)
├── database/        # Schema inicial
└── docker/          # Apache
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
