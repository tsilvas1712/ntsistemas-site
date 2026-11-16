<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * $attributes->cn('classes base', 'variante') nos componentes Blade:
         * junta as classes do componente com o "class" recebido, resolvendo conflitos
         * do Tailwind (quem usa o componente sempre ganha).
         */
        ComponentAttributeBag::macro('cn', function (?string ...$classes): ComponentAttributeBag {
            /** @var ComponentAttributeBag $this */
            return $this->except('class')->merge([
                'class' => cn(...$classes, ...[$this->get('class')]),
            ]);
        });

        // Ajustes do site (e-mail, WhatsApp, links…) disponíveis como $site nas páginas públicas.
        View::composer(['layouts.site', 'site.*'], function ($view) {
            $view->with('site', Setting::allValues());
        });
    }
}
