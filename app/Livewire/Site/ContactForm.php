<?php

namespace App\Livewire\Site;

use App\Models\Contact;
use App\Models\Setting;
use App\Notifications\NewContactReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;

/** Formulário de contato do site, em 2 passos: o problema e os detalhes. */
class ContactForm extends Component
{
    public int $step = 1;

    /** @var list<string> */
    public array $services = [];

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $company = '';

    public string $budget = '';

    public string $stack = '';

    public string $message = '';

    /** Armadilha para robôs: fica escondido, gente de verdade não preenche. */
    public string $website = '';

    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'services' => ['array'],
            'services.*' => [Rule::in(array_keys(Contact::SERVICES))],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:160'],
            'budget' => ['nullable', 'string', 'max:120'],
            'stack' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Informe seu nome.',
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'message.required' => 'Conte um pouco sobre o problema.',
            'message.min' => 'Conte um pouco mais (mínimo de 10 caracteres).',
        ];
    }

    public function toggleService(string $key): void
    {
        if (! array_key_exists($key, Contact::SERVICES)) {
            return;
        }

        $this->services = in_array($key, $this->services, true)
            ? array_values(array_diff($this->services, [$key]))
            : [...$this->services, $key];
    }

    public function next(): void
    {
        $this->step = 2;
    }

    public function back(): void
    {
        $this->step = 1;
    }

    public function submit(): void
    {
        // Robô preencheu o campo escondido: finge que deu certo e não grava nada.
        if ($this->website !== '') {
            $this->sent = true;

            return;
        }

        $data = $this->validate();

        $key = 'contact-form|'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'message' => 'Muitos envios seguidos. Tente de novo em alguns minutos ou fale com a gente por e-mail.',
            ]);
        }
        RateLimiter::hit($key, 3600);

        $contact = Contact::create([
            ...$data,
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);

        $this->notify($contact);

        $this->reset('services', 'name', 'email', 'phone', 'company', 'budget', 'stack', 'message');
        $this->step = 1;
        $this->sent = true;
    }

    /** Aviso por e-mail. Se o SMTP falhar, o contato já está salvo e aparece no painel. */
    protected function notify(Contact $contact): void
    {
        $to = Setting::get('notification_email');

        if (! $to) {
            return;
        }

        try {
            Notification::route('mail', $to)->notify(new NewContactReceived($contact));
        } catch (Throwable $e) {
            Log::warning('Falha ao enviar o aviso de novo contato.', ['contact' => $contact->id, 'error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.site.contact-form', [
            'options' => Contact::SERVICES,
            'responseHours' => Setting::get('response_hours', 48),
        ]);
    }
}
