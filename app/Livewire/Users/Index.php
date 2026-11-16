<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Usuários')]
class Index extends Component
{
    /** Filtros disponíveis (valor => rótulo). */
    public const FILTERS = [
        'todos' => 'Todos',
        'ativo' => 'Ativos',
        'pendente' => 'Pendentes',
    ];

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'todos')]
    public string $filter = 'todos';

    public function setFilter(string $filter): void
    {
        $this->filter = array_key_exists($filter, self::FILTERS) ? $filter : 'todos';
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->search($this->search)
            ->when($this->filter !== 'todos', fn ($query) => $query->where('status', $this->filter))
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.users.index', ['filters' => self::FILTERS]);
    }
}
