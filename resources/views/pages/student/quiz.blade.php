@extends('layouts.dashboard')
@section('title', $quiz->title)
@section('page-title', 'Làm bài kiểm tra')
@section('sidebar-label', 'HỌC VIÊN')
@section('sidebar-nav')
    <a href="{{ route('student.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-house"></i><span>Tổng quan</span></a>
    <a href="{{ route('student.my-courses') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-book-open"></i><span>Khóa học của tôi</span></a>
    <a href="{{ route('student.certificates') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-certificate"></i><span>Chứng chỉ</span></a>
@endsection
@section('content')
<div style="max-width:700px;margin:0 auto;">

    <div class="dash-card" style="text-align:center;padding:40px 32px 32px;">
        <div style="width:72px;height:72px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;">
            🧠
        </div>
        <h1 style="font-size:24px;font-weight:800;color:#0f172a;margin-bottom:8px;">{{ $quiz->title }}</h1>
        @if($quiz->description)
        <p style="color:#64748b;font-size:15px;line-height:1.7;margin-bottom:24px;">{{ $quiz->description }}</p>
        @endif

        <div style="display:flex;justify-content:center;gap:32px;padding:20px 0;border-top:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;margin-bottom:24px;">
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:800;color:#0f172a;">{{ $quiz->quizQuestions->count() }}</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Câu hỏi</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:800;color:#0f172a;">{{ $quiz->pass_score }}%</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Điểm đạt</div>
            </div>
            @if($quiz->time_limit_sec)
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:800;color:#0f172a;">{{ gmdate('i:s', $quiz->time_limit_sec) }}</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Thời gian</div>
            </div>
            @endif
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:800;color:#0f172a;">
                    {{ $quiz->max_attempts > 0 ? $quiz->max_attempts : '∞' }}
                </div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Số lần làm</div>
            </div>
        </div>

        @if($bestScore > 0)
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:#16a34a;">
            <i class="fa-solid fa-trophy"></i> Điểm cao nhất của bạn: <strong>{{ $bestScore }}%</strong>
        </div>
        @endif

        @if($canStart)
        <form action="{{ route('student.quiz.start', $quiz->quiz_id) }}" method="POST">
            @csrf
            <button type="submit"
                style="padding:14px 40px;background:rgb(40,40,254);color:white;border:none;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;">
                <i class="fa-solid fa-play"></i> Bắt đầu làm bài
            </button>
        </form>
        @else
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:14px;font-size:14px;color:#dc2626;">
            <i class="fa-solid fa-ban"></i> Bạn đã hết số lần làm bài cho phép.
        </div>
        @endif
    </div>

    @if($history->count() > 0)
    <div class="dash-card" style="margin-top:20px;">
        <div class="dash-card__header">
            <h3 class="dash-card__title">Lịch sử làm bài</h3>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Lần</th>
                        <th>Điểm</th>
                        <th>Kết quả</th>
                        <th>Thời gian</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history->sortByDesc('attempt_number') as $attempt)
                    <tr>
                        <td>Lần {{ $attempt->attempt_number }}</td>
                        <td><strong>{{ round($attempt->score) }}%</strong></td>
                        <td>
                            <span class="dash-badge dash-badge--{{ $attempt->is_passed ? 'published' : 'failed' }}">
                                {{ $attempt->is_passed ? 'Đạt' : 'Chưa đạt' }}
                            </span>
                        </td>
                        <td class="dash-table__muted">{{ $attempt->submitted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>
                            @if($attempt->submitted_at)
                            <a href="{{ route('student.quiz.result', $attempt->quiz_attempt_id) }}" class="dash-action-btn" title="Xem kết quả">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection