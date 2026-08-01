<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthForDocs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedUsername = env('DOCS_USERNAME', 'admin');
        $expectedPassword = env('DOCS_PASSWORD', 'diva2026');

        if ($request->getUser() !== $expectedUsername || $request->getPassword() !== $expectedPassword) {
            $headers = ['WWW-Authenticate' => 'Basic realm="Secure Area"'];
            return response('Unauthorized', 401, $headers);
        }

        return $next($request);
    }
}
