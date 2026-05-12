<?php

use Illuminate\Support\Facades\Route;

// ── Public Controllers ───────────────────────────────────────
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\RoadMapController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;

// ── Auth ─────────────────────────────────────────────────────
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// ── Student ──────────────────────────────────────────────────
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\LessonController as StudentLesson;
use App\Http\Controllers\Student\QuizController as StudentQuiz;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\CourseReviewController;
use App\Http\Controllers\Student\CertificateController;

// ── Instructor ───────────────────────────────────────────────
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboard;
use App\Http\Controllers\Instructor\CourseManagerController;
use App\Http\Controllers\Instructor\QuizManagerController;

// ── Admin ────────────────────────────────────────────────────
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\CourseController as AdminCourse;

// ============================================================
// PUBLIC ROUTES
// ============================================================

Route::get('/',        [HomeController::class, 'index'])->name('home');
Route::get('/home',    [HomeController::class, 'index']);
Route::get('/about',   [AboutController::class, 'index'])->name('about');
Route::get('/roadmap', [RoadMapController::class, 'index'])->name('roadmap');

Route::get('/courses',      [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/instructors',      [InstructorController::class, 'index'])->name('instructor.index');
Route::get('/instructors/{id}', [InstructorController::class, 'show'])->name('instructor.show'); // done

Route::get('/blog',        [BlogController::class, 'index'])->name('blog.index'); // error (chua co bang Blog)
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show'); // error

Route::get('/verify',        [CertificateController::class, 'verify'])->name('certificate.verify'); // error
Route::get('/verify/{code}', [CertificateController::class, 'show'])->name('certificate.show'); // error

// ============================================================
// AUTH ROUTES
// ============================================================

Route::middleware('guest_session')->group(function () {
    Route::get('/login',     [LoginController::class, 'showForm'])->name('login');
    Route::post('/login',    [LoginController::class, 'login'])->name('login.post');
    Route::get('/register',  [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth_session')
    ->name('logout');

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================

Route::middleware('auth_session')->group(function () {

    // ── Profile ──────────────────────────────────────────────
    Route::get('/profile',                  [ProfileController::class, 'show'])->name('profile.show'); // error -- chua co ui trang pages.profile
    Route::get('/profile/edit',             [ProfileController::class, 'edit'])->name('profile.edit'); // error
    Route::put('/profile',                  [ProfileController::class, 'update'])->name('profile.update'); // error
    Route::post('/profile/avatar',          [ProfileController::class, 'updateAvatar'])->name('profile.avatar'); // error
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.password'); // error

    // ── Payment callback (shared) ────────────────────────────
    Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

    // ============================================================
    // STUDENT ROUTES  (middleware: role:student)
    // ============================================================
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {

        Route::get('/dashboard',  [StudentDashboard::class, 'index'])->name('dashboard');
        Route::get('/my-courses', [StudentDashboard::class, 'myCourses'])->name('my-courses');

        // Enrollment
        Route::post('/enroll/{courseId}',    [EnrollmentController::class, 'enroll'])->name('enroll');
        Route::delete('/unenroll/{courseId}',[EnrollmentController::class, 'unenroll'])->name('unenroll');
        Route::get('/after-payment',         [EnrollmentController::class, 'afterPayment'])->name('after-payment');

        // Lesson
        Route::get('/courses/{courseId}/lessons/{lessonId}',
            [StudentLesson::class, 'show'])->name('lesson.show');
        Route::post('/lessons/{lessonId}/progress',
            [StudentLesson::class, 'updateProgress'])->name('lesson.progress');
        Route::post('/courses/{courseId}/lessons/{lessonId}/complete',
            [StudentLesson::class, 'markComplete'])->name('lesson.complete');

        // Quiz
        Route::get('/quiz/attempt/{attemptId}', [StudentQuiz::class, 'attempt'])->name('quiz.attempt');
        Route::get('/quiz/result/{attemptId}',  [StudentQuiz::class, 'result'])->name('quiz.result');
        Route::get('/quiz/{quizId}',            [StudentQuiz::class, 'show'])->name('quiz.show');
        Route::post('/quiz/{quizId}/start',     [StudentQuiz::class, 'start'])->name('quiz.start');
        Route::post('/quiz/attempt/{attemptId}/submit',
            [StudentQuiz::class, 'submit'])->name('quiz.submit');

        // Payment
        Route::get('/checkout/{courseId}',  [PaymentController::class, 'checkout'])->name('checkout'); 
        // error SQLSTATE[23505]: Unique violation: 7 ERROR: duplicate key value violates unique constraint "payments_pkey" 
        // DETAIL: Key (payment_id)=(2) already exists. (Connection: pgsql, Host: 127.0.0.1, Port: 5432, Database: OnlineEnglishLearningSystem, 
        // SQL: insert into "payments" ("user_id", "course_id", "amount", "transaction_ref", "payment_method", "status", "updated_at", "created_at") 
        // values (1001, 1, 457000.00, TXN-NCDPEGHNZNP4, credit_card, pending, 2026-05-11 10:19:33, 2026-05-11 10:19:33) returning "payment_id")
        Route::post('/checkout/{courseId}', [PaymentController::class, 'createPayment'])->name('payment.create'); 

        Route::get('/payment/history',      [PaymentController::class, 'history'])->name('payment.history'); // error 404 not found

        // Review
        Route::post('/courses/{courseId}/review', [CourseReviewController::class, 'store'])->name('review.store'); // error 404 not found
        Route::put('/reviews/{reviewId}',         [CourseReviewController::class, 'update'])->name('review.update'); // error 404 not found
        Route::delete('/reviews/{reviewId}',      [CourseReviewController::class, 'destroy'])->name('review.destroy'); // / error 404 not found

        // Certificates
        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates'); // errror 404 not found
    });

    // ============================================================
    // INSTRUCTOR ROUTES  (middleware: role:instructor)
    // ============================================================
    Route::middleware('role:instructor')->prefix('instructor')->name('instructor.')->group(function () {

        Route::get('/dashboard', [InstructorDashboard::class, 'index'])->name('dashboard');
        Route::get('/courses',   [InstructorDashboard::class, 'courses'])->name('courses');// error chua co ui phan courses cho instructor

        // Course
        Route::get('/courses/create',          [CourseManagerController::class, 'create'])->name('create-course'); // error tao popup de tao course trong trang pages.instructor.courses
        Route::post('/courses',                [CourseManagerController::class, 'store'])->name('courses.store'); // error tao popup de tao course trong trang pages.instructor.courses
        Route::get('/courses/{courseId}/edit', [CourseManagerController::class, 'edit'])->name('courses.edit'); // error tao popup de tao course trong trang pages.instructor.courses
        Route::put('/courses/{courseId}',      [CourseManagerController::class, 'update'])->name('courses.update'); // error tao popup de tao course trong trang pages.instructor.courses
        Route::delete('/courses/{courseId}',   [CourseManagerController::class, 'destroy'])->name('courses.destroy'); // error tao popup de tao course trong trang pages.instructor.courses
        Route::post('/courses/{courseId}/publish',   [CourseManagerController::class, 'publish'])->name('courses.publish'); // error tao popup de tao course trong trang pages.instructor.courses
        Route::post('/courses/{courseId}/unpublish', [CourseManagerController::class, 'unpublish'])->name('courses.unpublish'); // error tao popup de tao course trong trang pages.instructor.courses

        // Section
        Route::post('/courses/{courseId}/sections',         [CourseManagerController::class, 'storeSection'])->name('sections.store'); // error sua lai trang truy cap
        Route::put('/sections/{sectionId}',                 [CourseManagerController::class, 'updateSection'])->name('sections.update'); // error sua lai trang truy cap
        Route::delete('/sections/{sectionId}',              [CourseManagerController::class, 'destroySection'])->name('sections.destroy'); // error sua lai trang truy cap
        Route::post('/courses/{courseId}/sections/reorder', [CourseManagerController::class, 'reorderSections'])->name('sections.reorder'); // error sua lai trang truy cap

        // Lesson
        Route::post('/sections/{sectionId}/lessons',         [CourseManagerController::class, 'storeLesson'])->name('lessons.store'); // error 404 not found
        Route::put('/lessons/{lessonId}',                    [CourseManagerController::class, 'updateLesson'])->name('lessons.update'); // error 404 not found
        Route::delete('/lessons/{lessonId}',                 [CourseManagerController::class, 'destroyLesson'])->name('lessons.destroy'); // error 404 not found
        Route::post('/sections/{sectionId}/lessons/reorder', [CourseManagerController::class, 'reorderLessons'])->name('lessons.reorder'); // error 404 not found

        // Quiz
        Route::get('/quiz/show/{quizId}',           [QuizManagerController::class, 'show'])->name('quiz.show'); // error 404 not found
        Route::post('/lessons/{lessonId}/quiz',     [QuizManagerController::class, 'store'])->name('quiz.store'); // error 404 not found
        Route::put('/quiz/{quizId}',                [QuizManagerController::class, 'update'])->name('quiz.update'); // error 404 not found
        Route::delete('/quiz/{quizId}',             [QuizManagerController::class, 'destroy'])->name('quiz.destroy'); // error 404 not found

        // Question
        Route::post('/quiz/{quizId}/questions',   [QuizManagerController::class, 'storeQuestion'])->name('question.store'); // error 404 not found
        Route::put('/questions/{questionId}',     [QuizManagerController::class, 'updateQuestion'])->name('question.update'); // error 404 not found
        Route::delete('/questions/{questionId}',  [QuizManagerController::class, 'destroyQuestion'])->name('question.destroy'); // error 404 not found

        // Option
        Route::post('/questions/{questionId}/options',  [QuizManagerController::class, 'storeOption'])->name('option.store'); // error 404 not found
        Route::put('/options/{optionId}',               [QuizManagerController::class, 'updateOption'])->name('option.update'); // error 404 not found
        Route::delete('/options/{optionId}',            [QuizManagerController::class, 'destroyOption'])->name('option.destroy'); // error 404 not found
        Route::post('/options/{optionId}/set-correct',  [QuizManagerController::class, 'setCorrectOption'])->name('option.set-correct'); // error 404 not found
    });

    // ============================================================
    // ADMIN ROUTES  (middleware: role:admin)
    // ============================================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users',                         [AdminUser::class, 'index'])->name('users');
        Route::get('/users/{userId}',                [AdminUser::class, 'show'])->name('users.show'); // error 404 not found
        Route::get('/users/{userId}/edit',           [AdminUser::class, 'edit'])->name('users.edit'); // error 404 not found
        Route::put('/users/{userId}',                [AdminUser::class, 'update'])->name('users.update'); // error 404 not found
        Route::post('/users/{userId}/reset-password',[AdminUser::class, 'resetPassword'])->name('users.reset-password'); // error 404 not found
        Route::delete('/users/{userId}',             [AdminUser::class, 'destroy'])->name('users.destroy'); // error 404 not found

        // Courses - FIX: AdminDashboard@courses và AdminCourse@index đều redirect về route này
        Route::get('/courses',                       [AdminCourse::class, 'index'])->name('courses');
        Route::get('/courses/{courseId}',            [AdminCourse::class, 'show'])->name('courses.show'); // error 404 not found
        Route::post('/courses/{courseId}/publish',   [AdminCourse::class, 'publish'])->name('courses.publish'); // error 404 not found
        Route::post('/courses/{courseId}/unpublish', [AdminCourse::class, 'unpublish'])->name('courses.unpublish'); // error 404 not found
        Route::delete('/courses/{courseId}',         [AdminCourse::class, 'destroy'])->name('courses.destroy'); // error 404 not found
    });
});

?>