<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
        public function handle(Request $request, Closure $next): Response
    {
       //dd(session());
        if(! session()->has('User_UIN')){
            return response()->view('errors.sessionexpires',[],401);
        }
        
        if(!session()->has('organization_uin')){
        // Avoid redirect loop
            if (! $request->routeIs('organization.select')) {
                return redirect()->route('organization.select');
            }
        }
        return $next($request);
    }
}
