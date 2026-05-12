@extends('layouts.dashboard')

@section('title', $lesson->title)
@section('page-title', 'Học bài')
@section('sidebar-label', 'HỌC VIÊN')

@section('sidebar-nav')
    <a href="{{ route('student.dashboard') }}" class="dash-sidebar__nav-link">
        <i class="fa-solid fa-house"></i><span>Tổng quan</span>
    </a>
    <a href="{{ route('student.my-courses') }}" class="dash-sidebar__nav-link active">
        <i class="fa-solid fa-book-open"></i><span>Khóa học của tôi</span>
    </a>
    <a href="{{ route('student.certificates') }}" class="dash-sidebar__nav-link">
        <i class="fa-solid fa-certificate"></i><span>Chứng chỉ</span>
    </a>
    <div class="dash-sidebar__divider"></div>
    <a href="/courses" class="dash-sidebar__nav-link">
        <i class="fa-solid fa-magnifying-glass"></i><span>Khám phá</span>
    </a>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start;">

    {{-- Main lesson area --}}
    <div>
        {{-- Video --}}
        <div class="dash-card dash-card--no-pad" style="overflow: hidden; margin-bottom: 0; border-radius: 16px;">
            @if($lesson->video_url)
                <div style="position: relative; padding-top: 56.25%; background: #000;">
                    <iframe src="{{ $lesson->video_url }}"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"
                            allowfullscreen></iframe>
                </div>
            @else
                <div style="height: 360px; background: #0f172a; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 16px; color: #94a3b8;">
                    <i class="fa-regular fa-file-lines" style="font-size: 48px;"></i>
                    <span>Bài học dạng văn bản</span>
                </div>
            @endif
        </div>

        {{-- Lesson info --}}
        <div class="dash-card" style="margin-top: 0; border-top-left-radius: 0; border-top-right-radius: 0; border-top: none;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 16px;">
                <div>
                    <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">{{ $lesson->title }}</h1>
                    @if($lesson->duration_minutes)
                        <span style="font-size: 13px; color: #94a3b8;">
                            <i class="fa-regular fa-clock"></i> {{ $lesson->duration_minutes }} phút
                        </span>
                    @endif
                </div>

                <form action="{{ route('student.lesson.complete', [$courseId, $lesson->lesson_id]) }}" method="POST">
                    @csrf
                    @if($progress && $progress->is_completed)
                        <button type="button" style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: #f0fdf4; color: #16a34a; border: 1.5px solid #86efac; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: default;">
                            <i class="fa-solid fa-circle-check"></i> Đã hoàn thành
                        </button>
                    @else
                        <button type="submit" style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: rgb(40,40,254); color: white; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;">
                            <i class="fa-solid fa-check"></i> Đánh dấu hoàn thành
                        </button>
                    @endif
                </form>
            </div>

            {{-- Progress bar --}}
            @php $pct = $progress ? $progress->completed_percent : 0; @endphp
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; color: #94a3b8; margin-bottom: 6px;">
                    <span>Tiến độ bài học</span>
                    <span id="pct-label">{{ round($pct) }}%</span>
                </div>
                <div style="height: 6px; background: #e2e8f0; border-radius: 100px; overflow: hidden;">
                    <div id="pct-bar" style="height: 100%; width: {{ round($pct) }}%; background: rgb(40,40,254); border-radius: 100px; transition: width 0.3s;"></div>
                </div>
            </div>

            {{-- Content --}}
            @if($lesson->content)
            <div style="font-size: 15px; line-height: 1.8; color: #374151; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                {!! $lesson->content !!}
            </div>
            @endif
        </div>

        {{-- Alert: cert issued --}}
        @if(session('cert_issued'))
        <div style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white; border-radius: 12px; padding: 20px 24px; margin-top: 16px; display: flex; align-items: center; gap: 16px;">
            <i class="fa-solid fa-trophy" style="font-size: 32px;"></i>
            <div>
                <strong style="font-size: 16px;">Chúc mừng! Bạn đã hoàn thành khóa học!</strong>
                <p style="font-size: 13px; margin-top: 4px; opacity: 0.9;">Mã chứng chỉ: <code style="background: rgba(255,255,255,0.25); padding: 2px 8px; border-radius: 6px;">{{ session('cert_issued') }}</code></p>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar: lesson list --}}
    <div>
        <div class="dash-card dash-card--no-pad" style="position: sticky; top: 80px;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                <h3 style="font-size: 14px; font-weight: 700; color: #0f172a;">Nội dung khóa học</h3>
                <div style="margin-top: 8px; height: 5px; background: #e2e8f0; border-radius: 100px; overflow: hidden;">
                    <div style="height: 100%; width: {{ round($courseProgress) }}%; background: rgb(40,40,254); border-radius: 100px;"></div>
                </div>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 4px; display: block;">{{ round($courseProgress) }}% hoàn thành</span>
            </div>

            <div style="max-height: 60vh; overflow-y: auto;">
                @foreach($lesson->section->course->sections ?? [] as $section)
                <div>
                    <div style="padding: 10px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                        {{ $section->title }}
                    </div>
                    @foreach($section->lessons as $sLesson)
                    <a href="{{ route('student.lesson.show', [$courseId, $sLesson->lesson_id]) }}"
                       style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-bottom: 1px solid #f8fafc; text-decoration: none; color: {{ $sLesson->lesson_id == $lesson->lesson_id ? 'rgb(40,40,254)' : '#374151' }}; background: {{ $sLesson->lesson_id == $lesson->lesson_id ? '#eff6ff' : 'white' }};">
                        @if(in_array($sLesson->lesson_id, $completedIds->toArray()))
                            <i class="fa-solid fa-circle-check" style="color: #10b981; flex-shrink: 0; font-size: 14px;"></i>
                        @elseif($sLesson->lesson_id == $lesson->lesson_id)
                            <i class="fa-solid fa-play-circle" style="color: rgb(40,40,254); flex-shrink: 0; font-size: 14px;"></i>
                        @else
                            <i class="fa-regular fa-circle" style="color: #cbd5e1; flex-shrink: 0; font-size: 14px;"></i>
                        @endif
                        <span style="font-size: 13px; flex: 1; line-height: 1.4;">{{ $sLesson->title }}</span>
                        @if($sLesson->duration_minutes)
                            <span style="font-size: 11px; color: #94a3b8; flex-shrink: 0;">{{ $sLesson->duration_minutes }}'</span>
                        @endif
                    </a>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- AJAX progress update --}}
@if($lesson->video_url)
<script>
(function() {
    let lastSent = 0;
    function sendProgress(pct) {
        if (Math.abs(pct - lastSent) < 5) return;
        lastSent = pct;
        fetch('{{ route('student.lesson.progress', $lesson->lesson_id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ percent: pct })
        }).then(r => r.json()).then(data => {
            document.getElementById('pct-bar').style.width = data.percent + '%';
            document.getElementById('pct-label').textContent = Math.round(data.percent) + '%';
        });
    }

    const iframe = document.querySelector('iframe');
    if (iframe) {
        let fakeProgress = {{ round($pct) }};
        const timer = setInterval(() => {
            if (fakeProgress < 100) {
                fakeProgress = Math.min(100, fakeProgress + 0.5);
                sendProgress(fakeProgress);
            } else {
                clearInterval(timer);
            }
        }, 3000);
    }
})();
</script>
@endif
@endsection