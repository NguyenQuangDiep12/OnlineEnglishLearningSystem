@extends('layouts.app')
@section('title', 'Chứng chỉ — ' . ($cert->course->title ?? 'E-Learn'))
@section('content')

<section style="min-height:calc(100vh - 80px);display:flex;align-items:center;justify-content:center;background:#f8fafc;padding:60px 20px;">
    <div style="max-width:720px;width:100%;">

        {{-- Certificate card --}}
        <div style="background:white;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.12);border:1px solid #e2e8f0;">

            {{-- Gold header --}}
            <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,rgb(40,40,254) 100%);padding:48px 60px 40px;text-align:center;color:white;position:relative;">
                <div style="position:absolute;top:16px;left:16px;opacity:0.15;font-size:80px;line-height:1;">🏆</div>
                <div style="position:absolute;top:16px;right:16px;opacity:0.15;font-size:80px;line-height:1;">🏆</div>
                <div style="font-size:14px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#93c5fd;margin-bottom:16px;">
                    Chứng chỉ hoàn thành
                </div>
                <div style="font-size:48px;margin-bottom:8px;">🏆</div>
                <h1 style="font-size:28px;font-weight:800;margin:0;">E-Learn</h1>
                <p style="font-size:13px;color:rgba(255,255,255,0.6);margin-top:4px;">Nền tảng học tiếng Anh trực tuyến</p>
            </div>

            {{-- Content --}}
            <div style="padding:48px 60px;text-align:center;">
                <p style="font-size:15px;color:#64748b;margin-bottom:8px;">Chứng nhận rằng</p>
                <h2 style="font-size:32px;font-weight:800;color:#0f172a;margin-bottom:8px;">
                    {{ $cert->user->fullname ?? 'Học viên' }}
                </h2>
                <p style="font-size:15px;color:#64748b;margin-bottom:16px;">đã hoàn thành thành công khóa học</p>
                <h3 style="font-size:22px;font-weight:700;color:rgb(40,40,254);margin-bottom:8px;line-height:1.4;">
                    {{ $cert->course->title ?? 'Khóa học' }}
                </h3>

                <div style="display:flex;justify-content:center;gap:32px;padding:24px 0;border-top:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;margin:24px 0;">
                    <div>
                        <div style="font-size:13px;color:#94a3b8;margin-bottom:4px;">Cấp ngày</div>
                        <div style="font-size:15px;font-weight:600;color:#0f172a;">{{ $cert->issued_at?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:13px;color:#94a3b8;margin-bottom:4px;">Cấp độ</div>
                        <div style="font-size:15px;font-weight:600;color:#0f172a;">{{ ucfirst(str_replace('_',' ',$cert->course->level ?? '')) }}</div>
                    </div>
                    <div>
                        <div style="font-size:13px;color:#94a3b8;margin-bottom:4px;">Giảng viên</div>
                        <div style="font-size:15px;font-weight:600;color:#0f172a;">{{ $cert->course->user->fullname ?? 'E-Learn' }}</div>
                    </div>
                </div>

                {{-- Cert code --}}
                <div style="background:#f8fafc;border:1px dashed #e2e8f0;border-radius:10px;padding:16px 24px;margin-bottom:28px;">
                    <p style="font-size:12px;color:#94a3b8;margin-bottom:4px;">Mã chứng chỉ</p>
                    <p style="font-family:monospace;font-size:16px;font-weight:700;color:#0f172a;letter-spacing:2px;">{{ $cert->cert_code }}</p>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                    <a href="{{ route('certificate.verify') }}?code={{ $cert->cert_code }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:#f1f5f9;color:#374151;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;">
                        <i class="fa-solid fa-shield-check"></i> Xác minh
                    </a>
                    <a href="/"
                       style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:rgb(40,40,254);color:white;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;">
                        <i class="fa-solid fa-home"></i> Về trang chủ
                    </a>
                </div>
            </div>
        </div>

        {{-- Back --}}
        @if(session('user_id'))
        <div style="text-align:center;margin-top:24px;">
            <a href="{{ route('student.certificates') }}" style="font-size:14px;color:#64748b;text-decoration:none;">
                ← Quay lại danh sách chứng chỉ
            </a>
        </div>
        @endif
    </div>
</section>

@endsection