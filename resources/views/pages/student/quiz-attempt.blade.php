@extends('layouts.dashboard')
@section('title', 'Làm bài: ' . $quiz->title)
@section('page-title', $quiz->title)
@section('sidebar-label', 'HỌC VIÊN')
@section('sidebar-nav')
    <a href="{{ route('student.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-house"></i><span>Tổng quan</span></a>
    <a href="{{ route('student.my-courses') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-book-open"></i><span>Khóa học của tôi</span></a>
@endsection
@section('content')
<div style="max-width:760px;margin:0 auto;">

    {{-- Timer --}}
    @if($timeLimit)
    <div class="dash-card" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding:14px 20px;">
        <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:#475569;">
            <i class="fa-regular fa-clock"></i>
            <span>Thời gian còn lại</span>
        </div>
        <span id="timer" style="font-size:24px;font-weight:800;color:#ef4444;font-family:monospace;">
            {{ gmdate('i:s', $timeLimit) }}
        </span>
    </div>
    @endif

    <form action="{{ route('student.quiz.submit', $attempt->quiz_attempt_id) }}" method="POST" id="quiz-form">
        @csrf

        @foreach($quiz->quizQuestions as $qIdx => $question)
        <div class="dash-card" style="margin-bottom:16px;">
            <div style="display:flex;gap:14px;align-items:flex-start;margin-bottom:18px;">
                <div style="min-width:36px;height:36px;background:rgb(40,40,254);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;">
                    {{ $qIdx + 1 }}
                </div>
                <div style="flex:1;">
                    <p style="font-size:15px;font-weight:600;color:#0f172a;line-height:1.6;margin-bottom:4px;">{{ $question->question }}</p>
                    <span style="font-size:12px;color:#94a3b8;">
                        {{ $question->points }} điểm
                        · {{ match($question->question_type) {
                            'single_choice'   => 'Chọn 1 đáp án',
                            'multiple_choice' => 'Chọn nhiều đáp án',
                            'true_false'      => 'Đúng / Sai',
                            'fill_blank'      => 'Điền vào chỗ trống',
                            default           => $question->question_type,
                        } }}
                    </span>
                </div>
            </div>

            {{-- Luôn gửi question_id --}}
            <input type="hidden" name="answers[{{ $qIdx }}][question_id]" value="{{ $question->quiz_question_id }}">

            @if($question->question_type === 'single_choice' || $question->question_type === 'true_false')
                {{-- Single choice: radio button --}}
                <div style="display:flex;flex-direction:column;gap:10px;padding-left:50px;">
                    @foreach($question->quizOptions->sortBy('sort_order') as $option)
                    <label style="display:flex;align-items:center;gap:12px;padding:12px 16px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:border-color 0.15s;">
                        <input type="radio"
                               name="answers[{{ $qIdx }}][selected_option_id]"
                               value="{{ $option->quiz_option_id }}"
                               style="accent-color:rgb(40,40,254);width:16px;height:16px;flex-shrink:0;"
                               onchange="highlightSelected(this)">
                        <span style="font-size:14px;color:#374151;">{{ $option->option_text }}</span>
                    </label>
                    @endforeach
                </div>

            @elseif($question->question_type === 'multiple_choice')
                {{-- FIX: multiple_choice dùng checkbox với name[] để gửi mảng --}}
                <p style="font-size:12px;color:#3b82f6;padding-left:50px;margin-bottom:10px;">
                    <i class="fa-solid fa-info-circle"></i> Có thể chọn nhiều đáp án
                </p>
                <div style="display:flex;flex-direction:column;gap:10px;padding-left:50px;">
                    @foreach($question->quizOptions->sortBy('sort_order') as $option)
                    <label style="display:flex;align-items:center;gap:12px;padding:12px 16px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:border-color 0.15s;"
                           onmouseover="this.style.borderColor='rgb(40,40,254)'"
                           onmouseout="if(!this.querySelector('input').checked)this.style.borderColor='#e2e8f0'">
                        <input type="checkbox"
                               name="answers[{{ $qIdx }}][selected_option_id][]"
                               value="{{ $option->quiz_option_id }}"
                               style="accent-color:rgb(40,40,254);width:16px;height:16px;flex-shrink:0;"
                               onchange="this.closest('label').style.borderColor = this.checked ? 'rgb(40,40,254)' : '#e2e8f0'">
                        <span style="font-size:14px;color:#374151;">{{ $option->option_text }}</span>
                    </label>
                    @endforeach
                </div>

            @else
                {{-- fill_blank --}}
                <div style="padding-left:50px;">
                    <input type="text"
                           name="answers[{{ $qIdx }}][answer_text]"
                           placeholder="Nhập câu trả lời..."
                           style="width:100%;padding:12px 16px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                           onfocus="this.style.borderColor='rgb(40,40,254)'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>
            @endif
        </div>
        @endforeach

        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
            <span style="font-size:13px;color:#94a3b8;">{{ $quiz->quizQuestions->count() }} câu hỏi</span>
            <button type="button"
                onclick="if(confirm('Bạn chắc chắn muốn nộp bài?')) document.getElementById('quiz-form').submit();"
                style="padding:14px 32px;background:rgb(40,40,254);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;">
                <i class="fa-solid fa-paper-plane"></i> Nộp bài
            </button>
        </div>
    </form>
</div>

<script>
function highlightSelected(radio) {
    // Reset tất cả labels cùng group
    document.querySelectorAll('input[name="' + radio.name + '"]').forEach(r => {
        r.closest('label').style.borderColor = '#e2e8f0';
    });
    radio.closest('label').style.borderColor = 'rgb(40,40,254)';
}
</script>

@if($timeLimit)
<script>
let secs = {{ $timeLimit }};
const timerEl = document.getElementById('timer');
const interval = setInterval(() => {
    secs--;
    if (secs <= 0) { clearInterval(interval); document.getElementById('quiz-form').submit(); return; }
    const m = Math.floor(secs/60).toString().padStart(2,'0');
    const s = (secs%60).toString().padStart(2,'0');
    timerEl.textContent = m + ':' + s;
    if (secs <= 60) timerEl.style.color = '#ef4444';
}, 1000);
</script>
@endif
@endsection
