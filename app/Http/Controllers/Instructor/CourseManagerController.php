<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Section;
use App\Services\Interfaces\ICourseService;
use App\Services\Interfaces\ILessonService;
use App\Services\Interfaces\ISectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseManagerController extends Controller
{
    public function __construct(
        protected ICourseService  $courseService,
        protected ISectionService $sectionService,
        protected ILessonService  $lessonService,
    ) {}

    public function create()
    {
        $languages = Language::orderBy('name')->get();
        return view('pages.instructor.create-course', compact('languages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'level'       => 'required|in:beginner,elementary,intermediate,upper_intermediate,advanced',
            'language_id' => 'required|exists:languages,language_id',
            'price'       => 'required|numeric|min:0',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_url'] = Storage::url(
                $request->file('thumbnail')->store('thumbnails', 'public')
            );
        }

        $data['teacher_id'] = session('user_id');

        $course = $this->courseService->create($data);

        return redirect()->route('instructor.courses.edit', $course->course_id)
            ->with('success', 'Tạo khóa học thành công! Hãy thêm nội dung.');
    }

    public function edit(int $courseId)
    {
        $course = $this->courseService->findById($courseId);
        $this->authorizeInstructor($course);

        $sections  = $this->sectionService->getByCourse($courseId);
        $languages = Language::orderBy('name')->get();

        return view('pages.instructor.edit-course', compact('course', 'sections', 'languages'));
    }

    public function update(Request $request, int $courseId)
    {
        $course = $this->courseService->findById($courseId);
        $this->authorizeInstructor($course);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'level'       => 'required|in:beginner,elementary,intermediate,upper_intermediate,advanced',
            'price'       => 'required|numeric|min:0',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_url'] = Storage::url(
                $request->file('thumbnail')->store('thumbnails', 'public')
            );
        }

        $this->courseService->update($courseId, $data);

        return back()->with('success', 'Đã cập nhật khóa học.');
    }

    public function destroy(int $courseId)
    {
        $course = $this->courseService->findById($courseId);
        $this->authorizeInstructor($course);
        $this->courseService->delete($courseId);
        return redirect()->route('instructor.courses')->with('success', 'Đã xoá khóa học.');
    }

    public function publish(int $courseId)
    {
        $course = $this->courseService->findById($courseId);
        $this->authorizeInstructor($course);
        $this->courseService->publish($courseId);
        return back()->with('success', 'Khóa học đã được xuất bản.');
    }

    public function unpublish(int $courseId)
    {
        $course = $this->courseService->findById($courseId);
        $this->authorizeInstructor($course);
        $this->courseService->unpublish($courseId);
        return back()->with('success', 'Đã ẩn khóa học.');
    }

    // ── Section ────────────────────────────────────────────────

    public function storeSection(Request $request, int $courseId)
    {
        // FIX: kiểm tra quyền trước khi thêm section
        $course = $this->courseService->findById($courseId);
        $this->authorizeInstructor($course);

        $data = $request->validate(['title' => 'required|string|max:255']);

        $lastOrder = $this->sectionService->getByCourse($courseId)->count();
        $this->sectionService->create([
            'course_id'  => $courseId,
            'title'      => $data['title'],
            'sort_order' => $lastOrder + 1,
        ]);

        return back()->with('success', 'Đã thêm chương mới.');
    }

    public function updateSection(Request $request, int $sectionId)
    {
        // FIX: kiểm tra quyền qua section → course
        $section = $this->sectionService->findById($sectionId);
        $this->authorizeInstructorByCourseId($section->course_id);

        $data = $request->validate(['title' => 'required|string|max:255']);
        $this->sectionService->update($sectionId, $data);
        return back()->with('success', 'Đã cập nhật chương.');
    }

    public function destroySection(int $sectionId)
    {
        $section = $this->sectionService->findById($sectionId);
        $this->authorizeInstructorByCourseId($section->course_id);
        $this->sectionService->delete($sectionId);
        return back()->with('success', 'Đã xoá chương.');
    }

    public function reorderSections(Request $request, int $courseId)
    {
        // FIX: thêm authorization check
        $course = $this->courseService->findById($courseId);
        $this->authorizeInstructor($course);

        $request->validate(['order' => 'required|array']);
        $this->sectionService->reorder($courseId, $request->input('order'));
        return response()->json(['ok' => true]);
    }

    // ── Lesson ────────────────────────────────────────────────

    public function storeLesson(Request $request, int $sectionId)
    {
        $section = $this->sectionService->findById($sectionId);
        $this->authorizeInstructorByCourseId($section->course_id);

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'content'          => 'nullable|string',
            'video_url'        => 'nullable|url',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        $lastOrder = $this->lessonService->getBySection($sectionId)->count();
        $this->lessonService->create(array_merge($data, [
            'section_id' => $sectionId,
            'sort_order' => $lastOrder + 1,
        ]));

        return back()->with('success', 'Đã thêm bài học.');
    }

    public function updateLesson(Request $request, int $lessonId)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'content'          => 'nullable|string',
            'video_url'        => 'nullable|url',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        $this->lessonService->update($lessonId, $data);
        return back()->with('success', 'Đã cập nhật bài học.');
    }

    public function destroyLesson(int $lessonId)
    {
        $this->lessonService->delete($lessonId);
        return back()->with('success', 'Đã xoá bài học.');
    }

    public function reorderLessons(Request $request, int $sectionId)
    {
        // FIX: thêm authorization check
        $section = $this->sectionService->findById($sectionId);
        $this->authorizeInstructorByCourseId($section->course_id);

        $request->validate(['order' => 'required|array']);
        $this->lessonService->reorder($sectionId, $request->input('order'));
        return response()->json(['ok' => true]);
    }

    // ── Helpers ────────────────────────────────────────────────

    private function authorizeInstructor($course): void
    {
        if ((int) $course->teacher_id !== (int) session('user_id')) {
            abort(403, 'Bạn không có quyền thao tác với khóa học này.');
        }
    }

    private function authorizeInstructorByCourseId(int $courseId): void
    {
        $course = $this->courseService->findById($courseId);
        $this->authorizeInstructor($course);
    }
}

?>