<?php

namespace Tests\Feature;

use App\Livewire\Site\ContactForm;
use App\Livewire\SiteSettings;
use App\Models\Contact;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewContactReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_with_contact_form(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Assumimos, corrigimos e modernizamos.')
            ->assertSee('{"@context":"https://schema.org","@type":"ProfessionalService"', false)
            ->assertSee('contato@ntsistemasweb.com.br')
            ->assertSee('https://wa.me/5551920056949', false)
            ->assertDontSee('projetos entregues')
            ->assertDontSee('SLA de uptime')
            ->assertSeeLivewire(ContactForm::class);
    }

    public function test_home_speaks_to_business_owners_and_has_share_preview_and_faq_data(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<meta property="og:image" content="'.asset('images/og-nt-sistemas-web.png').'" />', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('Quem vai cuidar do seu sistema')
            ->assertSee(route('services.show', 'manutencao-de-sistemas'), false)
            ->assertDontSee('Staff/Principal')
            ->assertDontSee('SOLID')
            ->assertDontSee('IA opcional');
    }

    public function test_service_page_renders_with_contact_form_and_is_in_the_sitemap(): void
    {
        $this->get(route('services.show', 'migracao-de-sistemas-desktop-para-web'))
            ->assertOk()
            ->assertSee('Seu sistema Delphi, VB ou Access na web, sem parar a empresa.')
            ->assertSee('<title>Migração de sistema desktop (Delphi, VB, Access) para a web · NT Sistemas Web</title>', false)
            ->assertSeeLivewire(ContactForm::class);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<loc>'.route('services.show', 'diagnostico-de-sistema').'</loc>', false);
    }

    public function test_contact_keeps_the_campaign_that_brought_the_visitor(): void
    {
        Notification::fake();

        $this->get('/?utm_source=linkedin&utm_medium=social&utm_campaign=post-1')->assertOk();
        $this->get('/')->assertOk(); // visita seguinte, sem origem, não apaga a primeira

        Livewire::test(ContactForm::class)
            ->call('toggleService', 'legacy')
            ->call('next')
            ->set('name', 'Maria Silva')
            ->set('email', 'maria@empresa.com')
            ->set('message', 'Nosso sistema cai toda sexta-feira à tarde.')
            ->call('submit')
            ->assertHasNoErrors();

        $contact = Contact::sole();
        $this->assertSame(['linkedin', 'social', 'post-1'], [$contact->utm_source, $contact->utm_medium, $contact->utm_campaign]);
    }

    public function test_contact_form_saves_and_notifies(): void
    {
        Notification::fake();

        Livewire::test(ContactForm::class)
            ->call('toggleService', 'legacy')
            ->call('toggleService', 'devops')
            ->call('toggleService', 'devops')
            ->call('toggleService', 'inexistente')
            ->call('next')
            ->set('name', 'Maria Silva')
            ->set('email', 'maria@empresa.com')
            ->set('phone', '(51) 99999-0000')
            ->set('message', 'Nosso sistema cai toda sexta-feira à tarde.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true)
            ->assertSee('Mensagem recebida.');

        $contact = Contact::sole();
        $this->assertSame(['legacy'], $contact->services);
        $this->assertSame(['Sistema travando ou sem ninguém para mexer'], $contact->serviceLabels());
        $this->assertSame('novo', $contact->status->value);
        $this->assertSame('5551999990000', $contact->whatsappNumber());

        Notification::assertSentTo(new AnonymousNotifiable, NewContactReceived::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'contato@ntsistemasweb.com.br');
    }

    public function test_contact_form_validates(): void
    {
        Livewire::test(ContactForm::class)
            ->call('next')
            ->set('email', 'nao-e-email')
            ->set('message', 'curta')
            ->call('submit')
            ->assertHasErrors(['name' => 'required', 'email' => 'email', 'message' => 'min']);

        $this->assertSame(0, Contact::count());
    }

    public function test_honeypot_discards_bots(): void
    {
        Livewire::test(ContactForm::class)
            ->set('website', 'http://spam.example')
            ->set('name', 'Bot')
            ->set('email', 'bot@spam.example')
            ->set('message', 'Compre agora, promoção imperdível!')
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertSame(0, Contact::count());
    }

    public function test_contact_form_is_rate_limited(): void
    {
        Notification::fake();

        foreach (range(1, 6) as $i) {
            $component = Livewire::test(ContactForm::class)
                ->set('name', "Pessoa {$i}")
                ->set('email', "p{$i}@empresa.com")
                ->set('message', 'Mensagem de teste suficiente.')
                ->call('submit');
        }

        $component->assertHasErrors('message');
        $this->assertSame(5, Contact::count());
    }

    public function test_settings_change_the_site(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(SiteSettings::class)
            ->set('contact_email', 'oi@ntsistemasweb.com.br')
            ->set('whatsapp', '55 51 99999-0000')
            ->set('available', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('oi@ntsistemasweb.com.br', Setting::get('contact_email'));

        $this->get('/')
            ->assertSee('oi@ntsistemasweb.com.br')
            ->assertSee('wa.me/5551999990000')
            ->assertDontSee('Disponível para novos projetos');
    }

    public function test_settings_validate(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(SiteSettings::class)
            ->set('contact_email', '')
            ->set('github_url', 'github.com/sem-https')
            ->set('whatsapp', 'abc')
            ->call('save')
            ->assertHasErrors(['contact_email', 'github_url', 'whatsapp']);
    }

    public function test_form_offers_the_current_services_and_old_contacts_keep_their_labels(): void
    {
        Livewire::test(ContactForm::class)
            ->assertSee('Manutenção mensal')
            ->assertSee('Levar um sistema antigo para a web')
            ->assertDontSee('Redesign arquitetural')
            ->call('toggleService', 'arch')
            ->assertSet('services', []);

        $this->assertSame(['Redesign arquitetural', 'Manutenção mensal'], (new Contact(['services' => ['arch', 'maintenance']]))->serviceLabels());
    }
}
