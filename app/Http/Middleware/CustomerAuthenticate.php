<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('is_admin', false)) {
            return redirect()
                ->route('admin.dashboard')
                ->withErrors(['auth' => '当前是管理员会话，请使用后台功能页面。']);
        }

        if (! is_array($request->session()->get('customer'))) {
            return redirect()
                ->route('customer.login')
                ->withErrors(['auth' => '请先登录用户账号后再访问该页面。']);
        }

        return $next($request);
    }
}
