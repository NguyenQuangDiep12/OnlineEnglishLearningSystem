<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();
        return view('pages.admin.usermanager', compact('users'));
    }

    public function show(int $userId)
    {
        $user = User::with([
            'enrollments.course',
            'payments',
            'certificates',
        ])->findOrFail($userId);

        return view('pages.admin.user-detail', compact('user'));
    }

    public function edit(int $userId)
    {
        $user = User::findOrFail($userId);
        return view('pages.admin.user-edit', compact('user'));
    }

    public function update(Request $request, int $userId)
    {
        $user = User::findOrFail($userId);

        $data = $request->validate([
            'fullname' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $userId . ',user_id',
            'role'     => 'required|in:student,instructor,admin',
        ]);

        $user->update($data);

        return redirect()->route('admin.users')
            ->with('success', 'Đã cập nhật thông tin người dùng.');
    }

    public function resetPassword(Request $request, int $userId)
    {
        $data = $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        User::findOrFail($userId)->update([
            'password_hash' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Đã đặt lại mật khẩu.');
    }

    public function destroy(int $userId)
    {
        if ($userId === session('user_id')) {
            return back()->withErrors('Không thể xoá tài khoản đang đăng nhập.');
        }

        User::findOrFail($userId)->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Đã xoá người dùng.');
    }
}

?>