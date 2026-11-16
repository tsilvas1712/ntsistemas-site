<?php

namespace App\Livewire;

use App\Enums\ContactStatus;
use App\Models\Contact;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $byStatus = Contact::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $thisMonth = Contact::where('created_at', '>=', now()->startOfMonth())->count();

        return view('livewire.dashboard', [
            'today' => format_long_date(now()),
            'firstName' => str(auth()->user()?->name ?? 'Admin')->before(' '),
            'stats' => [
                ['title' => 'Novos', 'value' => (string) ($byStatus[ContactStatus::Novo->value] ?? 0), 'hint' => 'aguardando resposta', 'icon' => 'inbox', 'iconClass' => 'bg-indigo-500/15 text-indigo-700 dark:text-indigo-300', 'filter' => 'novo'],
                ['title' => 'Em atendimento', 'value' => (string) ($byStatus[ContactStatus::EmAtendimento->value] ?? 0), 'hint' => 'em conversa', 'icon' => 'messages-square', 'iconClass' => 'bg-amber-500/15 text-amber-700 dark:text-amber-300', 'filter' => 'em_atendimento'],
                ['title' => 'Fechados', 'value' => (string) ($byStatus[ContactStatus::Fechado->value] ?? 0), 'hint' => 'viraram projeto', 'icon' => 'handshake', 'iconClass' => 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300', 'filter' => 'fechado'],
                ['title' => 'Este mês', 'value' => (string) $thisMonth, 'hint' => 'contatos recebidos', 'icon' => 'calendar-days', 'iconClass' => 'bg-cyan-500/15 text-cyan-700 dark:text-cyan-300', 'filter' => 'todos'],
            ],
            'months' => $this->lastMonths(),
            'latest' => Contact::latest()->limit(6)->get(),
        ]);
    }

    /** Contatos por mês nos últimos 6 meses. @return list<array{label: string, total: int}> */
    protected function lastMonths(): array
    {
        $labels = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
        $start = now()->startOfMonth()->subMonths(5);

        $counts = Contact::where('created_at', '>=', $start)->pluck('created_at')
            ->countBy(fn (Carbon $date) => $date->format('Y-m'));

        return collect(range(0, 5))->map(function (int $i) use ($start, $counts, $labels) {
            $month = $start->copy()->addMonths($i);

            return ['label' => $labels[$month->month - 1], 'total' => (int) ($counts[$month->format('Y-m')] ?? 0)];
        })->all();
    }
}
