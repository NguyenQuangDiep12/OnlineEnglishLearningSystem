<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ICertificateService;
use App\Services\Interfaces\IEnrollmentService;
use App\Services\Interfaces\ILessonProgressService;
use App\Services\Interfaces\ILessonService;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct(
        protected ILessonService         $lessonService,
        protected ILessonProgressService $progressService,
        protected IEnrollmentService     $enrollmentService,
        protected ICertificateService    $certService,
    ) {}

    public function show(int $courseId, int $lessonId)
    {
        $userId = session('user_id');

        if (!$this->enrollmentService->isEnrolled($userId, $courseId)) {
            return redirect()->route('courses.show', $courseId)
                ->withErrors('Bạn cần đăng ký khóa học trước khi học bài này.');
        }

        $lesson = $this->lessonService->findById($lessonId);
        $lesson->load(['section.course.sections.lessons']);

        $progress = $this->progressService->getProgress($userId, $lessonId);

        $completedIds = $this->progressService
            ->getCompletedLessons($userId, $courseId)
            ->pluck('lesson_id');

        $courseProgress = $this->progressService->getCourseProgress($userId, $courseId);
        return view('pages.student.lesson', compact(
            'lesson', 'progress', 'completedIds', 'courseProgress', 'courseId'
        ));
    }

    public function updateProgress(Request $request, int $lessonId)
    {
        $data = $request->validate([
            'percent' => 'required|numeric|min:0|max:100',
        ]);

        $progress = $this->progressService->updateProgress(
            session('user_id'),
            $lessonId,
            $data['percent']
        );

        return response()->json([
            'is_completed' => $progress->is_completed,
            'percent'      => $progress->completed_percent,
        ]);
    }

    public function markComplete(Request $request, int $courseId, int $lessonId)
    {
        $userId = session('user_id');

        $this->progressService->markCompleted($userId, $lessonId);

        if ($this->certService->canReceive($userId, $courseId)) {
            $cert = $this->certService->issue($userId, $courseId);
            return back()->with('cert_issued', $cert->cert_code);
        }

        return back()->with('success', 'Đã hoàn thành bài học!');
    }
}

?>