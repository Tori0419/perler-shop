<?php

namespace App\Http\Controllers;

use App\Services\CustomerAuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function showLogin(Request $request, CustomerAuthService $customerAuthService)
    {
        if ($request->session()->has('customer')) {
            return redirect()->route('shop.index');
        }

        if ($request->session()->get('is_admin', false)) {
            return redirect()
                ->route('admin.dashboard')
                ->withErrors(['auth' => '当前是管理员会话，请先退出管理员后再登录用户。']);
        }

        return view('auth.login', [
            'accounts' => $customerAuthService->allAccounts(),
        ]);
    }

    public function login(Request $request, CustomerAuthService $customerAuthService)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $customer = $customerAuthService->attempt($validated['username'], $validated['password']);

        if (! $customer) {
            throw ValidationException::withMessages([
                'auth' => '用户账号或密码错误。',
            ]);
        }

        $request->session()->forget(['is_admin', 'admin_username']);
        $request->session()->put('customer', $customer);

        return redirect()
            ->intended(route('shop.index'))
            ->with('success', "欢迎回来，{$customer['name']}！");
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['customer', 'cart']);

        return redirect()
            ->route('customer.login')
            ->with('success', '用户已退出登录。');
    }
}
