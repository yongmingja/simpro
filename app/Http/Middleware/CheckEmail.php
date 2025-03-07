<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckEmail
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
        if (Auth::check() && empty(Auth::user()->email)) {
            return redirect()->route('profile');
        }

        return $next($request);
    }
}
