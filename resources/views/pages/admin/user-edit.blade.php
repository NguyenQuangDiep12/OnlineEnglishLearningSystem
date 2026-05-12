@extends('layouts.dashboard')
@section('title', 'Chỉnh sửa người dùng — ' . $user->fullname)
@section('page-title', 'Chỉnh sửa người dùng')
@section('sidebar-label', 'QUẢN TRỊ')

@section('sidebar-nav')
    <a href="{{ route('admin.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-chart-pie"></i><span>Tổng quan</span></a>
    <a href="{{ route('admin.users') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-users"></i><span>Người dùng</span></a>
    <a href="{{ route('admin.courses') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-book-open"></i><span>Khóa học</span></a>
@endsection

@section('content')

<a href="{{ route('admin.users.show', $user->user_id) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;margin-bottom:20px;">
    <i class="fa-solid fa-arrow-left"></i> Quay lại chi tiết
</a>

<div style="max-width:600px;display:flex;flex-direction:column;gap:20px;">

    {{-- Edit form --}}
    <div class="dash-card">
        <div class="dash-card__header">
            <h3 class="dash-card__title">Chỉnh sửa thông tin</h3>
        </div>

        @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-size:14px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#dc2626;font-size:14px;">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
        @endif

        <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST"
              style="display:flex;flex-direction:column;gap:16px;">
            @csrf @method('PUT')

            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Họ và tên</label>
                <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" required
                       style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                       onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                       onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Vai trò</label>
                <select name="role" required
                        style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;appearance:none;background:white url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\") no-repeat right 12px center;"
                        onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="student"    {{ $user->role->value == 'student'    ? 'selected' : '' }}>Học viên</option>
                    <option value="instructor" {{ $user->role->value == 'instructor' ? 'selected' : '' }}>Giảng viên</option>
                    <option value="admin"      {{ $user->role->value == 'admin'      ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="padding:12px 28px;background:rgb(40,40,254);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
                    Lưu thay đổi
                </button>
                <a href="{{ route('admin.users.show', $user->user_id) }}"
                   style="padding:12px 20px;background:#f1f5f9;color:#374151;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;">
                    Hủy
                </a>
            </div>
        </form>
    </div>

    {{-- Reset password --}}
    <div class="dash-card">
        <div class="dash-card__header">
            <h3 class="dash-card__title">Đặt lại mật khẩu</h3>
        </div>
        <form action="{{ route('admin.users.reset-password', $user->user_id) }}" method="POST"
              style="display:flex;flex-direction:column;gap:16px;">
            @csrf
            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Mật khẩu mới</label>
                <input type="password" name="password" required minlength="8"
                       style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                       onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" required
                       style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                       onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <button type="submit"
                        style="padding:12px 24px;background:#f1f5f9;color:#374151;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
                    <i class="fa-solid fa-key"></i> Đặt lại mật khẩu
                </button>
            </div>
        </form>
    </div>

</div>
@endsection