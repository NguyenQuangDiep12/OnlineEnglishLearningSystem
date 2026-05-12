@extends('layouts.dashboard')
@section('title', 'Hồ sơ cá nhân')
@section('page-title', 'Hồ sơ cá nhân')
@section('sidebar-label', 'TÀI KHOẢN')

@section('sidebar-nav')
    @php
        $role = session('role');
        $dashRoute = match($role) {
            'admin'      => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default      => route('student.dashboard'),
        };
    @endphp
    <a href="{{ $dashRoute }}" class="dash-sidebar__nav-link">
        <i class="fa-solid fa-house"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('profile.show') }}" class="dash-sidebar__nav-link active">
        <i class="fa-solid fa-user"></i><span>Hồ sơ</span>
    </a>
@endsection

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-size:14px;display:flex;gap:10px;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<div class="dash-row dash-row--2-1" style="align-items:start;">

    {{-- Thông tin hồ sơ --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Avatar --}}
        <div class="dash-card" style="text-align:center;padding:40px 32px;">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->fullname }}"
                     style="width:96px;height:96px;border-radius:50%;object-fit:cover;margin:0 auto 16px;display:block;border:3px solid #e2e8f0;">
            @else
                <div style="width:96px;height:96px;border-radius:50%;background:rgb(40,40,254);color:white;font-size:36px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    {{ mb_substr($user->fullname, 0, 1) }}
                </div>
            @endif
            <h2 style="font-size:20px;font-weight:700;color:#0f172a;margin-bottom:4px;">{{ $user->fullname }}</h2>
            <span class="dash-badge dash-badge--{{ $user->role->value }}">
                {{ match($user->role->value) { 'student'=>'Học viên','instructor'=>'Giảng viên','admin'=>'Admin',default=>$user->role->value } }}
            </span>
            <p style="font-size:13px;color:#94a3b8;margin-top:8px;">{{ $user->email }}</p>
            <p style="font-size:12px;color:#cbd5e1;margin-top:4px;">Tham gia {{ $user->created_at->format('d/m/Y') }}</p>

            {{-- Upload avatar --}}
            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" style="margin-top:20px;">
                @csrf
                <label style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;color:#475569;transition:all 0.2s;"
                       onmouseover="this.style.borderColor='rgb(40,40,254)';this.style.color='rgb(40,40,254)'"
                       onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569'">
                    <i class="fa-solid fa-camera"></i> Đổi ảnh đại diện
                    <input type="file" name="avatar" accept="image/*" style="display:none;" onchange="this.closest('form').submit()">
                </label>
            </form>
        </div>

        {{-- Chỉnh sửa thông tin --}}
        <div class="dash-card">
            <div class="dash-card__header">
                <h3 class="dash-card__title">Thông tin cá nhân</h3>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
                @csrf @method('PUT')
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Họ và tên</label>
                    <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}"
                           style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                           onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'" required>
                    @error('fullname')<p style="font-size:12px;color:#ef4444;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                           onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'" required>
                    @error('email')<p style="font-size:12px;color:#ef4444;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                        style="padding:12px 24px;background:rgb(40,40,254);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;align-self:flex-start;">
                    Lưu thay đổi
                </button>
            </form>
        </div>

        {{-- Đổi mật khẩu --}}
        <div class="dash-card">
            <div class="dash-card__header">
                <h3 class="dash-card__title">Đổi mật khẩu</h3>
            </div>
            <form action="{{ route('profile.password') }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
                @csrf
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Mật khẩu mới</label>
                    <input type="password" name="new_password"
                           style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;" required minlength="8"
                           onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Xác nhận mật khẩu mới</label>
                    <input type="password" name="new_password_confirmation"
                           style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;" required
                           onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <button type="submit"
                        style="padding:12px 24px;background:#f1f5f9;color:#374151;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;align-self:flex-start;">
                    Đổi mật khẩu
                </button>
            </form>
        </div>
    </div>

    {{-- Sidebar stats --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Thống kê học viên --}}
        @if($user->role->value === 'student')
        <div class="dash-card">
            <div class="dash-card__header"><h3 class="dash-card__title">Thống kê học tập</h3></div>
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:14px;">
                    <span style="color:#64748b;display:flex;align-items:center;gap:8px;"><i class="fa-solid fa-book-open" style="color:rgb(40,40,254);"></i> Khóa học đã đăng ký</span>
                    <strong>{{ $user->enrollments->count() }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:14px;">
                    <span style="color:#64748b;display:flex;align-items:center;gap:8px;"><i class="fa-solid fa-certificate" style="color:#f59e0b;"></i> Chứng chỉ</span>
                    <strong>{{ $user->certificates->count() }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:14px;">
                    <span style="color:#64748b;display:flex;align-items:center;gap:8px;"><i class="fa-solid fa-star" style="color:#f59e0b;"></i> Đánh giá đã viết</span>
                    <strong>{{ $user->courseReviews->count() }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:14px;">
                    <span style="color:#64748b;display:flex;align-items:center;gap:8px;"><i class="fa-solid fa-credit-card" style="color:#10b981;"></i> Tổng chi tiêu</span>
                    <strong style="color:#10b981;">{{ number_format($user->payments->where('status','paid')->sum('amount'), 0, '.', '.') }}đ</strong>
                </div>
            </div>
        </div>

        {{-- Khóa học gần đây --}}
        @if($user->enrollments->count())
        <div class="dash-card">
            <div class="dash-card__header"><h3 class="dash-card__title">Khóa học gần đây</h3></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($user->enrollments->take(5) as $enrollment)
                <div style="display:flex;align-items:center;gap:10px;font-size:13px;">
                    <img src="{{ $enrollment->course->thumbnail_url ?? 'https://picsum.photos/seed/'.$enrollment->course->course_id.'/48/32' }}"
                         style="width:48px;height:32px;border-radius:6px;object-fit:cover;flex-shrink:0;">
                    <span style="color:#374151;flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                        {{ $enrollment->course->title ?? '—' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        {{-- Instructor stats --}}
        @if($user->role->value === 'instructor')
        <div class="dash-card">
            <div class="dash-card__header"><h3 class="dash-card__title">Thống kê giảng dạy</h3></div>
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div style="display:flex;justify-content:space-between;font-size:14px;">
                    <span style="color:#64748b;">Khóa học đã tạo</span>
                    <strong>{{ $user->courses->count() }}</strong>
                </div>
            </div>
        </div>
        @endif

        {{-- Quick links --}}
        <div class="dash-card">
            <div class="dash-card__header"><h3 class="dash-card__title">Liên kết nhanh</h3></div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                @if($user->role->value === 'student')
                <a href="{{ route('student.dashboard') }}" style="font-size:13px;color:rgb(40,40,254);text-decoration:none;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-chart-line"></i> Dashboard học viên
                </a>
                <a href="{{ route('student.my-courses') }}" style="font-size:13px;color:rgb(40,40,254);text-decoration:none;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-book-open"></i> Khóa học của tôi
                </a>
                <a href="{{ route('student.payment.history') }}" style="font-size:13px;color:rgb(40,40,254);text-decoration:none;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-receipt"></i> Lịch sử thanh toán
                </a>
                @elseif($user->role->value === 'instructor')
                <a href="{{ route('instructor.dashboard') }}" style="font-size:13px;color:rgb(40,40,254);text-decoration:none;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-chart-line"></i> Dashboard giảng viên
                </a>
                @else
                <a href="{{ route('admin.dashboard') }}" style="font-size:13px;color:rgb(40,40,254);text-decoration:none;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-shield-halved"></i> Admin dashboard
                </a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="background:none;border:none;font-size:13px;color:#ef4444;cursor:pointer;padding:0;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection