<?php

use TailwindMerge\TailwindMerge;

if (! function_exists('cn')) {
    /**
     * Junta classes e resolve conflitos do Tailwind (ex.: "px-2" + "px-4" → "px-4").
     * Equivalente ao cn() das versões React/Vue.
     */
    function cn(?string ...$classes): string
    {
        return TailwindMerge::instance()->merge(...array_map(fn ($c) => (string) $c, $classes));
    }
}

if (! function_exists('format_long_date')) {
    /**
     * Formata uma data no estilo do painel: "Sexta, 6 nov 2026".
     * Equivalente ao formatLongDate() das versões React/Vue.
     */
    function format_long_date(DateTimeInterface $date): string
    {
        $weekdays = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        $months = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

        return sprintf(
            '%s, %d %s %d',
            $weekdays[(int) $date->format('w')],
            (int) $date->format('j'),
            $months[(int) $date->format('n') - 1],
            (int) $date->format('Y'),
        );
    }
}
