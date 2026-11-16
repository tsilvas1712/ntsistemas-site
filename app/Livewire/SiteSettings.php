<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Ajustes do site editáveis sem deploy: contatos, links e disponibilidade. */
#[Title('Ajustes do site')]
class SiteSettings extends Component
{
    public string $contact_email = '';

    public string $notification_email = '';

    public string $whatsapp = '';

    public string $github_url = '';

    public string $linkedin_url = '';

    public string $response_hours = '48';

    public bool $available = true;

    public function mount(): void
    {
        $values = Setting::allValues();

        foreach (['contact_email', 'notification_email', 'whatsapp', 'github_url', 'linkedin_url', 'response_hours'] as $key) {
            $this->{$key} = (string) ($values[$key] ?? '');
        }

        $this->available = (bool) ($values['available'] ?? true);
    }

    public function save(): void
    {
        $data = $this->validate([
            'contact_email' => ['required', 'email', 'max:160'],
            'notification_email' => ['nullable', 'email', 'max:160'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[\d\s()+-]*$/'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'response_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'available' => ['boolean'],
        ], [
            'contact_email.required' => 'Informe o e-mail de contato.',
            '*.email' => 'Informe um e-mail válido.',
            '*.url' => 'Informe um endereço completo, com https://.',
            'whatsapp.regex' => 'Use só números, espaços, parênteses, + e -.',
        ]);

        // Campos vazios ficam gravados como "" para não voltarem ao padrão do config.
        Setting::put(array_map(fn ($value) => $value ?? '', $data));

        session()->flash('saved', 'Ajustes salvos. O site já está atualizado.');
    }

    public function render()
    {
        return view('livewire.site-settings');
    }
}
