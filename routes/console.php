<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/*
| Cria (ou atualiza a senha de) um administrador do painel.
| php artisan site:admin voce@ntsistemasweb.com.br
*/
Artisan::command('site:admin {email} {--name=}', function (string $email) {
    if (Validator::make(['email' => $email], ['email' => 'required|email'])->fails()) {
        $this->error('E-mail inválido.');

        return 1;
    }

    $user = User::firstWhere('email', $email);
    $name = $this->option('name') ?: ($user?->name ?? text('Nome', default: 'Administrador', required: true));
    $secret = password('Senha (mínimo 8 caracteres)', required: true, validate: fn (string $value) => strlen($value) < 8 ? 'Use pelo menos 8 caracteres.' : null);

    User::updateOrCreate(['email' => $email], [
        'name' => $name,
        'password' => $secret,
        'role' => UserRole::Admin,
        'status' => UserStatus::Ativo,
        'email_verified_at' => now(),
    ]);

    $this->info(($user ? 'Senha atualizada' : 'Administrador criado').": {$email}. Acesse /admin.");

    return 0;
})->purpose('Cria um administrador do painel ou troca a senha dele');
