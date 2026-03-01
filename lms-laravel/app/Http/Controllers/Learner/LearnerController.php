<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Application\Services\CoursesService;
use App\Application\Services\EnrollmentsService;
use App\Application\Services\LaunchService;
use App\Application\Services\ProgressService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class LearnerController extends Controller
{
    public function __construct(
        private readonly CoursesService $coursesService,
        private readonly EnrollmentsService $enrollmentsService,
        private readonly LaunchService $launchService,
        private readonly ProgressService $progressService
    ) {
    }

    public function courses(User $user): View
    {
        $enrollments = $this->enrollmentsService->listUserEnrollments($user);
        $attempts = $this->progressService->listAttempts($user);

        $coursesById = [];
        foreach ($this->coursesService->listCourses() as $course) {
            $coursesById[$course->getId()] = $course;
        }

        return view('learner.courses', [
            'user' => $user,
            'enrollments' => $enrollments,
            'attempts' => $attempts,
            'coursesById' => $coursesById,
        ]);
    }

    public function launch(User $user, string $courseId): View|RedirectResponse
    {
        try {
            $launch = $this->launchService->createLaunch($user, $courseId);

            return view('learner.launch', [
                'user' => $user,
                'launch' => $launch,
                'courseId' => $courseId,
            ]);
        } catch (Throwable $e) {
            return redirect()
                ->route('learner.courses', ['user' => $user->id])
                ->withErrors(['launch' => $e->getMessage()]);
        }
    }

    public function progress(Request $request, User $user): View|RedirectResponse
    {
        try {
            $courseId = $request->query('course_id');
            $attempts = $this->progressService->listAttempts(
                user: $user,
                courseId: is_string($courseId) ? $courseId : null
            );

            $attemptId = $request->query('attempt_id');
            $progress = null;

            if (is_string($attemptId) && $attemptId !== '') {
                $progress = $this->progressService->getProgress($attemptId);
            }

            return view('learner.progress', [
                'user' => $user,
                'attempts' => $attempts,
                'progress' => $progress,
                'selectedAttemptId' => is_string($attemptId) ? $attemptId : null,
                'selectedCourseId' => is_string($courseId) ? $courseId : null,
            ]);
        } catch (Throwable $e) {
            return redirect()
                ->route('learner.courses', ['user' => $user->id])
                ->withErrors(['progress' => $e->getMessage()]);
        }
    }
}
