<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        view()->share('pageTitle', $request->route()->getAction()['as'] ?? 'Dashboard');

        return $next($request);
    }
}
