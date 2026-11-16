<?php

namespace App\Livewire\Contacts;

use App\Enums\ContactStatus;
use App\Models\Contact;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Contatos')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'todos')]
    public string $filter = 'todos';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter === 'todos' || ContactStatus::tryFrom($filter) ? $filter : 'todos';
        $this->resetPage();
    }

    protected function query()
    {
        return Contact::query()
            ->search($this->search)
            ->when($this->filter !== 'todos', fn ($query) => $query->where('status', $this->filter))
            ->latest();
    }

    #[Computed]
    public function contacts()
    {
        return $this->query()->paginate(20);
    }

    /** @return array<string, int> quantidade por status */
    #[Computed]
    public function counts(): array
    {
        return Contact::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->all();
    }

    /** Exporta o resultado filtrado em CSV (abre no Excel). */
    public function export(): StreamedResponse
    {
        $rows = $this->query()->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Data', 'Nome', 'E-mail', 'Telefone', 'Empresa', 'Orçamento', 'Stack', 'Serviços', 'Status', 'Mensagem', 'Observações'], ';');
            foreach ($rows as $c) {
                fputcsv($out, [
                    $c->created_at?->format('d/m/Y H:i'), $c->name, $c->email, $c->phone, $c->company, $c->budget, $c->stack,
                    implode(', ', $c->serviceLabels()), $c->status->label(), $c->message, $c->notes,
                ], ';');
            }
            fclose($out);
        }, 'contatos-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        return view('livewire.contacts.index', [
            'filters' => ['todos' => 'Todos', ...ContactStatus::options()],
        ]);
    }
}
