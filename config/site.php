<?php

/*
| Ajustes do site. Os valores abaixo são os padrões; o que for salvo no painel
| (Admin › Ajustes do site) tem prioridade.
*/

return [

    // Domínio principal (sem www). Com ele definido, www.<domínio> redireciona para cá.
    'domain' => env('SITE_DOMAIN'),

    // Estatísticas de acesso do site (Admin › Acessos).
    'analytics' => [
        'enabled' => (bool) env('SITE_ANALYTICS', true),
        'retention_days' => (int) env('SITE_ANALYTICS_RETENTION_DAYS', 180),
    ],

    'defaults' => [
        'contact_email' => env('SITE_CONTACT_EMAIL', 'contato@ntsistemasweb.com.br'),
        'notification_email' => env('SITE_NOTIFICATION_EMAIL', env('SITE_CONTACT_EMAIL', 'contato@ntsistemasweb.com.br')),
        'whatsapp' => env('SITE_WHATSAPP') ?: '5551995492191',
        'github_url' => env('SITE_GITHUB_URL', 'https://github.com/tsilvas1712'),
        'linkedin_url' => env('SITE_LINKEDIN_URL', ''),
        'available' => '1',
        'response_hours' => '48',
    ],

];
