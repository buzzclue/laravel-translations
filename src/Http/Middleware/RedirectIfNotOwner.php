<?php

namespace Outhebox\TranslationsUI\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotOwner
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
