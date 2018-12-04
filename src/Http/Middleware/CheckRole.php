<?php

namespace KawsarJoy\RolePermission\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        if ($request->user() === null) {
            return redirect()->route('login');
        }
        
        if ($request->user()->hasRole(explode('|', $roles))) {
            return $next($request);
        }

        return response()->view('errors.403', ['error' => 'This action is unauthorized.'], 403);
    }
}