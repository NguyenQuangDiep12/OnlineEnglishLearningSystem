@extends('layouts.dashboard')
@section('title', 'Khóa học của tôi')
@section('page-title', 'Khóa học của tôi')
@section('sidebar-label', 'HỌC VIÊN')
@section('sidebar-nav')
    <a href="{{ route('student.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-house"></i><span>Tổng quan</span></a>
    <a href="{{ route('student.my-courses') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-book-open"></i><span>Khóa học của tôi</span></a>
    <a href="{{ route('student.certificates') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-certificate"></i><span>Chứng chỉ</span></a>
    <a href="{{ route('student.payment.history') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-receipt"></i><span>Lịch sử thanh toán</span></a>
    <div class="dash-sidebar__divider"></div>
    <a href="/courses" class="dash-sidebar__nav-link"><i class="fa-solid fa-magnifying-glass"></i><span>Khám phá khóa học</span></a>
@endsection
@section('content')

@if($enrollments->isEmpty())
<div class="dash-card" style="text-align:center;padding:60px 32px;">
    <div style="font-size:48px;margin-bottom:16px;">📚</div>
    <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;">Bạn chưa đăng ký khóa học nào</h3>
    <p style="color:#64748b;margin-bottom:24px;">Hãy khám phá và đăng ký khóa học phù hợp với bạn.</p>
    <a href="{{ route('courses.index') }}" class="btn btn--secondary">Khám phá khóa học</a>
</div>
@else

<div class="dash-card dash-card--no-pad">
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Khóa học</th>
                    <th>Giảng viên</th>
                    <th>Tiến độ</th>
                    <th>Trạng thái</th>
                    <th>Ngày đăng ký</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $enrollment)
                @php
                    $course   = $enrollment->course;
                    $progress = $enrollment->progress_percent ?? 0;
                    $color    = $progress >= 100 ? '#10b981' : ($progress >= 50 ? 'rgb(40,40,254)' : '#f59e0b');
                    $status   = $progress >= 100 ? 'completed' : ($progress > 0 ? 'inprogress' : 'notstarted');
                @endphp
                <tr>
                    <td>
                        <div class="dash-course-cell">
                            <img src="{{ $course->thumbnail_url ?? 'https://picsum.photos/seed/'.$course->course_id.'/60/40' }}"
                                 alt="{{ $course->title }}" class="dash-course-cell__thumb">
                            <div>
                                <span class="dash-course-cell__title">{{ Str::limit($course->title, 36) }}</span>
                                <span class="dash-course-cell__meta">{{ ucfirst(str_replace('_', ' ', $course->level)) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="dash-table__muted">{{ $course->user->fullname ?? '—' }}</td>
                    <td style="min-width:140px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:6px;background:#e2e8f0;border-radius:100px;overflow:hidden;">
                                <div style="height:100%;width:{{ round($progress) }}%;background:{{ $color }};border-radius:100px;"></div>
                            </div>
                            <span style="font-size:12px;color:#64748b;width:32px;text-align:right;">{{ round($progress) }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="dash-badge dash-badge--{{ $status }}">
                            {{ match($status) {
                                'completed'  => 'Hoàn thành',
                                'inprogress' => 'Đang học',
                                default      => 'Chưa bắt đầu',
                            } }}
                        </span>
                    </td>
                    <td class="dash-table__muted">{{ $enrollment->enrolled_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="dash-action-group">
                            <a href="{{ route('courses.show', $course->course_id) }}" class="dash-action-btn" title="Xem khóa học">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <form action="{{ route('student.unenroll', $course->course_id) }}" method="POST"
                                  onsubmit="return confirm('Huỷ đăng ký khóa học này?');" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="dash-action-btn dash-action-btn--danger" title="Huỷ đăng ký">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="dash-pagination">
        <span class="dash-pagination__info">{{ $enrollments->total() }} khóa học</span>
        {{ $enrollments->links() }}
    </div>
</div>
@endif
@endsection