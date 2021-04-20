<?php

namespace App\Http\Middleware;

use Closure;

class RemoveIndexMiddleware
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
        if ($request->has('items.__INDEX__')) {
            $data = $request->all();
            unset($data['items']['__INDEX__']);
            $request->request->replace($data);

        }
        return $next($request);
    }
}
