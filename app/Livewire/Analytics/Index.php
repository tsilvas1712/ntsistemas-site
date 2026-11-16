<?php

namespace App\Livewire\Analytics;

use App\Models\Contact;
use App\Models\Visit;
use App\Models\VisitClick;
use App\Support\UserAgent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Acessos')]
class Index extends Component
{
    use WithPagination;

    public const PERIODS = ['24h' => '24 horas', '7d' => '7 dias', '30d' => '30 dias', '90d' => '90 dias'];

    #[Url(except: '7d')]
    public string $period = '7d';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: false)]
    public bool $bots = false;

    public ?int $selected = null;

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'bots'], true)) {
            $this->resetPage();
        }
    }

    public function setPeriod(string $period): void
    {
        $this->period = array_key_exists($period, self::PERIODS) ? $period : '7d';
        $this->resetPage();
    }

    public function filterIp(string $ip): void
    {
        $this->search = $ip;
        $this->resetPage();
    }

    public function select(int $id): void
    {
        $this->selected = $id;
    }

    protected function since(): Carbon
    {
        return match ($this->period) {
            '24h' => now()->subDay(),
            '30d' => now()->subDays(29)->startOfDay(),
            '90d' => now()->subDays(89)->startOfDay(),
            default => now()->subDays(6)->startOfDay(),
        };
    }

    /** Acessos do período (sem robôs, a não ser que o filtro peça). */
    protected function visits(): Builder
    {
        return Visit::query()
            ->where('created_at', '>=', $this->since())
            ->when(! $this->bots, fn ($q) => $q->where('is_bot', false));
    }

    protected function clicks(): Builder
    {
        return VisitClick::query()->whereIn('visit_id', $this->visits()->select('id'));
    }

    #[Computed]
    public function stats(): array
    {
        $views = $this->visits()->count();
        $visitors = $this->visits()->distinct()->count('visitor');
        $mobile = $this->visits()->whereIn('device', ['mobile', 'tablet'])->count();
        $contacts = Contact::where('created_at', '>=', $this->since())->count();

        return [
            ['title' => 'Visualizações', 'value' => number_format($views, 0, ',', '.'), 'hint' => 'páginas carregadas', 'icon' => 'eye', 'iconClass' => 'bg-indigo-500/15 text-indigo-700 dark:text-indigo-300'],
            ['title' => 'Visitantes únicos', 'value' => number_format($visitors, 0, ',', '.'), 'hint' => $mobile && $views ? round($mobile / $views * 100).'% no celular' : 'por IP + navegador', 'icon' => 'users', 'iconClass' => 'bg-cyan-500/15 text-cyan-700 dark:text-cyan-300'],
            ['title' => 'Cliques', 'value' => number_format($this->clicks()->count(), 0, ',', '.'), 'hint' => 'em links e botões', 'icon' => 'mouse-pointer-click', 'iconClass' => 'bg-amber-500/15 text-amber-700 dark:text-amber-300'],
            ['title' => 'Conversão', 'value' => $visitors ? number_format($contacts / $visitors * 100, 1, ',', '.').'%' : '—', 'hint' => $contacts.' '.str('contato')->plural($contacts).' no período', 'icon' => 'target', 'iconClass' => 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300'],
        ];
    }

    /** Visualizações por hora (24h) ou por dia. @return list<array{label: string, title: string, total: int}> */
    #[Computed]
    public function series(): array
    {
        $hourly = $this->period === '24h';
        $counts = $this->visits()->toBase()->pluck('created_at')
            ->countBy(fn ($date) => Carbon::parse($date)->format($hourly ? 'Y-m-d H' : 'Y-m-d'));

        $start = $hourly ? now()->subHours(23)->startOfHour() : $this->since();
        $steps = $hourly ? 24 : (int) $start->diffInDays(now()->startOfDay()) + 1;

        return collect(range(0, $steps - 1))->map(function (int $i) use ($start, $hourly, $counts) {
            $at = $hourly ? $start->copy()->addHours($i) : $start->copy()->addDays($i);
            $key = $at->format($hourly ? 'Y-m-d H' : 'Y-m-d');

            return [
                'label' => $hourly ? $at->format('H\h') : $at->format('d/m'),
                'title' => $hourly ? $at->format('d/m H\h') : $at->format('d/m/Y'),
                'total' => (int) ($counts[$key] ?? 0),
            ];
        })->all();
    }

    /** Totais agrupados por uma coluna. @return array<string, int> */
    protected function breakdown(Builder $query, string $column, int $limit = 6): array
    {
        return $query->selectRaw("{$column} as grp, count(*) as total")
            ->groupByRaw($column)->orderByDesc('total')->limit($limit)
            ->toBase()->pluck('total', 'grp')
            ->mapWithKeys(fn ($total, $key) => [(string) ($key ?: '—') => (int) $total])
            ->all();
    }

    #[Computed]
    public function breakdowns(): array
    {
        $devices = collect($this->breakdown($this->visits(), 'device'))
            ->mapWithKeys(fn ($total, $key) => [UserAgent::DEVICES[$key] ?? $key => $total])->all();

        $sources = $this->breakdown($this->visits(), "coalesce(utm_source, referrer, 'Direto')");

        return [
            ['title' => 'Navegadores', 'icon' => 'globe', 'rows' => $this->breakdown($this->visits(), 'browser')],
            ['title' => 'Sistemas', 'icon' => 'monitor-smartphone', 'rows' => $this->breakdown($this->visits(), 'os')],
            ['title' => 'Aparelhos', 'icon' => 'smartphone', 'rows' => $devices],
            ['title' => 'Origem', 'icon' => 'link', 'rows' => $sources],
        ];
    }

    /** Onde mais clicaram. @return list<array{label: string, href: ?string, section: ?string, total: int}> */
    #[Computed]
    public function topClicks(): array
    {
        return $this->clicks()
            ->selectRaw('label, href, section, count(*) as total')
            ->groupBy('label', 'href', 'section')->orderByDesc('total')->limit(12)
            ->toBase()->get()
            ->map(fn ($row) => ['label' => $row->label ?: ($row->href ?: 'Sem texto'), 'href' => $row->href, 'section' => $row->section, 'total' => (int) $row->total])
            ->all();
    }

    /** @return array<string, int> */
    #[Computed]
    public function clicksBySection(): array
    {
        return $this->breakdown($this->clicks(), 'section', 8);
    }

    /** @return array<string, int> */
    #[Computed]
    public function topIps(): array
    {
        return $this->breakdown($this->visits(), 'ip', 8);
    }

    #[Computed]
    public function list()
    {
        return $this->visits()->search($this->search)->withCount('clicks')->latest('id')->paginate(25);
    }

    #[Computed]
    public function visit(): ?Visit
    {
        return $this->selected ? Visit::with('clicks')->find($this->selected) : null;
    }

    #[Computed]
    public function sameIpCount(): int
    {
        return $this->visit?->ip ? Visit::where('ip', $this->visit->ip)->count() : 0;
    }

    public function render()
    {
        return view('livewire.analytics.index', ['periods' => self::PERIODS]);
    }
}
