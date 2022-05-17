<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        
        // switch (Auth::guard($guard)->check()) {
        //     case true:
        //     return route('home');
        //         break;
        //     case false:
        //        return redirect()->guest(route('login'));
        //         break;
        //     default:
        //        return redirect()->guest(route('login'));
        // }
        if (Auth::guard($guard)->check()) {
            return redirect(RouteServiceProvider::HOME);
        }
        else if(Auth::guard('delivery')->check())
        {
            return redirect()->route("delivery.home");
        }
        return $next($request);
    }
}
