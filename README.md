# CRRS — Site da coleção de camisetas

Site institucional/catálogo minimalista para apresentar a coleção da marca **CRRS**, com botão de compra que abre o WhatsApp (ou Instagram) já com a mensagem pronta. Inclui um **painel administrativo** para gerenciar os produtos sem precisar de código.

Desenvolvido em **PHP puro + SQLite** — sem WordPress, sem Wix, sem frameworks pesados. Roda em qualquer servidor Linux com PHP (Apache ou Nginx).

---

## ✨ Recursos

- Página inicial com identidade da marca (logo, cor de destaque, hero)
- Catálogo em grade (foto, nome, preço, tamanhos)
- Página de produto com seletor de tamanho + campo de observação
- Botão **Comprar** → abre WhatsApp/Instagram com mensagem pré-preenchida (produto, tamanho, observação)
- Páginas **Sobre** e **Contacto**
- **Painel admin** (`/admin`): adicionar / editar / ocultar / excluir produtos, upload de fotos, e todas as configurações da marca — tudo sem código
- Responsivo (desktop + mobile), leve e otimizado para SEO on-page (title, meta description, Open Graph)
- Lazy-loading de imagens, cache e compressão via `.htaccess`

---

## 🔑 Acesso ao painel

    URL:     https://SEU-DOMINIO/admin/
    Usuário: admin
    Senha:   crrs2026

> **Importante:** troque a senha no primeiro acesso em **Configurações → Alterar senha**.

---

## 🚀 Instalação (VPS Linux)

Requisitos: **PHP 8.0+** com extensões `pdo_sqlite` e `gd` (padrão na maioria dos servidores).

1. Envie todos os arquivos para a pasta do site (ex.: `/var/www/crrs`).
2. Garanta que as pastas `data/` e `uploads/` tenham permissão de escrita:

   ```bash
   chmod -R 775 data uploads
   ```

3. Aponte o domínio para a pasta e pronto — o banco de dados é criado automaticamente no primeiro acesso, já com produtos de demonstração.

### Apache
O arquivo `.htaccess` já vem configurado (cache, compressão, DirectoryIndex).

### Nginx
Bloco de exemplo:

```nginx
server {
    listen 80;
    server_name seudominio.com;
    root /var/www/crrs;
    index index.php;

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Protege o banco de dados
    location ~ ^/data/ { deny all; }
    # Impede execução de PHP em uploads
    location ~ ^/uploads/.*\.php$ { deny all; }
}
```

### Teste local
```bash
php -S localhost:8000
```
Abra http://localhost:8000

---

## 📁 Estrutura

```
index.php          Página inicial
catalogo.php       Catálogo
produto.php        Página do produto (?id=)
sobre.php          Página Sobre
contacto.php       Página Contacto
includes/          db.php, functions.php, header/footer
assets/            css + imagens de demonstração
admin/             Painel administrativo
data/              Banco SQLite (criado automaticamente — protegido)
uploads/           Fotos enviadas pelo painel
```

---

## ⚙️ Configurações (no painel)

Nome da marca, slogan, título/subtítulo do banner, cor de destaque, moeda, logo,
número do WhatsApp, @ do Instagram, canal de compra, texto do Sobre e do Contacto —
tudo editável em **Configurações**, sem tocar em código.
