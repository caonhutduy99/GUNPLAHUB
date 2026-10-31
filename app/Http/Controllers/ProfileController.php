<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $ordersCount = $user->orders()->count();
        $totalSpent = $user->orders()->revenue()->sum('total_amount');
        $recentOrders = $user->orders()->latest()->take(5)->get();

        return view('profile.show', compact('user', 'ordersCount', 'totalSpent', 'recentOrders'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'regex:/^(0|\+84)[0-9]{9}$/',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'address' => ['nullable', 'string', 'min:10', 'max:500'],
        ], array_merge(ValidationMessages::common(), [
            'phone.regex' => 'Số điện thoại không đúng định dạng (ví dụ: 0912345678).',
            'phone.unique' => 'Số điện thoại này đã được tài khoản khác sử dụng.',
            'address.min' => 'Địa chỉ phải có ít nhất 10 ký tự để đảm bảo giao hàng chính xác.',
        ]), [
            'full_name' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ giao hàng mặc định',
        ]);

        $user->update($data);
        AuditLog::record("Cập nhật thông tin hồ sơ cá nhân: {$user->full_name} ({$user->phone})");

        return back()->with('success', 'Đã cập nhật hồ sơ cá nhân thành công.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], array_merge(ValidationMessages::common(), [
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.mixed' => 'Mật khẩu mới phải có cả chữ hoa và chữ thường.',
            'password.numbers' => 'Mật khẩu mới phải có ít nhất một chữ số.',
            'password.symbols' => 'Mật khẩu mới phải có ít nhất một ký tự đặc biệt.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]), [
            'current_password' => 'Mật khẩu hiện tại',
            'password' => 'Mật khẩu mới',
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        AuditLog::record("Khách hàng {$user->email} đổi mật khẩu tài khoản");

        return back()->with('success', 'Đã đổi mật khẩu thành công.');
    }
}
