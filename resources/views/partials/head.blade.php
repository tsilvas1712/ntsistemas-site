<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>{{ filled($title ?? null) ? $title." · NT Sistemas Web" : "NT Sistemas Web · Painel" }}</title>
<meta name="robots" content="noindex" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
{{-- Tema do painel: claro por padrão; escuro se escolhido no botão do cabeçalho. --}}
<script>
    try { if (localStorage.getItem('admin-theme') === 'dark') document.documentElement.classList.add('dark'); } catch (e) {}
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
