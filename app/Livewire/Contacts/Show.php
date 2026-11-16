<?php

namespace App\Livewire\Contacts;

use App\Enums\ContactStatus;
use App\Models\Contact;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    public Contact $contact;

    public string $status = '';

    public string $notes = '';

    public function mount(Contact $contact): void
    {
        $this->contact = $contact;
        $this->status = $contact->status->value;
        $this->notes = (string) $contact->notes;
    }

    public function save(): void
    {
        $this->validate([
            'status' => ['required', Rule::enum(ContactStatus::class)],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $status = ContactStatus::from($this->status);

        $this->contact->update([
            'status' => $status,
            'notes' => $this->notes ?: null,
            'replied_at' => $status === ContactStatus::Respondido ? ($this->contact->replied_at ?? now()) : $this->contact->replied_at,
        ]);

        session()->flash('saved', 'Contato atualizado.');
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->save();
    }

    public function delete(): void
    {
        $this->contact->delete();

        $this->redirectRoute('contacts.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.contacts.show')->title($this->contact->name);
    }
}
