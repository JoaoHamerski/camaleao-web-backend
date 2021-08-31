<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        foreach ($roles as $role) {
            if (
                $request->user()->hasRole(['costura', 'estampa'])
                && $request->getRequestUri() === '/'
            ) {
                return redirect()->route('production.home');
            }

            if (
                $request->user()->hasRole('design')
                && $request->getrequestUri() === '/'
            ) {
                return redirect()->route('production-calendar.index');
            }

            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        return abort(403);
    }
}
