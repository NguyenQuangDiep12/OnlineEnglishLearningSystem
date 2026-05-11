@extends('layouts.app')

@section('title', 'Giảng viên')

@section('content')

<section class="page-header">
    <div class="page-header__inner">
        <span class="page-header__tag">Đội ngũ chuyên gia</span>
        <h1>Gặp gỡ các <span>giảng viên</span></h1>
        <p>Những nhà giáo dục tận tâm với nhiều năm kinh nghiệm, sẵn sàng đồng hành cùng bạn.</p>
    </div>
</section>

<section class="instructors-section">
    <div class="container">

        {{-- Stats --}}
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-item__number">20+</div>
                <div class="stat-item__label">Giảng viên</div>
            </div>
            <div class="stat-item">
                <div class="stat-item__number">15,000+</div>
                <div class="stat-item__label">Học viên</div>
            </div>
            <div class="stat-item">
                <div class="stat-item__number">4.8</div>
                <div class="stat-item__label">Điểm đánh giá TB</div>
            </div>
            <div class="stat-item">
                <div class="stat-item__number">1,000+</div>
                <div class="stat-item__label">Khóa học</div>
            </div>
        </div>

        {{-- Instructor Grid --}}
        <div class="instructor-grid">
            @php
                $instructors = [
                    ['id'=>1,'name'=>'Nguyễn Văn Long','title'=>'Chuyên gia TOEIC & IELTS','bio'=>'Hơn 10 năm kinh nghiệm giảng dạy tiếng Anh, chứng chỉ CELTA, DELTA từ Cambridge.','courses'=>48,'students'=>3200,'rating'=>4.9,'avatar'=>'https://i.pravatar.cc/150?img=1','badges'=>['TOEIC','IELTS','Business English']],
                    ['id'=>2,'name'=>'Trần Thị Hoài Mai','title'=>'Giảng viên tiếng Anh giao tiếp','bio'=>'Tốt nghiệp đại học nước ngoài, chuyên đào tạo kỹ năng giao tiếp và phát âm chuẩn.','courses'=>32,'students'=>2100,'rating'=>4.8,'avatar'=>'https://i.pravatar.cc/150?img=5','badges'=>['Speaking','Pronunciation']],
                    ['id'=>3,'name'=>'David Smith','title'=>'Native Speaker — Business English','bio'=>'Giảng viên người Mỹ, thạc sĩ TESOL, chuyên tiếng Anh thương mại và viết học thuật.','courses'=>25,'students'=>1800,'rating'=>4.9,'avatar'=>'https://i.pravatar.cc/150?img=3','badges'=>['Business','Academic Writing']],
                    ['id'=>4,'name'=>'Lê Thị Phương Hoa','title'=>'Chuyên gia IELTS 8.5','bio'=>'Đạt IELTS 8.5, từng giảng dạy tại British Council. Chuyên ôn luyện Writing & Reading.','courses'=>20,'students'=>1500,'rating'=>4.7,'avatar'=>'https://i.pravatar.cc/150?img=9','badges'=>['IELTS Writing','IELTS Reading']],
                    ['id'=>5,'name'=>'Phạm Văn Hùng','title'=>'Giảng viên Grammar & Vocabulary','bio'=>'Thạc sĩ Ngôn ngữ học ứng dụng. Phương pháp giảng dạy ngữ pháp dễ hiểu, dễ nhớ.','courses'=>38,'students'=>2800,'rating'=>4.8,'avatar'=>'https://i.pravatar.cc/150?img=7','badges'=>['Grammar','Vocabulary']],
                    ['id'=>6,'name'=>'Sophie Dupont','title'=>'Tiếng Pháp & Đa ngôn ngữ','bio'=>'Giảng viên người Pháp, thông thạo 4 ngôn ngữ. Phương pháp immersion học cực kỳ hiệu quả.','courses'=>15,'students'=>890,'rating'=>4.6,'avatar'=>'https://i.pravatar.cc/150?img=10','badges'=>['French','Multilingual']],
                ];
            @endphp

            @foreach($instructors as $ins)
            <div class="instructor-card">
                <div class="instructor-card__top">
                    <img src="{{ $ins['avatar'] }}" alt="{{ $ins['name'] }}" class="instructor-card__avatar">
                    <div class="instructor-card__rating">★ {{ $ins['rating'] }}</div>
                </div>
                <div class="instructor-card__body">
                    <h3>{{ $ins['name'] }}</h3>
                    <p class="instructor-card__title">{{ $ins['title'] }}</p>
                    <p class="instructor-card__bio">{{ $ins['bio'] }}</p>
                    <div class="instructor-card__badges">
                        @foreach($ins['badges'] as $badge)
                            <span class="badge">{{ $badge }}</span>
                        @endforeach
                    </div>
                    <div class="instructor-card__stats">
                        <div>
                            <strong>{{ $ins['courses'] }}</strong>
                            <small>Khóa học</small>
                        </div>
                        <div class="instructor-card__divider"></div>
                        <div>
                            <strong>{{ number_format($ins['students']) }}</strong>
                            <small>Học viên</small>
                        </div>
                    </div>
                    <a href="/instructors/{{ $ins['id'] }}" class="btn btn--primary btn--block">Xem hồ sơ</a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>

@endsection