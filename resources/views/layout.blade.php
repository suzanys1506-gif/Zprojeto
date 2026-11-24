<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Entrelinhas — Biblioteca')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css','resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ route('home') }}"><span class="brandmark">e</span><span>entrelinhas<small>biblioteca</small></span></a>
    <nav><a href="{{ route('catalog') }}">Catálogo</a><a href="{{ route('team') }}">Equipe</a>@auth <a href="{{ route('dashboard') }}">Painel</a> @endauth</nav>
    <div class="nav-actions">
        @auth
            <span class="user-chip">{{ auth()->user()->name }}</span>
            <form method="post" action="{{ route('logout') }}">@csrf <button class="text-button">Sair</button></form>
        @else
            <a class="login-link" href="{{ route('login') }}">Entrar</a>
            <a class="button button-dark small" href="{{ route('login') }}#cadastro">Criar conta <span>↗</span></a>
        @endauth
    </div>
</header>
<main>
    @if(session('success')) <div class="flash success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="flash error">{{ $errors->first() }}</div> @endif
    @yield('content')
</main>
<footer>
    <a class="brand footer-brand" href="{{ route('home') }}"><span class="brandmark">e</span><span>entrelinhas<small>biblioteca</small></span></a>
    <span>Um lugar para cada história.</span><span>© {{ date('Y') }} Entrelinhas</span>
</footer>
</body>
</html>
