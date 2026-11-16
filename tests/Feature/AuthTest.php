<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        foreach (['/admin', '/admin/contatos', '/admin/contatos/1', '/admin/ajustes', '/admin/usuarios'] as $uri) {
            $this->get($uri)->assertRedirect('/admin/login');
        }
    }

    public function test_login_page_renders(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Acesse o painel administrativo')
            ->assertSeeLivewire(Login::class);
    }

    public function test_authenticated_users_cannot_see_login(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/login')->assertRedirect('/admin');
    }

    public function test_users_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'ana@ntsistemasweb.test']);

        Livewire::test(Login::class)
            ->set('email', 'ana@ntsistemasweb.test')
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_users_cannot_login(): void
    {
        User::factory()->status(UserStatus::Inativo)->create(['email' => 'ana@ntsistemasweb.test']);

        Livewire::test(Login::class)
            ->set('email', 'ana@ntsistemasweb.test')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'ana@ntsistemasweb.test']);

        Livewire::test(Login::class)
            ->set('email', 'ana@ntsistemasweb.test')
            ->set('password', 'errada')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        User::factory()->create(['email' => 'ana@ntsistemasweb.test']);

        $component = Livewire::test(Login::class)->set('email', 'ana@ntsistemasweb.test')->set('password', 'errada');

        foreach (range(1, 5) as $attempt) {
            $component->call('login');
        }

        $component->set('password', 'password')->call('login')->assertHasErrors('email');
        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_unknown_routes_redirect(): void
    {
        $this->get('/qualquer/coisa')->assertRedirect('/');
        $this->get('/admin/qualquer')->assertRedirect('/admin');
    }

    public function test_site_admin_command_creates_admin(): void
    {
        $this->artisan('site:admin', ['email' => 'novo@ntsistemasweb.test', '--name' => 'Novo Admin'])
            ->expectsQuestion('Senha (mínimo 8 caracteres)', 'segredo123')
            ->assertSuccessful();

        $this->assertTrue(User::where('email', 'novo@ntsistemasweb.test')->where('status', 'ativo')->exists());
    }
}
