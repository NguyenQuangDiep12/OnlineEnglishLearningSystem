-- ============================================================
-- SEED DATA - Online English Learning System
-- PostgreSQL | ~1000 dòng mỗi bảng
-- ============================================================

-- Bật extension nếu cần
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ============================================================
-- 1. LANGUAGE (~10 ngôn ngữ)
-- ============================================================
INSERT INTO languages (language_id, name, code)
SELECT
    gs,
    (ARRAY['English','French','Spanish','German','Japanese','Korean','Chinese','Italian','Portuguese','Russian'])[gs],
    (ARRAY['en','fr','es','de','ja','ko','zh','it','pt','ru'])[gs]
FROM generate_series(1, 10) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 2. USER (~1000 người dùng)
-- ============================================================
INSERT INTO "users" (user_id, fullname, email, password_hash, avatar_url, role, created_at, updated_at)
SELECT
    gs,
    'User ' || gs,
    'user' || gs || '@example.com',
    encode(digest('password' || gs, 'sha256'), 'hex'),
    'https://i.pravatar.cc/150?img=' || (gs % 70 + 1),

    CASE
        WHEN gs <= 20 THEN 'instructor'
        WHEN gs <= 30 THEN 'admin'
        ELSE 'student'
    END,

    NOW() - (random() * INTERVAL '2 years'),
    NOW() - (random() * INTERVAL '30 days')
FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 3. COURSE (~1000 khóa học)
-- ============================================================
INSERT INTO courses (course_id, language_id, teacher_id, title, description, level, price, thumbnail_url, is_published, created_at)
SELECT
    gs,
    (gs % 10) + 1,
    (gs % 20) + 1,                          -- teacher_id từ 1-20
    (ARRAY['Beginner','Elementary','Pre-Intermediate','Intermediate','Upper-Intermediate','Advanced'])[((gs-1) % 6) + 1]
        || ' Course #' || gs,
    'Mô tả chi tiết khóa học số ' || gs || '. Giúp học viên nâng cao kỹ năng ngôn ngữ một cách hiệu quả.',
    (ARRAY['beginner','elementary','intermediate','upper_intermediate','advanced'])[((gs-1) % 5) + 1],
    ROUND((CAST(random() * 500000 + 50000 AS numeric)), -3),
    'https://picsum.photos/seed/' || gs || '/400/225',
    (random() > 0.2),
    NOW() - (random() * INTERVAL '2 years')
FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 4. SECTION (~1000 phần học, ~1 section/course cho 1000 courses)
-- ============================================================
INSERT INTO sections (section_id, course_id, title, sort_order)
SELECT
    gs,
    gs,                                      -- mỗi course 1 section chính
    'Chương ' || ((gs-1) % 10 + 1) || ': ' ||
    (ARRAY['Giới thiệu','Ngữ pháp cơ bản','Từ vựng','Kỹ năng nghe','Kỹ năng nói','Kỹ năng đọc','Kỹ năng viết','Luyện tập','Kiểm tra','Tổng kết'])[((gs-1) % 10) + 1],
    (gs-1) % 10 + 1
FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 5. LESSON (~1000 bài học)
-- ============================================================
INSERT INTO lessons (lesson_id, section_id, title, content, video_url, duration_minutes, sort_order, created_at)
SELECT
    gs,
    (gs % 1000) + 1,
    'Bài ' || gs || ': ' ||
    (ARRAY['Phát âm cơ bản','Câu chào hỏi','Số đếm','Màu sắc','Gia đình','Thực phẩm','Du lịch','Mua sắm','Công việc','Sức khỏe'])[((gs-1) % 10) + 1],
    '<p>Nội dung bài học ' || gs || '. Học viên sẽ được học các kiến thức quan trọng và thực hành thông qua các bài tập đa dạng.</p>',
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ&lesson=' || gs,
    (random() * 45 + 5)::int,
    (gs-1) % 20 + 1,
    NOW() - (random() * INTERVAL '2 years')
FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 6. QUIZ (~1000 bài quiz)
-- ============================================================
INSERT INTO quizzes (quiz_id, lesson_id, title, description, pass_score, time_limit_sec, max_attempts)
SELECT
    gs,
    (gs % 1000) + 1,
    'Quiz ' || gs || ': ' ||
    (ARRAY['Kiểm tra từ vựng','Bài thi ngữ pháp','Luyện nghe','Bài kiểm tra cuối chương','Ôn tập tổng hợp'])[((gs-1) % 5) + 1],
    'Bài kiểm tra đánh giá kiến thức phần ' || gs,
    (ARRAY[50,60,70,75,80])[((gs-1) % 5) + 1],
    (ARRAY[600,900,1200,1800,3600])[((gs-1) % 5) + 1],
    (ARRAY[1,2,3,5,999])[((gs-1) % 5) + 1]
FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 7. QUIZ_QUESTION (~1000 câu hỏi)
-- ============================================================
INSERT INTO quiz_questions (quiz_question_id, quiz_id, question, question_type, points)
SELECT
    gs,
    (gs % 1000) + 1,
    'Câu hỏi ' || gs || ': ' ||
    (ARRAY[
        'Chọn nghĩa đúng của từ "beautiful"?',
        'Điền vào chỗ trống: She ___ to school every day.',
        'Nghe và chọn câu đúng?',
        'Từ nào đồng nghĩa với "happy"?',
        '"Good morning" được dùng vào thời điểm nào?',
        'Chọn đáp án đúng về ngữ pháp?',
        'Từ trái nghĩa của "hot" là gì?',
        'Hoàn thành câu: I ___ (be) a student.',
        'Dịch sang tiếng Anh: "Cảm ơn bạn"?',
        'Thì hiện tại đơn dùng khi nào?'
    ])[((gs-1) % 10) + 1],
    (ARRAY['single_choice','multiple_choice','true_false','fill_blank'])[((gs-1) % 4) + 1],
    (ARRAY[1,2,5,10])[((gs-1) % 4) + 1]
FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 8. QUIZ_OPTION (~4000 lựa chọn, 4 options/question)
-- ============================================================
INSERT INTO quiz_options (quiz_option_id, question_id, option_text, is_correct, sort_order)
SELECT
    gs,
    CEIL(gs::float / 4)::int,
    (ARRAY['A. Đẹp','B. Xấu','C. To','D. Nhỏ',
           'A. goes','B. go','C. going','D. gone',
           'A. Đúng','B. Sai','C. Có thể','D. Không xác định',
           'A. Glad','B. Sad','C. Angry','D. Tired'])[((gs-1) % 16) + 1],
    CASE WHEN (gs-1) % 4 = 0 THEN true ELSE false END,  -- option đầu luôn đúng
    (gs-1) % 4 + 1
