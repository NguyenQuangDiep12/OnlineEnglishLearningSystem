@extends('layouts.app')
@section('title', 'Xác minh chứng chỉ — E-Learn')
@section('content')

<section style="min-height:calc(100vh - 80px);display:flex;align-items:center;justify-content:center;background:#f8fafc;padding:60px 20px;">
    <div style="max-width:560px;width:100%;">

        <div class="dash-card" style="border-radius:20px;">
            <div style="text-align:center;margin-bottom:32px;">
                <div style="font-size:48px;margin-bottom:12px;">🔍</div>
                <h1 style="font-size:24px;font-weight:800;color:#0f172a;margin-bottom:8px;">Xác minh chứng chỉ</h1>
                <p style="font-size:15px;color:#64748b;">Nhập mã chứng chỉ để kiểm tra tính xác thực.</p>
            </div>

            <form method="GET" action="{{ route('certificate.verify') }}" style="margin-bottom:28px;">
                <div style="display:flex;gap:10px;">
                    <input type="text" name="code" value="{{ request('code') }}"
                           placeholder="Nhập mã chứng chỉ (VD: CERT-ABCD1234EFGH)"
                           style="flex:1;padding:12px 16px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-family:monospace;outline:none;"
                           onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
                    <button type="submit"
                            style="padding:12px 24px;background:rgb(40,40,254);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;">
                        Xác minh
                    </button>
                </div>
            </form>

            @if(request('code'))
                @if($cert)
                    {{-- Valid --}}
                    <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:14px;padding:28px;text-align:center;">
                        <div style="font-size:40px;margin-bottom:12px;">✅</div>
                        <h2 style="font-size:18px;font-weight:700;color:#16a34a;margin-bottom:16px;">Chứng chỉ hợp lệ!</h2>
                        <div style="display:flex;flex-direction:column;gap:10px;text-align:left;font-size:14px;">
                            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #dcfce7;">
                                <span style="color:#64748b;">Học viên</span>
                                <strong>{{ $cert->user->fullname ?? '—' }}</strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #dcfce7;">
                                <span style="color:#64748b;">Khóa học</span>
                                <strong style="max-width:260px;text-align:right;">{{ $cert->course->title ?? '—' }}</strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #dcfce7;">
                                <span style="color:#64748b;">Cấp ngày</span>
                                <strong>{{ $cert->issued_at?->format('d/m/Y') ?? '—' }}</strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:8px 0;">
                                <span style="color:#64748b;">Mã chứng chỉ</span>
                                <strong style="font-family:monospace;">{{ $cert->cert_code }}</strong>
                            </div>
                        </div>
                        <a href="{{ route('certificate.show', $cert->cert_code) }}"
                           style="display:inline-flex;align-items:center;gap:8px;margin-top:20px;padding:10px 20px;background:white;border:1.5px solid #16a34a;border-radius:8px;font-size:13px;font-weight:600;color:#16a34a;text-decoration:none;">
                            <i class="fa-solid fa-eye"></i> Xem chứng chỉ
                        </a>
                    </div>
                @else
                    {{-- Invalid --}}
                    <div style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:14px;padding:28px;text-align:center;">
                        <div style="font-size:40px;margin-bottom:12px;">❌</div>
                        <h2 style="font-size:18px;font-weight:700;color:#dc2626;margin-bottom:8px;">Không tìm thấy chứng chỉ</h2>
                        <p style="font-size:14px;color:#64748b;">Mã <code style="background:#fee2e2;padding:2px 8px;border-radius:4px;font-family:monospace;">{{ request('code') }}</code> không hợp lệ hoặc không tồn tại.</p>
                    </div>
                @endif
            @endif
        </div>

        <p style="text-align:center;margin-top:20px;font-size:13px;color:#94a3b8;">
            <a href="/" style="color:#64748b;text-decoration:none;">← Về trang chủ</a>
        </p>
    </div>
</section>

@endsection