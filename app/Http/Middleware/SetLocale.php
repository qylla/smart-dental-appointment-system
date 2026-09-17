<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Supported locales. Anything outside this list is ignored, so a
     * crafted URL like /lang/xx cannot change the application locale.
     */
    protected array $supported = ['en', 'ms'];

    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, $this->supported, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}