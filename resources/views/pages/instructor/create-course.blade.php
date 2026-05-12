@extends('layouts.dashboard')
@section('title', 'Tạo khóa học mới')
@section('page-title', 'Tạo khóa học mới')
@section('sidebar-label', 'GIẢNG VIÊN')

@section('sidebar-nav')
    <a href="{{ route('instructor.dashboard') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-chart-line"></i><span>Tổng quan</span></a>
    <a href="{{ route('instructor.courses') }}" class="dash-sidebar__nav-link"><i class="fa-solid fa-book-open"></i><span>Khóa học của tôi</span></a>
    <div class="dash-sidebar__divider"></div>
    <a href="{{ route('instructor.create-course') }}" class="dash-sidebar__nav-link active"><i class="fa-solid fa-plus"></i><span>Tạo khóa học mới</span></a>
@endsection

@section('content')

<div style="max-width:720px;">

    <a href="{{ route('instructor.courses') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;margin-bottom:20px;">
        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
    </a>

    <div class="dash-card">
        <div class="dash-card__header">
            <h3 class="dash-card__title">Thông tin khóa học</h3>
        </div>

        @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:#dc2626;">
            <strong>Có lỗi xảy ra:</strong>
            <ul style="margin:8px 0 0 16px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('instructor.courses.store') }}" method="POST" enctype="multipart/form-data"
              style="display:flex;flex-direction:column;gap:20px;">
            @csrf

            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">
                    Tiêu đề khóa học <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                       placeholder="VD: TOEIC 750+ Complete Course"
                       style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                       onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'" required>
            </div>

            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Mô tả</label>
                <textarea name="description" rows="4"
                          placeholder="Mô tả chi tiết về nội dung và mục tiêu của khóa học..."
                          style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;resize:vertical;font-family:inherit;"
                          onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">{{ old('description') }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">
                        Cấp độ <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="level" required
                            style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;appearance:none;background:white url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\") no-repeat right 12px center;"
                            onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
                        <option value="">Chọn cấp độ</option>
                        @foreach(['beginner'=>'Beginner (Cơ bản)','elementary'=>'Elementary','intermediate'=>'Intermediate','upper_intermediate'=>'Upper Intermediate','advanced'=>'Advanced'] as $val => $label)
                        <option value="{{ $val }}" {{ old('level') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">
                        Ngôn ngữ <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="language_id" required
                            style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;appearance:none;background:white url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\") no-repeat right 12px center;"
                            onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'">
                        <option value="">Chọn ngôn ngữ</option>
                        @foreach($languages as $lang)
                        <option value="{{ $lang->language_id }}" {{ old('language_id') == $lang->language_id ? 'selected' : '' }}>
                            {{ $lang->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">
                    Giá (VNĐ) <span style="color:#ef4444;">*</span>
                </label>
                <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="1000"
                       style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                       onfocus="this.style.borderColor='rgb(40,40,254)'" onblur="this.style.borderColor='#e2e8f0'" required>
                <p style="font-size:12px;color:#94a3b8;margin-top:4px;">Nhập 0 để miễn phí</p>
            </div>

            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Thumbnail</label>
                <input type="file" name="thumbnail" accept="image/jpg,image/jpeg,image/png,image/webp"
                       style="width:100%;padding:11px 14px;border:1.5px dashed #e2e8f0;border-radius:10px;font-size:14px;cursor:pointer;"
                       onchange="previewThumb(this)">
                <img id="thumb-preview" style="display:none;margin-top:10px;max-height:160px;border-radius:8px;object-fit:cover;">
                <p style="font-size:12px;color:#94a3b8;margin-top:4px;">JPG, PNG, WebP. Tối đa 3MB.</p>
            </div>

            <div style="display:flex;gap:12px;padding-top:8px;border-top:1px solid #f1f5f9;">
                <button type="submit"
                        style="padding:12px 32px;background:rgb(40,40,254);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
                    <i class="fa-solid fa-plus"></i> Tạo khóa học
                </button>
                <a href="{{ route('instructor.courses') }}"
                   style="padding:12px 24px;background:#f1f5f9;color:#374151;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function previewThumb(input) {
    const preview = document.getElementById('thumb-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection