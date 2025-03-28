<?php

namespace Xultech\LaravelIpWhitelist\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Xultech\LaravelIpWhitelist\Exceptions\UnauthorizedIpException;
use Xultech\LaravelIpWhitelist\Support\IpMatcher;

class IpWhitelistMiddleware
{
    /**
     * Handle the incoming request and check IP whitelisting.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If disabled, skip IP checking
        if (!config('ipwhitelist.enabled')) {
            return $next($request);
        }

        $clientIp = $request->ip();

        // Let IpMatcher resolve allowed IPs from config or DB
        if (!IpMatcher::isAllowed($clientIp)) {
            return $this->denyAccess($request);
        }

        return $next($request);
    }

    /**
     * Respond when access is denied due to IP mismatch.
     *
     * @param Request $request
     * @return Response
     */
    protected function denyAccess(Request $request): Response
    {
        $config = config('ipwhitelist.response', []);
        $type = $config['type'] ?? 'abort';

        switch ($type) {
            case 'redirect':
                return redirect()->to($config['redirect_to'] ?? '/unauthorized');

            case 'json':
                return response()->json(
                    $config['json'] ?? ['message' => 'Your IP address is not whitelisted.'],
                    $config['json']['code'] ?? 403
                );

            case 'abort':
            default:
                // Default: throw an abort-style exception
                $message = $config['message'] ?? 'Access denied. Your IP is not authorized.';
                $status = $config['status_code'] ?? 403;

                throw new UnauthorizedIpException($message, $status);

        }
    }

}