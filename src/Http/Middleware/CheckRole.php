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
        if(!config('permissions-config.rolepermission-enable'))
            return $next($request);
        
        if ($request->user() === null) {
            return redirect()->route(config('permissions-config.login-route'));
        }
        
        if ($request->user()->hasRole(explode('|', $roles))) {
            return $next($request);
        }

        return response()->view('rolepermission::errors.403', ['error' => 'This action is unauthorized.'], 403);
    }
}