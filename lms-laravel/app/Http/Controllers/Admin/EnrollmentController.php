<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Services\CoursesService;
use App\Application\Services\EnrollmentsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EnrollUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class EnrollmentController extends Controller
{
    public function __construct(
        private readonly CoursesService $coursesService,
        private readonly EnrollmentsService $enrollmentsService
    ) {
    }

    public function index(Request $request): View
    {
        $users = User::query()->orderBy('name')->get();
        $courses = $this->coursesService->listCourses();

        $selectedUser = null;
        $enrollments = [];

        $selectedUserId = $request->query('user_id');
        if (is_string($selectedUserId) && $selectedUserId !== '') {
            $selectedUser = User::query()->find((int) $selectedUserId);
            if ($selectedUser !== null) {
                $enrollments = $this->enrollmentsService->listUserEnrollments($selectedUser);
            }
        }

        return view('admin.enrollments', [
            'users' => $users,
            'courses' => $courses,
            'selectedUser' => $selectedUser,
            'enrollments' => $enrollments,
        ]);
    }

    public function store(EnrollUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = User::query()->findOrFail((int) $validated['user_id']);

        try {
            $this->enrollmentsService->enrollUserToCourse($user, (string) $validated['course_id']);

            return redirect()
                ->route('admin.enrollments.index', ['user_id' => $user->id])
                ->with('status', 'Enrollment created for user ' . $user->email);
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.enrollments.index', ['user_id' => $user->id])
                ->withErrors(['enrollment' => $e->getMessage()]);
        }
    }
}
