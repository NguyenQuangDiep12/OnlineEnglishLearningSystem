@extends('layouts.dashboard')
@section('title', 'Kết quả bài kiểm tra')
@section('page-title', 'Kết quả')
@section('sidebar-label', 'HỌC VIÊN')
@section('sidebar-nav')
    <a href="{{ route('student.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-house"></i><span>Tổng quan</span></a>
    <a href="{{ route('student.my-courses') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-book-open"></i><span>Khóa học của tôi</span></a>
@endsection
@section('content')
<div style="max-width:760px;margin:0 auto;">

    <div class="dash-card" style="text-align:center;padding:40px 32px 32px;margin-bottom:20px;">
        @php $score = round($attempt->score); @endphp

        <div style="width:120px;height:120px;border-radius:50%;border:6px solid {{ $attempt->is_passed ? '#10b981' : '#ef4444' }};display:flex;flex-direction:column;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span style="font-size:36px;font-weight:900;color:{{ $attempt->is_passed ? '#10b981' : '#ef4444' }};">{{ $score }}%</span>
        </div>

        <h2 style="font-size:22px;font-weight:800;margin-bottom:8px;color:{{ $attempt->is_passed ? '#16a34a' : '#dc2626' }};">
            {{ $attempt->is_passed ? '🎉 Chúc mừng! Bạn đã đạt!' : '😢 Chưa đạt. Cố gắng hơn nhé!' }}
        </h2>
        <p style="font-size:14px;color:#64748b;margin-bottom:24px;">
            Điểm đạt yêu cầu: {{ $attempt->quiz->pass_score }}%
            &nbsp;·&nbsp; Điểm cao nhất của bạn: <strong>{{ $bestScore }}%</strong>
        </p>

        <div style="display:flex;justify-content:center;gap:32px;padding:20px 0;border-top:1px solid #f1f5f9;margin-bottom:20px;">
            <div>
                <div style="font-size:22px;font-weight:800;color:#10b981;">{{ $correct }}</div>
                <div style="font-size:12px;color:#94a3b8;">Đúng</div>
            </div>
            <div>
                <div style="font-size:22px;font-weight:800;color:#ef4444;">{{ $total - $correct }}</div>
                <div style="font-size:12px;color:#94a3b8;">Sai</div>
            </div>
            <div>
                <div style="font-size:22px;font-weight:800;color:#0f172a;">{{ $total }}</div>
                <div style="font-size:12px;color:#94a3b8;">Tổng câu</div>
            </div>
            <div>
                <div style="font-size:22px;font-weight:800;color:#0f172a;">{{ $attempt->attempt_number }}</div>
                <div style="font-size:12px;color:#94a3b8;">Lần thứ</div>
            </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:center;">
            <a href="{{ route('student.quiz.show', $attempt->quiz_id) }}" class="btn btn--primary">
                <i class="fa-solid fa-rotate-left"></i> Làm lại
            </a>
            <a href="{{ route('student.my-courses') }}" class="btn btn--secondary">
                <i class="fa-solid fa-book-open"></i> Về khóa học
            </a>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-card__header">
            <h3 class="dash-card__title">Chi tiết đáp án</h3>
        </div>

        @foreach($answers as $idx => $answer)
        <div style="padding:16px 0;border-bottom:1px solid #f8fafc;">
            <div style="display:flex;gap:12px;align-items:flex-start;">
                <div style="min-width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;
                    background:{{ $answer->is_correct ? '#f0fdf4' : '#fef2f2' }};
                    color:{{ $answer->is_correct ? '#16a34a' : '#dc2626' }};">
                    {{ $answer->is_correct ? '✓' : '✗' }}
                </div>
                <div style="flex:1;">
                    <p style="font-size:14px;font-weight:600;color:#0f172a;margin-bottom:8px;">
                        Câu {{ $idx + 1 }}: {{ $answer->question->question ?? '' }}
                    </p>
                    @if($answer->selectedOption)
                    <p style="font-size:13px;color:#475569;margin-bottom:4px;">
                        <span style="color:#94a3b8;">Bạn chọn:</span>
                        <span style="color:{{ $answer->is_correct ? '#16a34a' : '#dc2626' }};font-weight:600;">
                            {{ $answer->selectedOption->option_text }}
                        </span>
                    </p>
                    @elseif($answer->answer_text)
                    <p style="font-size:13px;color:#475569;margin-bottom:4px;">
                        <span style="color:#94a3b8;">Câu trả lời:</span>
                        <span style="color:{{ $answer->is_correct ? '#16a34a' : '#dc2626' }};font-weight:600;">
                            @php
                                // Hiển thị đẹp cho multiple choice (JSON array)
                                $answerText = $answer->answer_text;
                                $decoded = json_decode($answerText, true);
                                echo is_array($decoded) ? 'Đã chọn ' . count($decoded) . ' đáp án' : e($answerText);
                            @endphp
                        </span>
                    </p>
                    @else
                    <p style="font-size:13px;color:#94a3b8;margin-bottom:4px;">Không trả lời</p>
                    @endif
                    <span style="font-size:12px;color:#94a3b8;">
                        {{ $answer->points_earned }}/{{ $answer->question->points ?? 1 }} điểm
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
