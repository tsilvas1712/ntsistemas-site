<?php

namespace App\Enums;

/**
 * Situação do usuário (equivalente a USER_STATUSES da versão React).
 */
enum UserStatus: string
{
    case Ativo = 'ativo';
    case Pendente = 'pendente';
    case Inativo = 'inativo';

    public function label(): string
    {
        return match ($this) {
            self::Ativo => 'Ativo',
            self::Pendente => 'Pendente',
            self::Inativo => 'Inativo',
        };
    }

    /** Classes Tailwind do badge de status. */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Ativo => 'border-transparent bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
            self::Pendente => 'border-transparent bg-amber-500/15 text-amber-700 dark:text-amber-300',
            self::Inativo => 'border-transparent bg-rose-500/15 text-rose-700 dark:text-rose-300',
        };
    }

    /** @return array<string, string> valor => rótulo, para selects */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
