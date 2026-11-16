<?php

namespace App\Livewire\Users;

use App\Livewire\Forms\UserForm;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo usuário')]
class Create extends Component
{
    public UserForm $form;

    public function save(): void
    {
        $user = $this->form->store();

        session()->flash('password', $this->form->generatedPassword);

        $this->redirectRoute('users.show', $user, navigate: true);
    }

    public function render()
    {
        return view('livewire.users.create');
    }
}
