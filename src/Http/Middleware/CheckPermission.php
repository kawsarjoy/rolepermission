<?php

namespace KawsarJoy\RolePermission\Http\Middleware;

use Closure;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if ($request->user() === null) {
            return redirect()->route('login');
        }        
        
        if ($request->user()->hasPermission($request->route()->action['as'])) {
            return $next($request);
        }
        else if ($request->user()->hasPermission(explode('|', $permissions))) {
            return $next($request);
        }

        return response()->view('rolepermission::errors.403', ['error' => 'This action is unauthorized.'], 403);
    }
}
