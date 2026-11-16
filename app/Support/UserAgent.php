<?php

namespace App\Support;

/**
 * Leitura simples do User-Agent: navegador, sistema, tipo de aparelho e robôs.
 * Cobre os casos comuns; o texto original fica salvo para consulta.
 */
final class UserAgent
{
    private const BOTS = '/bot|crawl|spider|slurp|scrap|facebookexternalhit|preview|embed|whatsapp|telegram|curl|wget|python|httpclient|okhttp|go-http|java\/|axios|node-fetch|headless|lighthouse|pingdom|uptime|monitor|scanner|ahrefs|semrush/i';

    /** Ordem importa: Edge e Opera também dizem "Chrome"; Chrome também diz "Safari". */
    private const BROWSERS = [
        'Edge' => '/Edg(?:e|A|iOS)?\/(\d+)/',
        'Opera' => '/(?:OPR|Opera)\/(\d+)/',
        'Samsung Internet' => '/SamsungBrowser\/(\d+)/',
        'Firefox' => '/(?:Firefox|FxiOS)\/(\d+)/',
        'Chrome' => '/(?:Chrome|CriOS)\/(\d+)/',
        'Safari' => '/Version\/(\d+).*Safari/',
    ];

    private const SYSTEMS = [
        'Windows' => '/Windows/',
        'iOS' => '/iPhone|iPad|iPod/',
        'Android' => '/Android/',
        'ChromeOS' => '/CrOS/',
        'macOS' => '/Mac OS X|Macintosh/',
        'Linux' => '/Linux/',
    ];

    /** @return array{browser: ?string, browser_version: ?string, os: ?string, device: string, is_bot: bool} */
    public static function parse(?string $userAgent): array
    {
        $ua = (string) $userAgent;
        $isBot = $ua === '' || preg_match(self::BOTS, $ua) === 1;

        $browser = $version = null;
        foreach (self::BROWSERS as $name => $pattern) {
            if (preg_match($pattern, $ua, $m)) {
                [$browser, $version] = [$name, $m[1]];
                break;
            }
        }

        $os = null;
        foreach (self::SYSTEMS as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                $os = $name;
                break;
            }
        }

        $device = match (true) {
            $isBot => 'bot',
            (bool) preg_match('/iPad|Tablet|Android(?!.*Mobile)/i', $ua) => 'tablet',
            (bool) preg_match('/Mobi|iPhone|iPod|Android/i', $ua) => 'mobile',
            default => 'desktop',
        };

        return ['browser' => $browser, 'browser_version' => $version, 'os' => $os, 'device' => $device, 'is_bot' => $isBot];
    }

    /** "Chrome 131 · Windows" */
    public static function summary(?string $userAgent): string
    {
        $info = self::parse($userAgent);

        return collect([trim(($info['browser'] ?? 'Navegador desconhecido').' '.$info['browser_version']), $info['os']])->filter()->implode(' · ');
    }

    public const DEVICES = [
        'desktop' => 'Computador',
        'mobile' => 'Celular',
        'tablet' => 'Tablet',
        'bot' => 'Robô',
    ];
}
