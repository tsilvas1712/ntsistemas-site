<?php

namespace App\Models;

use App\Enums\ContactStatus;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'company', 'budget', 'stack', 'services', 'message', 'status', 'notes', 'ip', 'user_agent', 'replied_at'])]
class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    /** Serviços que o visitante pode marcar no formulário (chave => rótulo). */
    public const SERVICES = [
        'legacy' => 'Sistema travando ou sem ninguém para mexer',
        'bugs' => 'Bugs críticos e lentidão',
        'web' => 'Levar um sistema antigo para a web',
        'maintenance' => 'Manutenção mensal',
        'new' => 'Sistema novo sob medida',
        'devops' => 'Servidor, nuvem e deploy',
    ];

    /** Opções que saíram do formulário: só para exibir contatos antigos com o rótulo certo. */
    public const RETIRED_SERVICES = [
        'arch' => 'Redesign arquitetural',
        'mvp' => 'Novo MVP / greenfield',
        'security' => 'Hardening de segurança',
    ];

    protected $attributes = [
        'status' => 'novo',
    ];

    protected function casts(): array
    {
        return [
            'services' => 'array',
            'status' => ContactStatus::class,
            'replied_at' => 'datetime',
        ];
    }

    /** Rótulos dos serviços marcados. @return list<string> */
    public function serviceLabels(): array
    {
        return collect($this->services ?? [])
            ->map(fn (string $key) => self::SERVICES[$key] ?? self::RETIRED_SERVICES[$key] ?? $key)
            ->values()
            ->all();
    }

    public function initials(): string
    {
        return User::initialsFor($this->name);
    }

    /** Número só com dígitos para o link do WhatsApp (assume Brasil quando vier sem DDI). */
    public function whatsappNumber(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone);

        if (strlen($digits) < 10) {
            return null;
        }

        return strlen($digits) <= 11 ? '55'.$digits : $digits;
    }

    /** Busca por nome, e-mail, empresa ou telefone. */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('company', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }
}
