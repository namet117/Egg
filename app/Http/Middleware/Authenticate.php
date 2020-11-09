<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $current_full_path = $request->path()
            . ($request->getQueryString() ? ('?' . $request->getQueryString()) : '');
        $current_full_path = $current_full_path ? "/{$current_full_path}" : '/';
        $request->session()->put('login_redirect', $current_full_path);
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
