<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Dados de desenvolvimento: um admin (admin@ntsistemasweb.test / password) e contatos de exemplo.
 * Em produção não roda: crie o acesso com "php artisan site:admin seu@email".
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('DatabaseSeeder não roda em produção. Use: php artisan site:admin seu@email');

            return;
        }

        User::updateOrCreate(['email' => 'admin@ntsistemasweb.test'], [
            'name' => 'Thiago Silva',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'ativo',
            'email_verified_at' => now(),
        ]);

        $contacts = [
            ['Rodrigo Almeida', 'rodrigo@fintech-xp.com.br', '(11) 98888-1001', 'FinTech XP', ['legacy', 'arch'], 'Java 8, Spring MVC, Oracle DB, JBoss', 'R$ 50k–100k', 'Sistema de pagamentos com 8 anos sem refatoração, mais de 40 mil linhas de código legado. Precisamos migrar para microsserviços.', 'novo', 2],
            ['Camila Torres', 'camila@logistica-br.com', '(51) 99777-2002', 'LogísticaBR', ['bugs'], 'Node.js, MongoDB, AWS Lambda', 'R$ 20k–50k', 'API de rastreamento de fretes caindo em produção nos dias de pico. Nenhuma stack trace útil. Precisamos de diagnóstico urgente.', 'em_atendimento', 5],
            ['Felipe Nascimento', 'felipe@saasplatform.io', null, 'SaaS Platform', ['mvp'], 'A definir', 'R$ 30k–60k', 'Quero construir um SaaS B2B para gestão de obras. Tenho o produto mapeado, preciso de uma equipe técnica para executar.', 'respondido', 12],
            ['Ana Paula Ferreira', 'ana@healthtech.med.br', '(48) 99666-3003', 'HealthTech Med', ['security', 'devops'], 'Python Django, PostgreSQL, Heroku', 'R$ 15k–30k', 'Aplicação médica que precisa de compliance LGPD completo e migração do Heroku para infraestrutura controlada.', 'novo', 20],
            ['Gustavo Moraes', 'gmoraes@ecommerce360.com', '(21) 99555-4004', 'eCommerce 360', ['legacy', 'bugs'], 'PHP 5.6, CodeIgniter, MySQL, cPanel', 'R$ 80k–150k', 'E-commerce com 500 mil produtos em PHP 5.6. A loja cai toda Black Friday. Precisamos modernizar com urgência.', 'fechado', 45],
            ['Mariana Souza', 'mariana@edtech.com.br', null, 'EdTech Brasil', ['mvp', 'devops'], 'React, precisa de backend', 'R$ 40k–70k', 'Plataforma de cursos online com gamificação. Protótipo no Figma pronto. Preciso de time full-stack para construir do zero.', 'descartado', 80],
        ];

        foreach ($contacts as [$name, $email, $phone, $company, $services, $stack, $budget, $message, $status, $daysAgo]) {
            Contact::updateOrCreate(['email' => $email], [
                'name' => $name, 'phone' => $phone, 'company' => $company, 'services' => $services, 'stack' => $stack,
                'budget' => $budget, 'message' => $message, 'status' => $status,
                'created_at' => now()->subDays($daysAgo), 'updated_at' => now()->subDays($daysAgo),
            ]);
        }
    }
}
