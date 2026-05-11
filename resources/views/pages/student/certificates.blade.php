@extends('layouts.dashboard')
@section('title', 'Chứng chỉ của tôi')
@section('page-title', 'Chứng chỉ')
@section('sidebar-label', 'HỌC VIÊN')
@section('sidebar-nav')
    <a href="{{ route('student.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-house"></i><span>Tổng quan</span></a>
    <a href="{{ route('student.my-courses') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-book-open"></i><span>Khóa học của tôi</span></a>
    <a href="{{ route('student.certificates') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-certificate"></i><span>Chứng chỉ</span></a>
@endsection
@section('content')

@if($certs->isEmpty())
<div class="dash-card" style="text-align:center;padding:60px 32px;">
    <div style="font-size:48px;margin-bottom:16px;">🎓</div>
    <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;">Chưa có chứng chỉ nào</h3>
    <p style="color:#64748b;margin-bottom:24px;">Hoàn thành 100% bài học trong một khóa học để nhận chứng chỉ.</p>
    <a href="{{ route('student.my-courses') }}" class="btn btn--secondary">Xem khóa học của tôi</a>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;">
    @foreach($certs as $cert)
    <div style="background:linear-gradient(135deg,#fffbeb,white);border:1px solid #fde68a;border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        {{-- Header vàng --}}
        <div style="background:linear-gradient(135deg,#f59e0b,#fbbf24);padding:24px;text-align:center;">
            <div style="font-size:48px;margin-bottom:8px;">🏆</div>
            <h3 style="font-size:16px;font-weight:800;color:white;margin:0;">Chứng chỉ hoàn thành</h3>
        </div>
        {{-- Body --}}
        <div style="padding:20px;">
            <h4 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:6px;line-height:1.4;">
                {{ $cert->course->title ?? 'Khóa học' }}
            </h4>
            <p style="font-size:12px;color:#94a3b8;margin-bottom:16px;">
                Cấp ngày: {{ $cert->issued_at?->format('d/m/Y') ?? '—' }}
            </p>
            <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:8px;padding:10px;font-family:monospace;font-size:13px;color:#92400e;text-align:center;margin-bottom:16px;word-break:break-all;">
                {{ $cert->cert_code }}
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('certificate.show', $cert->cert_code) }}"
                   style="flex:1;text-align:center;padding:10px;background:white;border:1.5px solid #f59e0b;border-radius:8px;font-size:13px;font-weight:600;color:#d97706;text-decoration:none;">
                    <i class="fa-solid fa-eye"></i> Xem
                </a>
                <a href="{{ route('certificate.verify') }}?code={{ $cert->cert_code }}"
                   style="flex:1;text-align:center;padding:10px;background:#f59e0b;border:none;border-radius:8px;font-size:13px;font-weight:600;color:white;text-decoration:none;">
                    <i class="fa-solid fa-shield-check"></i> Xác minh
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection