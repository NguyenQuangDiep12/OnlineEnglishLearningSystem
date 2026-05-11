<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Interfaces\IUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct(
        protected IUserService $userService
    ) {}

    public function show()
    {
        $user = User::with([
            'enrollments.course',
            'certificates.course',
            'courseReviews.course',
        ])->findOrFail(session('user_id'));

        return view('pages.profile', compact('user'));
    }

    public function edit()
    {
        $user = User::findOrFail(session('user_id'));
        return view('pages.profile-edit', compact('user')); // sua doan nay thanh popup sua tren trang profile
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(session('user_id'));

        $data = $request->validate([
            'fullname' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
        ]);

        $this->userService->updateProfile($user, $data);

        // Cập nhật session
        session(['fullname' => $data['fullname']]);

        return redirect()->route('profile.show')
            ->with('success', 'Cập nhật thông tin thành công.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');
        $url  = Storage::url($path);

        $this->userService->updateAvatar(session('user_id'), $url);

        return back()->with('success', 'Cập nhật ảnh đại diện thành công.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        $this->userService->changePassword(session('user_id'), $request->new_password);

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }
}

?>