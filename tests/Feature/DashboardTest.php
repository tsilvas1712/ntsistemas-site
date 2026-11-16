<?php

namespace Tests\Feature;

use App\Enums\ContactStatus;
use App\Livewire\Dashboard;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_greeting_and_contact_counts(): void
    {
        Carbon::setTestNow('2026-09-18 10:00:00');
        Contact::factory()->count(3)->create();
        Contact::factory()->create(['status' => ContactStatus::Fechado, 'name' => 'Cliente Fechado']);

        $this->actingAs(User::factory()->create(['name' => 'Ana Martins']))
            ->get('/admin')
            ->assertOk()
            ->assertSee('Olá, Ana')
            ->assertSee('Sexta, 18 set 2026')
            ->assertSee('Cliente Fechado')
            ->assertSee('© 2026 NT Sistemas Web');

        Livewire::test(Dashboard::class)
            ->assertViewHas('stats', fn (array $stats) => $stats[0]['value'] === '3' && $stats[2]['value'] === '1' && $stats[3]['value'] === '4');
    }

    public function test_format_long_date(): void
    {
        $this->assertSame('Domingo, 1 fev 2026', format_long_date(Carbon::parse('2026-02-01')));
        $this->assertSame('Sábado, 31 dez 2033', format_long_date(Carbon::parse('2033-12-31')));
    }
}
