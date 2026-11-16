<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Users\Create;
use App\Livewire\Users\Edit;
use App\Livewire\Users\Index;
use App\Livewire\Users\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['name' => 'Thiago Silva', 'email' => 'thiago@adminkit.io', 'role' => UserRole::Admin]);
        User::factory()->create(['id' => 1024, 'name' => 'Ana Martins', 'email' => 'ana@adminkit.io', 'role' => UserRole::Admin, 'notes' => 'Acesso total ao painel comercial.', 'created_at' => '2026-08-02 12:00:00']);
        User::factory()->create(['name' => 'Rafael Lopes', 'email' => 'rafael@adminkit.io']);
        User::factory()->status(UserStatus::Pendente)->create(['name' => 'Julia Souza', 'email' => 'julia@adminkit.io']);
        User::factory()->status(UserStatus::Inativo)->create(['name' => 'Pedro Lima', 'email' => 'pedro@adminkit.io']);
        $this->actingAs($this->admin);
    }

    public function test_list_shows_all_users(): void
    {
        $this->get('/admin/usuarios')->assertOk()->assertSee(['Ana Martins', 'Rafael Lopes', 'Julia Souza', 'Pedro Lima']);

        Livewire::test(Index::class)->assertSee('5 registros');
    }

    public function test_list_can_be_searched_by_name_or_email(): void
    {
        Livewire::test(Index::class)
            ->set('search', 'ana')
            ->assertSee('Ana Martins')
            ->assertDontSee('Rafael Lopes')
            ->assertSee('1 registro')
            ->set('search', 'pedro@')
            ->assertSee('Pedro Lima')
            ->assertDontSee('Ana Martins');
    }

    public function test_list_can_be_filtered_by_status(): void
    {
        Livewire::test(Index::class)
            ->call('setFilter', 'pendente')
            ->assertSee('Julia Souza')
            ->assertDontSee('Ana Martins')
            ->call('setFilter', 'ativo')
            ->assertSee('Ana Martins')
            ->assertDontSee('Julia Souza')
            ->call('setFilter', 'invalido')
            ->assertSet('filter', 'todos');
    }

    public function test_users_can_be_created(): void
    {
        Livewire::test(Create::class)
            ->set('form.name', '  Maria Teste ')
            ->set('form.email', 'maria@adminkit.io')
            ->set('form.role', 'admin')
            ->set('form.status', 'pendente')
            ->set('form.notes', 'Criada no teste')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('users.show', User::where('email', 'maria@adminkit.io')->first()));

        $user = User::where('email', 'maria@adminkit.io')->firstOrFail();
        $this->assertSame('Maria Teste', $user->name);
        $this->assertSame(UserRole::Admin, $user->role);
        $this->assertSame(UserStatus::Pendente, $user->status);
        $this->assertTrue(Hash::check(session('password'), $user->password));
    }

    public function test_create_validates_input(): void
    {
        Livewire::test(Create::class)
            ->set('form.name', '')
            ->set('form.email', 'ana@adminkit.io')
            ->set('form.role', 'root')
            ->call('save')
            ->assertHasErrors(['form.name' => 'required', 'form.email' => 'unique', 'form.role']);

        Livewire::test(Create::class)
            ->set('form.name', 'X')
            ->set('form.email', 'nao-e-email')
            ->call('save')
            ->assertHasErrors(['form.email' => 'email']);
    }

    public function test_users_can_be_edited(): void
    {
        $ana = User::where('email', 'ana@adminkit.io')->firstOrFail();

        Livewire::test(Edit::class, ['user' => $ana])
            ->assertSet('form.name', 'Ana Martins')
            ->assertSet('form.role', 'admin')
            ->set('form.name', 'Ana Editada')
            ->set('form.status', 'inativo')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('users.show', $ana));

        $ana->refresh();
        $this->assertSame('Ana Editada', $ana->name);
        $this->assertSame(UserStatus::Inativo, $ana->status);
    }

    public function test_editing_keeps_own_email_valid(): void
    {
        $ana = User::where('email', 'ana@adminkit.io')->firstOrFail();

        Livewire::test(Edit::class, ['user' => $ana])->call('save')->assertHasNoErrors();
    }

    public function test_detail_page_shows_user(): void
    {
        $this->get('/admin/usuarios/1024')
            ->assertOk()
            ->assertSee(['Ana Martins', 'ana@adminkit.io', 'Administrador', '02/08/2026', 'Acesso total ao painel comercial.']);
    }

    public function test_missing_users_redirect_to_list(): void
    {
        $this->get('/admin/usuarios/999999')->assertRedirect('/admin/usuarios');
        $this->get('/admin/usuarios/999999/editar')->assertRedirect('/admin/usuarios');
    }

    public function test_users_can_be_deleted(): void
    {
        $pedro = User::where('email', 'pedro@adminkit.io')->firstOrFail();

        Livewire::test(Show::class, ['user' => $pedro])
            ->call('delete')
            ->assertRedirect(route('users.index'));

        $this->assertModelMissing($pedro);
    }

    public function test_users_cannot_delete_themselves(): void
    {
        Livewire::test(Show::class, ['user' => $this->admin])
            ->call('delete')
            ->assertForbidden();

        $this->assertModelExists($this->admin);
    }

    public function test_password_can_be_reset(): void
    {
        $ana = User::where('email', 'ana@adminkit.io')->firstOrFail();

        $before = $ana->password;

        Livewire::test(Show::class, ['user' => $ana])->call('resetPassword')->assertSee('Senha de acesso');

        $this->assertNotSame($before, $ana->fresh()->password);
        $this->assertFalse(Hash::check('password', $ana->fresh()->password));
    }
}
