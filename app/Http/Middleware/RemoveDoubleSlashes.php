<?php

namespace App\Http\Middleware;

use Closure;

class RemoveDoubleSlashes
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
        $path = $request->path();
        $newPath = str_replace('//', '/', $path);
        if ($newPath !== $path) {
            $query = $request->getQueryString();
            if ($query) {
                $newUrl = "{$newPath}?{$query}";
            } else {
                $newUrl = $newPath;
            }
            return redirect($newUrl, 301);
        }
        return $next($request);
    }
}
