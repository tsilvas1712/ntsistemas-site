<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Avisa por e-mail que chegou um contato novo pelo site. */
class NewContactReceived extends Notification
{
    public function __construct(public Contact $contact) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $contact = $this->contact;

        $mail = (new MailMessage)
            ->subject("Novo contato pelo site: {$contact->name}")
            ->replyTo($contact->email, $contact->name)
            ->greeting('Novo contato pelo site')
            ->line("**{$contact->name}** ({$contact->email})");

        foreach (array_filter([
            'Empresa' => $contact->company,
            'Telefone' => $contact->phone,
            'Orçamento' => $contact->budget,
            'Stack' => $contact->stack,
            'Serviços' => implode(', ', $contact->serviceLabels()),
        ]) as $label => $value) {
            $mail->line("{$label}: {$value}");
        }

        return $mail
            ->line($contact->message)
            ->action('Abrir no painel', route('contacts.show', $contact));
    }
}
