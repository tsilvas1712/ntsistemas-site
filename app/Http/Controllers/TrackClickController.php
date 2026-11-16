<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\VisitClick;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/** Recebe (via navigator.sendBeacon) os cliques feitos no site. Sempre responde 204. */
class TrackClickController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $visitId = $request->session()->get('visit_id');

        if ($visitId && Visit::whereKey($visitId)->exists() && VisitClick::where('visit_id', $visitId)->count() < VisitClick::LIMIT_PER_VISIT) {
            VisitClick::create([
                'visit_id' => $visitId,
                'label' => $this->text($request, 'label', 120),
                'href' => $this->text($request, 'href', 500),
                'section' => preg_match('/^[\w-]{1,60}$/', (string) $request->input('section')) ? $request->input('section') : null,
                'element' => preg_match('/^[a-z]{1,20}$/', (string) $request->input('element')) ? $request->input('element') : null,
            ]);
        }

        return response()->noContent();
    }

    private function text(Request $request, string $key, int $max): ?string
    {
        $value = $request->input($key);

        return is_string($value) && trim($value) !== '' ? mb_substr(trim(preg_replace('/\s+/u', ' ', $value)), 0, $max) : null;
    }
}
