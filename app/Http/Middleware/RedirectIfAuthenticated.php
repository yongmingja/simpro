<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        if ($guard === 'admin' && Auth::guard($guard)->check()) {
            return redirect('/admin');
        }
        if ($guard === 'mahasiswa' && Auth::guard($guard)->check()) {
            return redirect('/mahasiswa');
        }
        if ($guard === 'dosen' && Auth::guard($guard)->check()) {
            return redirect('/dosen');
        }
        if ($guard === 'dekan' && Auth::guard($guard)->check()) {
            return redirect('/dekan');
        }
        if ($guard === 'rektorat' && Auth::guard($guard)->check()) {
            return redirect('/rektorat');
        }
        if (Auth::guard($guard)->check()) {
            return redirect('/home');
        }

        return $next($request);
    }
}
