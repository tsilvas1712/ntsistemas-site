<?php

namespace App\Models;

use App\Support\UserAgent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

#[Fillable(['visitor', 'ip', 'user_agent', 'browser', 'browser_version', 'os', 'device', 'is_bot', 'path', 'referrer', 'utm_source', 'utm_medium', 'utm_campaign'])]
class Visit extends Model
{
    use MassPrunable;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['is_bot' => 'boolean'];
    }

    /** @return HasMany<VisitClick, $this> */
    public function clicks(): HasMany
    {
        return $this->hasMany(VisitClick::class)->orderBy('id');
    }

    public static function record(Request $request): self
    {
        $userAgent = substr((string) $request->userAgent(), 0, 512);
        $referrer = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);

        return self::create([
            ...UserAgent::parse($userAgent),
            'visitor' => substr(hash_hmac('sha256', $request->ip().'|'.$userAgent, (string) config('app.key')), 0, 16),
            'ip' => $request->ip(),
            'user_agent' => $userAgent ?: null,
            'path' => substr('/'.ltrim($request->path(), '/'), 0, 255),
            'referrer' => $referrer && $referrer !== $request->getHost() ? substr(preg_replace('/^www\./', '', $referrer), 0, 255) : null,
            'utm_source' => self::param($request, 'utm_source'),
            'utm_medium' => self::param($request, 'utm_medium'),
            'utm_campaign' => self::param($request, 'utm_campaign'),
        ]);
    }

    private static function param(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) && $value !== '' ? substr($value, 0, 100) : null;
    }

    public function deviceLabel(): string
    {
        return UserAgent::DEVICES[$this->device] ?? $this->device;
    }

    /** "Google", "instagram.com", "Direto"… */
    public function sourceLabel(): string
    {
        return $this->utm_source ?: ($this->referrer ?: 'Direto');
    }

    /** Busca por IP, navegador, sistema ou origem. */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(fn (Builder $q) => $q->whereLike('ip', "%{$term}%")
            ->orWhereLike('browser', "%{$term}%")
            ->orWhereLike('os', "%{$term}%")
            ->orWhereLike('referrer', "%{$term}%")
            ->orWhereLike('utm_source', "%{$term}%"));
    }

    /** Acessos mais antigos que o prazo de retenção são apagados (LGPD: IP é dado pessoal). */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays((int) config('site.analytics.retention_days')));
    }
}
