@extends('layouts.dashboard')
@section('title', 'Lịch sử thanh toán')
@section('page-title', 'Lịch sử thanh toán')
@section('sidebar-label', 'HỌC VIÊN')
@section('sidebar-nav')
    <a href="{{ route('student.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-house"></i><span>Tổng quan</span></a>
    <a href="{{ route('student.my-courses') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-book-open"></i><span>Khóa học của tôi</span></a>
    <a href="{{ route('student.certificates') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-certificate"></i><span>Chứng chỉ</span></a>
    <a href="{{ route('student.payment.history') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-receipt"></i><span>Lịch sử thanh toán</span></a>
    <div class="dash-sidebar__divider"></div>
    <a href="/courses" class="dash-sidebar__nav-link"><i class="fa-solid fa-magnifying-glass"></i><span>Khám phá</span></a>
@endsection
@section('content')

@if($payments->isEmpty())
<div class="dash-card" style="text-align:center;padding:60px 32px;">
    <div style="font-size:48px;margin-bottom:16px;">💳</div>
    <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;">Chưa có giao dịch nào</h3>
    <p style="color:#64748b;margin-bottom:24px;">Đăng ký một khóa học để bắt đầu.</p>
    <a href="{{ route('courses.index') }}" class="btn btn--secondary">Xem khóa học</a>
</div>
@else
<div class="dash-card dash-card--no-pad">
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Mã giao dịch</th>
                    <th>Khóa học</th>
                    <th>Số tiền</th>
                    <th>Phương thức</th>
                    <th>Trạng thái</th>
                    <th>Ngày</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td class="dash-table__mono">{{ strtoupper(substr($payment->transaction_ref, 0, 12)) }}</td>
                    <td>{{ $payment->course->title ?? '—' }}</td>
                    <td><strong>{{ number_format($payment->amount, 0, '.', '.') }}đ</strong></td>
                    <td class="dash-table__muted">{{ $payment->payment_method }}</td>
                    <td>
                        <span class="dash-badge dash-badge--{{ $payment->status }}">
                            {{ match($payment->status) {
                                'paid'     => 'Đã thanh toán',
                                'pending'  => 'Chờ xử lý',
                                'failed'   => 'Thất bại',
                                'refunded' => 'Đã hoàn',
                                default    => $payment->status,
                            } }}
                        </span>
                    </td>
                    <td class="dash-table__muted">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection