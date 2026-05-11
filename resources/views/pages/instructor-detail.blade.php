@extends('layouts.app')

@section('title', ($instructor->fullname ?? 'Giảng viên') . ' — E-Learn')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%);padding:70px 80px;color:white;">
    <div style="max-width:1200px;margin:0 auto;display:flex;gap:60px;align-items:center;flex-wrap:wrap;">

        {{-- Avatar --}}
        <div style="flex-shrink:0;">
            @if($instructor->avatar_url)
                <img src="{{ $instructor->avatar_url }}"
                     alt="{{ $instructor->fullname }}"
                     style="width:160px;height:160px;border-radius:50%;object-fit:cover;border:5px solid rgba(255,255,255,0.15);box-shadow:0 20px 40px rgba(0,0,0,0.35);">
            @else
                <div style="width:160px;height:160px;border-radius:50%;background:white;color:rgb(40,40,254);display:flex;align-items:center;justify-content:center;font-size:64px;font-weight:800;box-shadow:0 20px 40px rgba(0,0,0,0.35);">
                    {{ mb_substr($instructor->fullname,0,1) }}
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div style="flex:1;min-width:300px;">

            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px;">
                <span style="background:rgba(255,255,255,0.12);padding:6px 14px;border-radius:100px;font-size:12px;font-weight:700;color:#93c5fd;text-transform:uppercase;letter-spacing:1px;">
                    Giảng viên
                </span>

                <span style="background:#10b981;padding:6px 14px;border-radius:100px;font-size:12px;font-weight:700;color:white;">
                    ✓ Đang hoạt động
                </span>
            </div>

            <h1 style="font-size:42px;font-weight:900;line-height:1.2;margin-bottom:14px;">
                {{ $instructor->fullname }}
            </h1>

            <p style="font-size:16px;line-height:1.8;color:#cbd5e1;max-width:760px;margin-bottom:28px;">
                {{ $instructor->bio ?? 'Giảng viên giàu kinh nghiệm trong lĩnh vực đào tạo tiếng Anh trực tuyến, đồng hành cùng học viên từ nền tảng cơ bản đến nâng cao.' }}
            </p>

            {{-- Stats --}}
            <div style="display:flex;gap:28px;flex-wrap:wrap;">

                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div>
                        <div style="font-size:18px;font-weight:800;">
                            {{ $instructor->courses->count() }}
                        </div>
                        <div style="font-size:13px;color:#94a3b8;">
                            Khóa học
                        </div>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <div style="font-size:18px;font-weight:800;">
                            {{ $instructor->courses->sum(fn($c) => $c->enrollments->count()) }}
                        </div>
                        <div style="font-size:13px;color:#94a3b8;">
                            Học viên
                        </div>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-star" style="color:#f59e0b;"></i>
                    </div>
                    <div>
                        <div style="font-size:18px;font-weight:800;">
                            4.8
                        </div>
                        <div style="font-size:13px;color:#94a3b8;">
                            Đánh giá
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

{{-- Main --}}
<section style="padding:70px 80px;background:#f8fafc;">
    <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:2fr 1fr;gap:40px;align-items:start;">

        {{-- Left --}}
        <div>

            {{-- About --}}
            <div style="background:white;border-radius:18px;padding:32px;border:1px solid #e2e8f0;margin-bottom:30px;">
                <h2 style="font-size:24px;font-weight:800;margin-bottom:20px;color:#0f172a;">
                    Giới thiệu
                </h2>

                <p style="font-size:15px;line-height:1.9;color:#475569;">
                    {{ $instructor->bio ?? 'Giảng viên chuyên đào tạo tiếng Anh giao tiếp, IELTS và Business English với phương pháp học thực tế, hiện đại và tập trung vào khả năng ứng dụng.' }}
                </p>
            </div>

            {{-- Courses --}}
            <div style="background:white;border-radius:18px;padding:32px;border:1px solid #e2e8f0;">

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                    <h2 style="font-size:24px;font-weight:800;color:#0f172a;">
                        Khóa học nổi bật
                    </h2>

                    <span style="font-size:14px;color:#64748b;">
                        {{ $instructor->courses->count() }} khóa học
                    </span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;">

                    @foreach($instructor->courses as $course)
                    <div style="border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;background:white;transition:0.25s;box-shadow:0 4px 14px rgba(0,0,0,0.04);">

                        <img src="{{ $course->thumbnail_url ?? 'https://picsum.photos/seed/'.$course->course_id.'/500/280' }}"
                             alt="{{ $course->title }}"
                             style="width:100%;height:180px;object-fit:cover;display:block;">

                        <div style="padding:20px;">

                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                                <span style="font-size:11px;font-weight:700;padding:5px 12px;border-radius:100px;background:#eff6ff;color:rgb(40,40,254);text-transform:uppercase;">
                                    {{ ucfirst(str_replace('_',' ',$course->level)) }}
                                </span>

                                <span style="font-size:13px;color:#f59e0b;font-weight:700;">
                                    ★ {{ number_format($course->courseReviews->avg('rating') ?? 0,1) }}
                                </span>
                            </div>

                            <h3 style="font-size:17px;font-weight:800;line-height:1.5;color:#0f172a;margin-bottom:10px;">
                                {{ Str::limit($course->title,60) }}
                            </h3>

                            <p style="font-size:14px;color:#64748b;line-height:1.7;margin-bottom:18px;">
                                {{ Str::limit($course->description,90) }}
                            </p>

                            <div style="display:flex;justify-content:space-between;align-items:center;">

                                <div style="font-size:14px;font-weight:800;color:#0f172a;">
                                    @if($course->price == 0)
                                        <span style="color:#10b981;">Miễn phí</span>
                                    @else
                                        {{ number_format($course->price,0,'.','.') }}đ
                                    @endif
                                </div>

                                <a href="{{ route('courses.show',$course->course_id) }}"
                                   style="padding:10px 16px;background:rgb(40,40,254);color:white;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;">
                                    Xem khóa học
                                </a>

                            </div>

                        </div>

                    </div>
                    @endforeach

                </div>

            </div>

        </div>

        {{-- Right --}}
        <div style="position:sticky;top:100px;">

            <div style="background:white;border-radius:18px;padding:28px;border:1px solid #e2e8f0;box-shadow:0 10px 30px rgba(0,0,0,0.06);">

                <h3 style="font-size:20px;font-weight:800;margin-bottom:22px;color:#0f172a;">
                    Thông tin nhanh
                </h3>

                <div style="display:flex;flex-direction:column;gap:16px;">

                    <div style="display:flex;justify-content:space-between;font-size:14px;">
                        <span style="color:#64748b;">Vai trò</span>
                        <strong>Instructor</strong>
                    </div>

                    <div style="display:flex;justify-content:space-between;font-size:14px;">
                        <span style="color:#64748b;">Khóa học</span>
                        <strong>{{ $instructor->courses->count() }}</strong>
                    </div>

                    <div style="display:flex;justify-content:space-between;font-size:14px;">
                        <span style="color:#64748b;">Học viên</span>
                        <strong>{{ $instructor->courses->sum(fn($c) => $c->enrollments->count()) }}</strong>
                    </div>

                    <div style="display:flex;justify-content:space-between;font-size:14px;">
                        <span style="color:#64748b;">Đánh giá</span>
                        <strong style="color:#f59e0b;">★ 4.8</strong>
                    </div>

                </div>

                <a href="/courses"
                   style="display:block;width:100%;margin-top:24px;padding:14px;background:rgb(40,40,254);color:white;text-align:center;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;">
                    Khám phá khóa học
                </a>

            </div>

        </div>

    </div>
</section>

@endsection