FROM generate_series(1, 4000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 9. ENROLLMENT (~1000 lượt đăng ký)
-- ============================================================
INSERT INTO enrollments (enrollment_id, user_id, course_id, enrolled_at)
SELECT
    gs,
    (gs % 970) + 31,                         -- user student (id 31-1000)
    (gs % 1000) + 1,
    NOW() - (random() * INTERVAL '1 year')
FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 10. PAYMENT (~1000 giao dịch)
-- ============================================================
INSERT INTO payments (payment_id, user_id, course_id, amount, transaction_ref, payment_method, status, paid_at)
SELECT
    gs,
    (gs % 970) + 31,
    (gs % 1000) + 1,
    ROUND((CAST(random() * 500000 + 50000 AS numeric)), -3),
    'TXN' || LPAD(gs::text, 10, '0'),
    (ARRAY['credit_card','momo','vnpay','bank_transfer','zalopay'])[((gs-1) % 5) + 1],
    (ARRAY['paid','pending','failed','refunded'])[
        CASE WHEN random() < 0.85 THEN 1
             WHEN random() < 0.10 THEN 2
             WHEN random() < 0.04 THEN 3
             ELSE 4 END
    ],
    NOW() - (random() * INTERVAL '1 year')
FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 11. QUIZ_ATTEMPT (~1000 lượt thi)
-- ============================================================
INSERT INTO quiz_attempts (quiz_attempt_id, user_id, quiz_id, attempt_number, score, is_passed, started_at, submitted_at)
SELECT
    gs,
    (gs % 970) + 31,
    (gs % 1000) + 1,
    (gs % 3) + 1,
    ROUND(CAST(random() * 100 AS numeric), 2),
    (random() > 0.35),
    NOW() - (random() * INTERVAL '6 months'),
    NOW() - (random() * INTERVAL '6 months') + INTERVAL '30 minutes'
FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 12. QUIZ_ANSWER (~1000 câu trả lời)
-- ============================================================
INSERT INTO quiz_answers (
    quiz_answer_id,
    quiz_attempt_id,
    question_id,
    selected_option_id,
    answer_text,
    is_correct,
    points_earned,
    created_at,
    updated_at
)
SELECT
    gs,

    (gs % 1000) + 1,     -- quiz_attempt_id

    (gs % 1000) + 1,     -- question_id

    ((gs - 1) * 4 % 4000) + 1, -- selected_option_id

    (ARRAY['A','B','C','D'])[
        ((gs - 1) % 4) + 1
    ],

    (random() > 0.4),

    CASE
        WHEN random() > 0.4 THEN 1
        ELSE 0
    END,

    NOW(),
    NOW()

FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 13. LESSON_PROGRESS (~1000 tiến độ học)
-- ============================================================
INSERT INTO lesson_progresses (
    progress_id,
    user_id,
    lesson_id,
    completed_percent,
    is_completed,
    completed_at,
    created_at,
    updated_at
)
SELECT
    gs,

    (gs % 970) + 31,

    (gs % 1000) + 1,

    CASE
        WHEN random() > 0.3
            THEN 100
        ELSE
            ROUND(CAST(random() * 99 AS numeric), 2)
    END,

    (random() > 0.3),

    CASE
        WHEN random() > 0.3
            THEN NOW() - (random() * INTERVAL '6 months')
        ELSE
            NULL
    END,

    NOW(),
    NOW()

FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- ============================================================
-- 14. COURSE_REVIEW (~1000 đánh giá)
-- ============================================================
INSERT INTO course_reviews (
    course_review_id,
    user_id,
    course_id,
    rating,
    comment,
    created_at,
    updated_at
)
SELECT
    gs,

    (gs % 970) + 31,

    (gs % 1000) + 1,

    ROUND(CAST(random() * 4 + 1 AS numeric), 1),

    (ARRAY[
        'Khóa học rất hay, giảng viên nhiệt tình!',
        'Nội dung phong phú, dễ hiểu.',
        'Tôi đã học được rất nhiều từ khóa học này.',
        'Bài giảng sinh động, có nhiều ví dụ thực tế.',
        'Rất hài lòng với chất lượng khóa học.',
        'Phương pháp giảng dạy hiệu quả.',
        'Tài liệu đầy đủ và chi tiết.',
        'Khóa học phù hợp với người mới bắt đầu.',
        'Cần thêm bài tập thực hành.',
        'Giá cả hợp lý, chất lượng tốt.'
    ])[((gs - 1) % 10) + 1],

    NOW() - (random() * INTERVAL '1 year'),

    NOW() - (random() * INTERVAL '30 days')

FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;
-- ============================================================
-- 15. CERTIFICATE (~1000 chứng chỉ)
-- ============================================================
INSERT INTO certificates (
    certificate_id,
    user_id,
    course_id,
    cert_code,
    issued_at
)
SELECT
    gs,

    (gs % 970) + 31,

    (gs % 1000) + 1,

    'CERT-' || UPPER(
        SUBSTRING(
            MD5(gs::text || RANDOM()::text),
            1,
            12
        )
    ),

    NOW() - (random() * INTERVAL '1 year')

FROM generate_series(1, 1000) gs
ON CONFLICT DO NOTHING;

-- reset Postgresql Sequence sau khi chay DataSample.sql
-- Chay sau khi tao bang

SELECT setval('users_user_id_seq',          (SELECT MAX(user_id)          FROM users)          + 1);
SELECT setval('courses_course_id_seq',       (SELECT MAX(course_id)        FROM courses)        + 1);
SELECT setval('languages_language_id_seq',   (SELECT MAX(language_id)      FROM languages)      + 1);
SELECT setval('sections_section_id_seq',     (SELECT MAX(section_id)       FROM sections)       + 1);
SELECT setval('lessons_lesson_id_seq',       (SELECT MAX(lesson_id)        FROM lessons)        + 1);
SELECT setval('quizzes_quiz_id_seq',         (SELECT MAX(quiz_id)          FROM quizzes)        + 1);
SELECT setval('quiz_questions_quiz_question_id_seq', (SELECT MAX(quiz_question_id) FROM quiz_questions) + 1);
SELECT setval('quiz_options_quiz_option_id_seq',     (SELECT MAX(quiz_option_id)   FROM quiz_options)   + 1);
SELECT setval('quiz_attempts_quiz_attempt_id_seq',   (SELECT MAX(quiz_attempt_id)  FROM quiz_attempts)  + 1);
SELECT setval('quiz_answers_quiz_answer_id_seq',     (SELECT MAX(quiz_answer_id)   FROM quiz_answers)   + 1);
SELECT setval('enrollments_enrollment_id_seq',       (SELECT MAX(enrollment_id)    FROM enrollments)    + 1);
SELECT setval('payments_payment_id_seq',             (SELECT MAX(payment_id)       FROM payments)       + 1);
SELECT setval('lesson_progresses_progress_id_seq',   (SELECT MAX(progress_id)      FROM lesson_progresses) + 1);
SELECT setval('course_reviews_course_review_id_seq', (SELECT MAX(course_review_id) FROM course_reviews) + 1);
SELECT setval('certificates_certificate_id_seq',     (SELECT MAX(certificate_id)   FROM certificates)   + 1);