# Entrelinhas · Biblioteca

Sistema web responsivo para consulta de catálogo e gestão de livros, usuários e empréstimos. Construído com Laravel 12 e Blade.

## Instalação local

```bash
composer install
copy .env.example .env
php artisan key:generate
# Configure ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding
php artisan migrate --seed
php artisan serve
```

O projeto está configurado para SQLite. Se necessário, crie `database/database.sqlite` antes da migração. O frontend inclui uma folha de estilos pública de fallback; para compilar os assets via Vite, instale as dependências Node e execute `npm run build`.

## Administrador

Para criar o administrador inicial, defina `ADMIN_EMAIL` e `ADMIN_PASSWORD` no arquivo `.env` antes de executar `php artisan migrate --seed`. Use uma senha forte e exclusiva. Se ambas as variáveis estiverem vazias, o seeder não cria uma conta administrativa; se apenas uma estiver definida, a execução falha com uma mensagem explícita.

O seeder cadastra oito livros de exemplo. Leitores podem criar sua própria conta pela página de entrada.

## Recursos

- Catálogo com busca por título/autor e filtro por categoria.
- Painel do leitor com acesso restrito aos próprios empréstimos.
- Gestão administrativa de livros, usuários e empréstimos.
- Controle transacional de exemplares disponíveis, devoluções e atrasos.
- Página institucional da equipe e layout adaptável a telas menores.
