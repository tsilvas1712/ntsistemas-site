<?php

namespace Tests\Feature;

use App\Livewire\Analytics\Index;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitClick;
use App\Support\UserAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private const CHROME_WINDOWS = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36';

    private const SAFARI_IPHONE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.1 Mobile/15E148 Safari/604.1';

    public function test_user_agent_is_parsed(): void
    {
        $this->assertSame(['browser' => 'Chrome', 'browser_version' => '131', 'os' => 'Windows', 'device' => 'desktop', 'is_bot' => false], UserAgent::parse(self::CHROME_WINDOWS));
        $this->assertSame(['browser' => 'Safari', 'browser_version' => '18', 'os' => 'iOS', 'device' => 'mobile', 'is_bot' => false], UserAgent::parse(self::SAFARI_IPHONE));

        $edge = UserAgent::parse('Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Edg/131.0.0.0');
        $this->assertSame('Edge', $edge['browser']);

        $tablet = UserAgent::parse('Mozilla/5.0 (Linux; Android 14; SM-X710) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36');
        $this->assertSame(['Android', 'tablet'], [$tablet['os'], $tablet['device']]);

        $this->assertTrue(UserAgent::parse('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')['is_bot']);
        $this->assertTrue(UserAgent::parse('')['is_bot']);
    }

    public function test_site_visit_is_recorded(): void
    {
        $this->withHeaders(['User-Agent' => self::SAFARI_IPHONE, 'Referer' => 'https://www.instagram.com/ntsistemas'])
            ->get('/?utm_source=instagram&utm_campaign=lancamento')
            ->assertOk();

        $visit = Visit::sole();
        $this->assertSame('127.0.0.1', $visit->ip);
        $this->assertSame(['Safari', 'iOS', 'mobile'], [$visit->browser, $visit->os, $visit->device]);
        $this->assertSame('instagram.com', $visit->referrer);
        $this->assertSame('lancamento', $visit->utm_campaign);
        $this->assertSame($visit->id, session('visit_id'));
    }

    public function test_logged_in_admin_and_prefetch_are_not_recorded(): void
    {
        $this->withHeaders(['Sec-Purpose' => 'prefetch'])->get('/')->assertOk();
        $this->actingAs(User::factory()->create())->get('/')->assertOk();

        $this->assertSame(0, Visit::count());
    }

    public function test_clicks_are_recorded_for_the_current_visit(): void
    {
        $this->withHeaders(['User-Agent' => self::CHROME_WINDOWS])->get('/');
        $visit = Visit::sole();

        $this->post('/t/c', ['label' => "  Solicitar\n avaliação técnica ", 'href' => '#contato', 'section' => 'hero', 'element' => 'a'])->assertNoContent();
        $this->post('/t/c', ['label' => str_repeat('x', 500), 'section' => 'inválido!', 'element' => '<script>'])->assertNoContent();

        $clicks = $visit->clicks()->get();
        $this->assertCount(2, $clicks);
        $this->assertSame(['Solicitar avaliação técnica', '#contato', 'hero', 'a'], [$clicks[0]->label, $clicks[0]->href, $clicks[0]->section, $clicks[0]->element]);
        $this->assertSame(120, mb_strlen($clicks[1]->label));
        $this->assertNull($clicks[1]->section);
        $this->assertNull($clicks[1]->element);
    }

    public function test_clicks_without_visit_are_ignored(): void
    {
        $this->post('/t/c', ['label' => 'Algo'])->assertNoContent();

        $this->assertSame(0, VisitClick::count());
    }

    public function test_old_visits_are_pruned(): void
    {
        $this->get('/');
        Visit::query()->update(['created_at' => now()->subDays(181)]);
        $this->get('/');

        (new Visit)->pruneAll();

        $this->assertSame(1, Visit::count());
    }

    public function test_admin_page_shows_stats_and_details(): void
    {
        $this->withHeaders(['User-Agent' => self::CHROME_WINDOWS])->get('/');
        $this->post('/t/c', ['label' => 'Fale com a gente', 'href' => '#contato', 'section' => 'header', 'element' => 'a']);
        $this->withHeaders(['User-Agent' => 'Googlebot/2.1'])->get('/');

        $visit = Visit::where('is_bot', false)->sole();

        $this->actingAs(User::factory()->create());

        $this->get('/admin/acessos')->assertOk()->assertSee(['Acessos', 'Chrome', 'Windows', 'Fale com a gente']);

        Livewire::test(Index::class)
            ->assertSee('1 no período')
            ->set('bots', true)
            ->assertSee('2 no período')
            ->call('select', $visit->id)
            ->assertSee(['Acesso #'.$visit->id, self::CHROME_WINDOWS])
            ->call('setPeriod', '24h')
            ->assertSet('period', '24h')
            ->call('setPeriod', 'qualquer')
            ->assertSet('period', '7d')
            ->call('filterIp', '10.0.0.1')
            ->assertSee('Nenhum acesso encontrado');
    }
}
