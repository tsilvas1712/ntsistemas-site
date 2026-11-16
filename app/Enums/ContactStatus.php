<?php

namespace App\Enums;

/** Situação de um contato recebido pelo site. */
enum ContactStatus: string
{
    case Novo = 'novo';
    case EmAtendimento = 'em_atendimento';
    case Respondido = 'respondido';
    case Fechado = 'fechado';
    case Descartado = 'descartado';

    public function label(): string
    {
        return match ($this) {
            self::Novo => 'Novo',
            self::EmAtendimento => 'Em atendimento',
            self::Respondido => 'Respondido',
            self::Fechado => 'Fechado',
            self::Descartado => 'Descartado',
        };
    }

    /** Classes Tailwind do badge (tema escuro). */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Novo => 'border-transparent bg-indigo-500/15 text-indigo-700 dark:text-indigo-300',
            self::EmAtendimento => 'border-transparent bg-amber-500/15 text-amber-700 dark:text-amber-300',
            self::Respondido => 'border-transparent bg-cyan-500/15 text-cyan-700 dark:text-cyan-300',
            self::Fechado => 'border-transparent bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
            self::Descartado => 'border-transparent bg-slate-500/15 text-slate-600 dark:text-slate-400',
        };
    }

    /** @return array<string, string> valor => rótulo */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
