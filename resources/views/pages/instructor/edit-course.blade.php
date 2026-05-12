@extends('layouts.dashboard')

@section('title', 'Chỉnh sửa khóa học — ' . $course->title)
@section('page-title', 'Chỉnh sửa khóa học')
@section('sidebar-label', 'GIẢNG VIÊN')

@section('sidebar-nav')
    <a href="{{ route('instructor.dashboard') }}" class="dash-sidebar__nav-link">
        <i class="fa-solid fa-chart-line"></i>
        <span>Tổng quan</span>
    </a>

    <a href="{{ route('instructor.courses') }}" class="dash-sidebar__nav-link active">
        <i class="fa-solid fa-book-open"></i>
        <span>Khóa học</span>
    </a>
@endsection

@section('content')

<a href="{{ route('instructor.courses') }}"
   style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;margin-bottom:20px;">
    <i class="fa-solid fa-arrow-left"></i>
    Quay lại khóa học
</a>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

    {{-- LEFT --}}
    <div style="display:flex;flex-direction:column;gap:24px;">

        {{-- EDIT FORM --}}
        <div class="dash-card">

            <div class="dash-card__header">
                <h3 class="dash-card__title">Thông tin khóa học</h3>
            </div>

            @if(session('success'))
                <div style="background:#f0fdf4;border:1px solid #86efac;color:#16a34a;padding:12px 16px;border-radius:10px;margin-bottom:18px;">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fca5a5;color:#dc2626;padding:12px 16px;border-radius:10px;margin-bottom:18px;">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('instructor.courses.update', $course->course_id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  style="display:flex;flex-direction:column;gap:18px;">

                @csrf
                @method('PUT')

                {{-- TITLE --}}
                <div>
                    <label class="dash-label">Tên khóa học</label>

                    <input type="text"
                           name="title"
                           value="{{ old('title', $course->title) }}"
                           required
                           class="dash-input">
                </div>

                {{-- DESCRIPTION --}}
                <div>
                    <label class="dash-label">Mô tả</label>

                    <textarea name="description"
                              rows="6"
                              class="dash-textarea">{{ old('description', $course->description) }}</textarea>
                </div>

                {{-- LANGUAGE --}}
                <div>
                    <label class="dash-label">Ngôn ngữ</label>

                    <select name="language_id" class="dash-input">
                        @foreach($languages as $language)
                            <option value="{{ $language->language_id }}"
                                {{ $course->language_id == $language->language_id ? 'selected' : '' }}>
                                {{ $language->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PRICE --}}
                <div>
                    <label class="dash-label">Giá khóa học</label>

                    <input type="number"
                           name="price"
                           min="0"
                           step="0.01"
                           value="{{ old('price', $course->price) }}"
                           class="dash-input">
                </div>

                {{-- LEVEL --}}
                <div>
                    <label class="dash-label">Trình độ</label>

                    <select name="level" class="dash-input">
                        <option value="beginner"
                            {{ $course->level == 'beginner' ? 'selected' : '' }}>
                            Beginner
                        </option>

                        <option value="intermediate"
                            {{ $course->level == 'intermediate' ? 'selected' : '' }}>
                            Intermediate
                        </option>

                        <option value="advanced"
                            {{ $course->level == 'advanced' ? 'selected' : '' }}>
                            Advanced
                        </option>
                    </select>
                </div>

                {{-- THUMBNAIL --}}
                <div>
                    <label class="dash-label">Thumbnail</label>

                    <input type="file"
                           name="thumbnail"
                           accept="image/*"
                           class="dash-input">
                </div>

                {{-- ACTION --}}
                <div style="display:flex;gap:12px;">
                    <button type="submit"
                            style="padding:12px 24px;background:rgb(40,40,254);border:none;border-radius:10px;color:white;font-weight:600;cursor:pointer;">
                        Lưu thay đổi
                    </button>

                    <a href="{{ route('instructor.courses.show', $course->course_id) }}"
                       style="padding:12px 20px;background:#f1f5f9;border-radius:10px;text-decoration:none;color:#334155;font-weight:600;">
                        Hủy
                    </a>
                </div>

            </form>

        </div>

        {{-- SECTIONS --}}
        <div class="dash-card">

            <div class="dash-card__header"
                 style="display:flex;justify-content:space-between;align-items:center;">
                <h3 class="dash-card__title">Sections</h3>

                <a href="{{ route('instructor.sections.create', $course->course_id) }}"
                   style="padding:10px 16px;background:rgb(40,40,254);color:white;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600;">
                    + Thêm section
                </a>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px;">

                @forelse($sections as $section)

                    <div style="border:1px solid #e2e8f0;border-radius:12px;padding:16px;display:flex;justify-content:space-between;align-items:center;">

                        <div>
                            <div style="font-weight:600;color:#0f172a;">
                                {{ $section->title }}
                            </div>

                            <div style="font-size:13px;color:#64748b;margin-top:4px;">
                                {{ $section->lessons_count ?? 0 }} bài học
                            </div>
                        </div>

                        <a href="{{ route('instructor.sections.edit', $section->section_id) }}"
                           style="font-size:13px;text-decoration:none;color:rgb(40,40,254);font-weight:600;">
                            Chỉnh sửa
                        </a>

                    </div>

                @empty

                    <div style="padding:30px;border:1px dashed #cbd5e1;border-radius:12px;text-align:center;color:#64748b;">
                        Chưa có section nào 📚
                    </div>

                @endforelse

            </div>

        </div>

    </div>

    {{-- RIGHT --}}
    <div style="display:flex;flex-direction:column;gap:24px;">

        {{-- COURSE PREVIEW --}}
        <div class="dash-card">

            <div class="dash-card__header">
                <h3 class="dash-card__title">Preview</h3>
            </div>

            @if($course->thumbnail)
                <img src="{{ asset($course->thumbnail) }}"
                     alt="{{ $course->title }}"
                     style="width:100%;height:180px;object-fit:cover;border-radius:14px;margin-bottom:14px;">
            @endif

            <h4 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:10px;">
                {{ $course->title }}
            </h4>

            <p style="font-size:14px;color:#64748b;line-height:1.7;">
                {{ Str::limit($course->description, 140) }}
            </p>

            <div style="margin-top:16px;display:flex;flex-wrap:wrap;gap:8px;">

                <span style="padding:6px 12px;background:#eff6ff;color:#2563eb;border-radius:999px;font-size:12px;font-weight:600;">
                    {{ $course->language->name }}
                </span>

                <span style="padding:6px 12px;background:#f8fafc;color:#334155;border-radius:999px;font-size:12px;font-weight:600;">
                    {{ ucfirst($course->level) }}
                </span>

            </div>

        </div>

    </div>

</div>

@endsection