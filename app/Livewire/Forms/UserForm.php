<?php

namespace App\Livewire\Forms;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

/**
 * Formulário compartilhado entre criar e editar usuário (equivalente ao UserForm do React).
 */
class UserForm extends Form
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $role = 'editor';

    public string $status = 'ativo';

    public string $notes = '';

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->status = $user->status->value;
        $this->notes = (string) $user->notes;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Informe o nome.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Já existe um usuário com este e-mail.',
            'role.*' => 'Selecione um perfil válido.',
            'status.*' => 'Selecione um status válido.',
            '*.max' => 'Texto longo demais.',
        ];
    }

    /** @return array<string, mixed> */
    protected function payload(): array
    {
        $this->name = trim($this->name);
        $this->email = trim($this->email);
        $this->notes = trim($this->notes);

        return $this->validate();
    }

    /** Senha gerada no cadastro, mostrada uma única vez na tela do usuário. */
    public ?string $generatedPassword = null;

    public function store(): User
    {
        $data = $this->payload();

        // O formulário não tem campo de senha: gera uma aleatória.
        $this->generatedPassword = Str::password(14, symbols: false);

        return User::create([...$data, 'password' => $this->generatedPassword]);
    }

    public function update(): User
    {
        $this->user->update($this->payload());

        return $this->user;
    }
}
