<?php

namespace App\Http\Controllers;

use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::query()
            ->with('user')
            ->withCount('sections')
            ->withAvg('courseReviews', 'rating')
            ->where('is_published', true)
            ->latest()
            ->paginate(12);

        return view('pages.course', compact('courses'));
    }

    public function show(int $id)
    {
        $course = Course::with([
            'user',
            'sections.lessons',
            'courseReviews.user',
            'language'
        ])->findOrFail($id);

        return view('pages.course-detail', compact('course'));
    }
}