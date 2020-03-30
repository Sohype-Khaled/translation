<?php

namespace Codtail\Translation\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class TranslationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Session::exists('locale'))
            Session::put('locale', config('translation.locale'));
        \App::setLocale(Session::get('locale'));
        return $next($request);
    }
}
