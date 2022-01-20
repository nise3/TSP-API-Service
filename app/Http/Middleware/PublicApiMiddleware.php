<?php

namespace App\Http\Middleware;

use App\Models\BaseModel;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PublicApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws RequestException
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->headers->has('Domain')){
            Log::info("AAAAAAAAAAAAAAAA");
            Log::info($request->path());

            $domain = $request->headers->get('Domain');
            $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'service-to-service-call/domain-identification/' . $domain;

            $response = Http::withOptions(['debug' => config("nise3.is_dev_mode"), 'verify' => config("nise3.should_ssl_verify")])
                ->get($url)
                ->throw(function ($response, $exception) {
                    return $exception;
                })
                ->json();

            if(!empty($response['data']['institute_id'])){
                $request->offsetSet('institute_id', $response['data']['institute_id']);
            }
        }else{
            Log::info("BBBBBBBBBBBBBBBBBBBBBBB");
            Log::info($request->path());


            return response()->json([
                "_response_status" => [
                    "success" => false,
                    "code" => ResponseAlias::HTTP_NOT_FOUND,
                    "message" => "Domain name not found in header"
                ]
            ], ResponseAlias::HTTP_UNAUTHORIZED);
        }

        return $next($request);

    }
}
