<?php

namespace Salahhusa9\GeetestCaptcha\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Salahhusa9\GeetestCaptcha\Facades\GeetestCaptcha;

class ValidateGeetestCaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $value = $request->input('geetest_captcha');

        if (!GeetestCaptcha::validate($value)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'GeeTest captcha validation failed.',
                    'errors' => [
                        'geetest_captcha' => ['The GeeTest captcha verification failed.']
                    ]
                ], 422);
            }

            return back()->withErrors([
                'geetest_captcha' => 'The GeeTest captcha verification failed.'
            ])->withInput();
        }

        return $next($request);
    }
}
