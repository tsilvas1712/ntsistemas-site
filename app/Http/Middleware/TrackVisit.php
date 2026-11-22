<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/** Registra o acesso às páginas do site público (não conta quem está logado no painel). */
class TrackVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        try {
            $visit = Visit::record($request);
            $request->session()->put('visit_id', $visit->id);

            // Primeira origem conhecida da sessão: vai junto se a pessoa mandar o formulário.
            if (! $request->session()->has('visit_source') && ($visit->utm_source || $visit->referrer)) {
                $request->session()->put('visit_source', [
                    'utm_source' => $visit->utm_source ?? $visit->referrer,
                    'utm_medium' => $visit->utm_source ? $visit->utm_medium : 'referral',
                    'utm_campaign' => $visit->utm_campaign,
                ]);
            }

            // Limpeza ocasional dos acessos antigos, sem depender do agendador.
            if (random_int(1, 200) === 1) {
                (new Visit)->pruneAll();
            }
        } catch (Throwable $e) {
            report($e); // estatística nunca derruba o site
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        $purpose = strtolower($request->header('Sec-Purpose', $request->header('Purpose', '')));

        return config('site.analytics.enabled')
            && $request->isMethod('GET')
            && $response->getStatusCode() === 200
            && ! str_contains($purpose, 'prefetch')
            && ! Auth::check()
            && $request->hasSession();
    }
}
