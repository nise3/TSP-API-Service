<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected Auth $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $guard = null)
    {
        if (!Auth::id()) {
            return response()->json([
                "_response_status" => [
                    "success" => false,
                    "code" => ResponseAlias::HTTP_UNAUTHORIZED,
                    "message" => "Unauthenticated action"
                ]
            ], ResponseAlias::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
