<?php

namespace Tests\Feature;

use App\Enums\ContactStatus;
use App\Livewire\Contacts\Index;
use App\Livewire\Contacts\Show;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
        Contact::factory()->create(['name' => 'Rodrigo Almeida', 'email' => 'rodrigo@fintech.com', 'company' => 'FinTech XP']);
        Contact::factory()->create(['name' => 'Camila Torres', 'email' => 'camila@logistica.com', 'company' => 'LogísticaBR', 'status' => ContactStatus::Fechado]);
    }

    public function test_list_shows_contacts(): void
    {
        $this->get('/admin/contatos')->assertOk()->assertSee(['Rodrigo Almeida', 'Camila Torres']);

        Livewire::test(Index::class)->assertSee('2 registros');
    }

    public function test_list_can_be_searched_and_filtered(): void
    {
        Livewire::test(Index::class)
            ->set('search', 'fintech')
            ->assertSee('Rodrigo Almeida')
            ->assertDontSee('Camila Torres')
            ->set('search', '')
            ->call('setFilter', 'fechado')
            ->assertSee('Camila Torres')
            ->assertDontSee('Rodrigo Almeida')
            ->call('setFilter', 'invalido')
            ->assertSet('filter', 'todos');
    }

    public function test_list_can_be_exported(): void
    {
        Livewire::test(Index::class)->call('export')->assertFileDownloaded('contatos-'.now()->format('Y-m-d').'.csv');
    }

    public function test_detail_page_shows_contact(): void
    {
        $contact = Contact::where('email', 'rodrigo@fintech.com')->sole();

        $this->get("/admin/contatos/{$contact->id}")->assertOk()->assertSee(['Rodrigo Almeida', 'FinTech XP', 'Resgate de sistema legado']);
        $this->get('/admin/contatos/999999')->assertRedirect('/admin/contatos');
    }

    public function test_status_and_notes_can_be_updated(): void
    {
        $contact = Contact::where('email', 'rodrigo@fintech.com')->sole();

        Livewire::test(Show::class, ['contact' => $contact])
            ->set('status', 'em_atendimento')
            ->set('notes', 'Ligar na sexta')
            ->call('save')
            ->assertHasNoErrors();

        $contact->refresh();
        $this->assertSame(ContactStatus::EmAtendimento, $contact->status);
        $this->assertSame('Ligar na sexta', $contact->notes);
        $this->assertNull($contact->replied_at);

        Livewire::test(Show::class, ['contact' => $contact])->call('setStatus', 'respondido');
        $this->assertNotNull($contact->fresh()->replied_at);

        Livewire::test(Show::class, ['contact' => $contact])->set('status', 'qualquer')->call('save')->assertHasErrors('status');
    }

    public function test_contacts_can_be_deleted(): void
    {
        $contact = Contact::where('email', 'rodrigo@fintech.com')->sole();

        Livewire::test(Show::class, ['contact' => $contact])->call('delete')->assertRedirect(route('contacts.index'));

        $this->assertModelMissing($contact);
    }
}
