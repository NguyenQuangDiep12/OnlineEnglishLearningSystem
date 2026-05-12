@extends('layouts.dashboard')
@section('title', 'Khóa học của tôi')
@section('page-title', 'Khóa học của tôi')
@section('sidebar-label', 'GIẢNG VIÊN')

@section('sidebar-nav')
    <a href="{{ route('instructor.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-chart-line"></i><span>Tổng quan</span></a>
    <a href="{{ route('instructor.courses') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-book-open"></i><span>Khóa học của tôi</span></a>
    <div class="dash-sidebar__divider"></div>
    <a href="{{ route('instructor.create-course') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-plus"></i><span>Tạo khóa học mới</span></a>
    <a href="{{ route('home') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-globe"></i><span>Về trang chủ</span></a>
@endsection

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-size:14px;display:flex;gap:10px;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div style="font-size:14px;color:#64748b;">
        {{ $courses->total() }} khóa học
    </div>
    <a href="{{ route('instructor.create-course') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:rgb(40,40,254);color:white;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;">
        <i class="fa-solid fa-plus"></i> Tạo khóa học mới
    </a>
</div>

@if($courses->isEmpty())
<div class="dash-card" style="text-align:center;padding:60px 32px;">
    <div style="font-size:48px;margin-bottom:16px;">📚</div>
    <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;">Bạn chưa có khóa học nào</h3>
    <p style="color:#64748b;margin-bottom:24px;">Tạo khóa học đầu tiên của bạn ngay!</p>
    <a href="{{ route('instructor.create-course') }}" class="btn btn--secondary">Tạo khóa học</a>
</div>
@else

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;margin-bottom:24px;">
    @foreach($courses as $course)
    <div style="background:white;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);transition:all 0.2s;"
         onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'"
         onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)'">
        <div style="position:relative;">
            <img src="{{ $course->thumbnail_url ?? 'https://picsum.photos/seed/'.$course->course_id.'/400/200' }}"
                 alt="{{ $course->title }}"
                 style="width:100%;height:160px;object-fit:cover;display:block;">
            <span style="position:absolute;top:10px;left:10px;padding:4px 10px;border-radius:100px;font-size:11px;font-weight:700;
                background:{{ $course->is_published ? '#10b981' : '#94a3b8' }};color:white;">
                {{ $course->is_published ? 'Đang mở' : 'Ẩn' }}
            </span>
        </div>
        <div style="padding:18px;">
            <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:6px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                {{ $course->title }}
            </h3>
            <div style="display:flex;gap:16px;margin-bottom:14px;font-size:12px;color:#64748b;">
                <span><i class="fa-solid fa-users"></i> {{ $course->enrollments_count }} học viên</span>
                <span><i class="fa-solid fa-layer-group"></i> {{ $course->sections_count }} chương</span>
                <span><i class="fa-solid fa-star" style="color:#f59e0b;"></i> {{ number_format($course->course_reviews_avg_rating ?? 0, 1) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1px solid #f1f5f9;">
                <span style="font-size:15px;font-weight:700;color:{{ $course->price == 0 ? '#10b981' : '#0f172a' }};">
                    {{ $course->price == 0 ? 'Miễn phí' : number_format($course->price, 0, '.', '.').'đ' }}
                </span>
                <div style="display:flex;gap:6px;">
                    <a href="{{ route('instructor.courses.edit', $course->course_id) }}"
                       class="dash-action-btn" title="Chỉnh sửa">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    @if($course->is_published)
                    <form action="{{ route('instructor.courses.unpublish', $course->course_id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="dash-action-btn" title="Ẩn" style="color:#f59e0b;">
                            <i class="fa-solid fa-eye-slash"></i>
                        </button>
                    </form>
                    @else
                    <form action="{{ route('instructor.courses.publish', $course->course_id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="dash-action-btn" title="Xuất bản" style="color:#10b981;">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('instructor.courses.destroy', $course->course_id) }}" method="POST"
                          onsubmit="return confirm('Xóa khóa học này?');" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="dash-action-btn dash-action-btn--danger" title="Xóa">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="dash-pagination">
    <span class="dash-pagination__info">{{ $courses->total() }} khóa học</span>
    {{ $courses->links() }}
</div>
@endif

@endsection