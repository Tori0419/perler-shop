<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->get('is_admin', false)) {
            return redirect()->route('admin.dashboard');
        }

        if (is_array($request->session()->get('customer'))) {
            return redirect()
                ->route('shop.index')
                ->withErrors(['auth' => '当前是用户会话，请先退出用户后再登录管理员。']);
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (
            $validated['username'] !== (string) config('admin.username') ||
            $validated['password'] !== (string) config('admin.password')
        ) {
            throw ValidationException::withMessages([
                'auth' => '管理员账号或密码错误。',
            ]);
        }

        $request->session()->forget(['customer', 'cart']);
        $request->session()->put('is_admin', true);
        $request->session()->put('admin_username', $validated['username']);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', '管理员登录成功。');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['is_admin', 'admin_username']);

        return redirect()
            ->route('admin.login')
            ->with('success', '已退出管理员账号。');
    }
}
