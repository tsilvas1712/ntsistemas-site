<?php

use App\Http\Controllers\SitePageController;
use App\Http\Controllers\TrackClickController;
use App\Http\Middleware\TrackVisit;
use App\Livewire\Analytics;
use App\Livewire\Auth\Login;
use App\Livewire\Contacts;
use App\Livewire\Dashboard;
use App\Livewire\SiteSettings;
use App\Livewire\Users;
use App\Support\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// www.ntsistemasweb.com.br → ntsistemasweb.com.br (mantém o caminho).
if ($domain = config('site.domain')) {
    Route::domain('www.'.$domain)->group(function () use ($domain) {
        Route::get('/{path?}', fn (?string $path = null) => redirect()->away('https://'.$domain.'/'.ltrim((string) $path, '/'), 301))
            ->where('path', '.*');
    });
}

/*
| Site público
*/
Route::view('/', 'site.home')->middleware(TrackVisit::class)->name('home');

// Páginas de serviço (uma por busca que o cliente faz no Google).
Route::get('/servicos/{service}', [SitePageController::class, 'service'])
    ->whereIn('service', array_keys(SiteContent::services()))
    ->middleware(TrackVisit::class)
    ->name('services.show');

Route::get('/sitemap.xml', [SitePageController::class, 'sitemap'])->name('sitemap');

// Cliques no site (enviados pelo navegador com sendBeacon).
Route::post('/t/c', TrackClickController::class)->middleware('throttle:120,1')->name('track.click');

/*
| Painel administrativo (/admin)
*/
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::livewire('/login', Login::class)->name('login');
    });

    Route::middleware('auth')->group(function () {
        Route::livewire('/', Dashboard::class)->name('dashboard');

        Route::livewire('/contatos', Contacts\Index::class)->name('contacts.index');
        Route::livewire('/contatos/{contact}', Contacts\Show::class)->name('contacts.show')
            ->whereNumber('contact')
            ->missing(fn () => redirect()->route('contacts.index'));

        Route::livewire('/acessos', Analytics\Index::class)->name('analytics.index');

        Route::livewire('/ajustes', SiteSettings::class)->name('settings');

        Route::livewire('/usuarios', Users\Index::class)->name('users.index');
        Route::livewire('/usuarios/novo', Users\Create::class)->name('users.create');
        Route::livewire('/usuarios/{user}', Users\Show::class)->name('users.show')
            ->whereNumber('user')
            ->missing(fn () => redirect()->route('users.index'));
        Route::livewire('/usuarios/{user}/editar', Users\Edit::class)->name('users.edit')
            ->whereNumber('user')
            ->missing(fn () => redirect()->route('users.index'));

        Route::post('/logout', function (Request $request) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        })->name('logout');
    });

    // Qualquer outra rota do painel → dashboard (que manda para o login se não houver sessão).
    Route::fallback(fn () => redirect()->route('dashboard'));
});

// Qualquer outra rota do site → página inicial.
Route::fallback(fn () => redirect()->route('home'));
