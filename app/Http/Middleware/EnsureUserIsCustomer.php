<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    /**
     * Chỉ khách hàng mới có giỏ hàng. Tài khoản admin bị chặn.
     * Luôn dùng sau middleware 'auth'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin()) {
            abort(403, 'Tài khoản quản trị không sử dụng giỏ hàng.');
        }

        return $next($request);
    }
}
