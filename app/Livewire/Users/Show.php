<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Component;

class Show extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    /** Gera uma nova senha aleatória e mostra uma vez. */
    public function resetPassword(): void
    {
        $password = Str::password(14, symbols: false);
        $this->user->update(['password' => $password]);

        session()->flash('password', $password);
    }

    public function delete(): void
    {
        // Não deixa o usuário logado excluir a própria conta.
        abort_if($this->user->is(auth()->user()), 403, 'Você não pode excluir a própria conta.');

        $this->user->delete();

        $this->redirectRoute('users.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.users.show')->title($this->user->name);
    }
}
