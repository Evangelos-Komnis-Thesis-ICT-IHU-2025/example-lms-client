<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Services\CoursesService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportCourseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

final class CourseController extends Controller
{
    public function __construct(private readonly CoursesService $coursesService)
    {
    }

    public function index(): View
    {
        return view('admin.courses', [
            'courses' => $this->coursesService->listCourses(),
        ]);
    }

    public function store(ImportCourseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $course = $this->coursesService->importCourse(
                file: $request->file('file'),
                code: $validated['code'] ?? null,
                versionLabel: $validated['versionLabel'] ?? null
            );

            return redirect()
                ->route('admin.courses.index')
                ->with('status', 'Imported course: ' . ($course->getTitle() ?? $course->getId()));
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.courses.index')
                ->withErrors(['import' => $e->getMessage()]);
        }
    }
}
