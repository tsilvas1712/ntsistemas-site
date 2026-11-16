<?php

/*
|--------------------------------------------------------------------------
| Painel administrativo
|--------------------------------------------------------------------------
| Itens do menu lateral.
| - "route": nome da rota para onde o item aponta.
| - "active": padrão(ões) de nome de rota que deixam o item ativo.
| - "icon": nome de um ícone Lucide (https://lucide.dev/icons).
*/

return [

    'nav' => [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'layout-dashboard'],
        ['label' => 'Contatos', 'route' => 'contacts.index', 'active' => 'contacts.*', 'icon' => 'inbox', 'badge' => 'new_contacts'],
        ['label' => 'Acessos', 'route' => 'analytics.index', 'active' => 'analytics.*', 'icon' => 'activity'],
        ['label' => 'Ajustes do site', 'route' => 'settings', 'active' => 'settings', 'icon' => 'sliders-horizontal'],
        ['label' => 'Usuários', 'route' => 'users.index', 'active' => 'users.*', 'icon' => 'users'],
    ],

    // Senha atribuída a usuários criados pelo painel (o formulário não tem campo de senha).
    'default_password' => env('ADMIN_DEFAULT_PASSWORD', 'password'),

];
