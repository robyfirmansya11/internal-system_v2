<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate(['locale' => ['required', Rule::in(SetLocale::SUPPORTED)]]);
        $request->session()->put('locale', $data['locale']);
        // Return only to a local path supplied by our switcher, never an external URL.
        $path = $request->input('return_to', '/portal');
        if (! is_string($path) || ! str_starts_with($path, '/') || str_starts_with($path, '//') || str_contains($path, '\\') || preg_match('/[\x00-\x20\x7f]/', $path)) {
            $path = '/portal';
        }

        return redirect($path)->withCookie(cookie('locale', $data['locale'], 60 * 24 * 365));
    }
}
