<?php

namespace App\Livewire\Users;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar usuário')]
class Edit extends Component
{
    public UserForm $form;

    public function mount(User $user): void
    {
        $this->form->setUser($user);
    }

    public function save(): void
    {
        $user = $this->form->update();

        $this->redirectRoute('users.show', $user, navigate: true);
    }

    public function render()
    {
        return view('livewire.users.edit', ['user' => $this->form->user]);
    }
}
