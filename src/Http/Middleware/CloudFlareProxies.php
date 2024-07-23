<?php

namespace Antriver\LaravelSiteUtils\Http\Middleware;

use Cache;
use Closure;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class CloudFlareProxies
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $proxyIps = Cache::remember(
            'cloudFlareProxyIps',
            1440,
            function () {
                $url = 'https://www.cloudflare.com/ips-v4';
                try {
                    $ips = file_get_contents($url);
                    $ips = explode("\n", $ips);
                } catch (Exception $e) {
                    $ips = [];
                }

                return array_filter($ips);
            }
        );

        $request->setTrustedProxies(
            $proxyIps,
            Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB,
        );

        return $next($request);
    }
}
