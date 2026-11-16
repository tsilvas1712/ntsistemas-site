<?php

namespace App\Enums;

/**
 * Perfis de acesso (equivalente a USER_ROLES da versão React).
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Editor => 'Editor',
            self::Viewer => 'Visualizador',
        };
    }

    /** @return array<string, string> valor => rótulo, para selects */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
