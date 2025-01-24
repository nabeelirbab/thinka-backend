<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminMiddleware
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
        $user = Session::get('user'); // Retrieve the user object from the session

        // Check if the user exists and if they are an admin
        if ($user && isset($user->is_admin) && $user->is_admin) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Access denied. Admins only.');
    }
}
