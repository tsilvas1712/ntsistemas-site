<?php

namespace App\Http\Controllers;

use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

/** Páginas públicas além da home: serviços (uma por busca no Google) e sitemap. */
class SitePageController extends Controller
{
    public function service(string $service): View
    {
        return view('site.service', [
            'slug' => $service,
            'service' => SiteContent::services()[$service],
        ]);
    }

    public function sitemap(): Response
    {
        $urls = [
            route('home'),
            ...array_map(fn (string $slug) => route('services.show', $slug), array_keys(SiteContent::services())),
        ];

        return response()->view('site.sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }
}
