@extends('layouts.dashboard')
@section('title', 'Chi tiết người dùng — ' . $user->fullname)
@section('page-title', 'Chi tiết người dùng')
@section('sidebar-label', 'QUẢN TRỊ')

@section('sidebar-nav')
    <a href="{{ route('admin.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-chart-pie"></i><span>Tổng quan</span></a>
    <a href="{{ route('admin.users') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-users"></i><span>Người dùng</span></a>
    <a href="{{ route('admin.courses') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-book-open"></i><span>Khóa học</span></a>
@endsection

@section('content')

<a href="{{ route('admin.users') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;margin-bottom:20px;">
    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
</a>

<div class="dash-row dash-row--2-1" style="align-items:start;">

    <div style="display:flex;flex-direction:column;gap:20px;">
        {{-- Basic info --}}
        <div class="dash-card">
            <div class="dash-card__header">
                <h3 class="dash-card__title">Thông tin cơ bản</h3>
                <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn--sm btn--primary">
                    <i class="fa-solid fa-pen"></i> Chỉnh sửa
                </a>
            </div>
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:24px;">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" style="width:72px;height:72px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                @else
                    <div style="width:72px;height:72px;border-radius:50%;background:hsl({{ $user->user_id * 47 % 360 }},60%,55%);color:white;font-size:28px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        {{ mb_substr($user->fullname, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:4px;">{{ $user->fullname }}</h2>
                    <span class="dash-badge dash-badge--{{ $user->role->value }}">
                        {{ match($user->role->value) { 'student'=>'Học viên','instructor'=>'Giảng viên','admin'=>'Admin',default=>$user->role->value } }}
                    </span>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:14px;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f8fafc;">
                    <span style="color:#64748b;">ID</span><strong>#{{ $user->user_id }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f8fafc;">
                    <span style="color:#64748b;">Email</span><strong>{{ $user->email }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f8fafc;">
                    <span style="color:#64748b;">Vai trò</span>
                    <span class="dash-badge dash-badge--{{ $user->role->value }}">{{ $user->role->value }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;">
                    <span style="color:#64748b;">Tham gia</span><strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong>
                </div>
            </div>
        </div>

        {{-- Enrollments --}}
        <div class="dash-card">
            <div class="dash-card__header">
                <h3 class="dash-card__title">Khóa học đã đăng ký ({{ $user->enrollments->count() }})</h3>
            </div>
            @if($user->enrollments->isEmpty())
                <p style="color:#94a3b8;font-size:13px;">Chưa đăng ký khóa học nào.</p>
            @else
            <div class="dash-table-wrap">
                <table class="dash-table">
                    <thead><tr><th>Khóa học</th><th>Ngày đăng ký</th></tr></thead>
                    <tbody>
                        @foreach($user->enrollments->take(10) as $enrollment)
                        <tr>
                            <td>{{ $enrollment->course->title ?? '—' }}</td>
                            <td class="dash-table__muted">{{ $enrollment->enrolled_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Certificates --}}
        <div class="dash-card">
            <div class="dash-card__header">
                <h3 class="dash-card__title">Chứng chỉ ({{ $user->certificates->count() }})</h3>
            </div>
            @if($user->certificates->isEmpty())
                <p style="color:#94a3b8;font-size:13px;">Chưa có chứng chỉ.</p>
            @else
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($user->certificates->take(5) as $cert)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f8fafc;font-size:13px;">
                    <span>{{ $cert->course->title ?? '—' }}</span>
                    <span style="font-family:monospace;color:#64748b;font-size:11px;">{{ $cert->cert_code }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Right sidebar --}}
    <div style="display:flex;flex-direction:column;gap:20px;">
        {{-- Stats --}}
        <div class="dash-card">
            <div class="dash-card__header"><h3 class="dash-card__title">Thống kê</h3></div>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:14px;">
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:#64748b;">Khóa học</span>
                    <strong>{{ $user->enrollments->count() }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:#64748b;">Chứng chỉ</span>
                    <strong>{{ $user->certificates->count() }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:#64748b;">Tổng chi</span>
                    <strong style="color:#10b981;">{{ number_format($user->payments->where('status','paid')->sum('amount'), 0, '.', '.') }}đ</strong>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="dash-card">
            <div class="dash-card__header"><h3 class="dash-card__title">Thao tác</h3></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <a href="{{ route('admin.users.edit', $user->user_id) }}"
                   style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#eff6ff;color:rgb(40,40,254);border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
                    <i class="fa-solid fa-pen"></i> Chỉnh sửa thông tin
                </a>
                @if($user->user_id !== session('user_id'))
                <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST"
                      onsubmit="return confirm('Xóa người dùng {{ $user->fullname }}?');">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="width:100%;display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fef2f2;color:#ef4444;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                        <i class="fa-solid fa-trash"></i> Xóa người dùng
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection