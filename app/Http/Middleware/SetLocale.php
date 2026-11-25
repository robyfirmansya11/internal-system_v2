<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public const SUPPORTED = ['en', 'id', 'zh_CN'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('locale', $request->cookie('locale', config('app.locale')));
        app()->setLocale(in_array($locale, self::SUPPORTED, true) ? $locale : 'en');

        return $next($request);
    }
}
