<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class SrPermissions
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
        if(!Gate::allows('isSalesRepresentative') && !Gate::allows('isDistributor') && !Gate::allows('isAdmin')){
            abort(403);
        }
        
        return $next($request);
    }
}
