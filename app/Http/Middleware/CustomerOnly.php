<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && (auth()->user()->role ?? '') === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin không sử dụng chức năng của khách.');
        }
        return $next($request);
    }
}
