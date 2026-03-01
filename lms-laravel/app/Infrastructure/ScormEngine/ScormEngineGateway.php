<?php

declare(strict_types=1);

namespace App\Infrastructure\ScormEngine;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use ScormEngineSdk\Client\ScormEngineClient;
use ScormEngineSdk\Model\Dto\AttemptProgressDto;
use ScormEngineSdk\Model\Dto\CourseDto;
use ScormEngineSdk\Model\Dto\EnrollmentDto;
use ScormEngineSdk\Model\Dto\LaunchCreatedDto;
use ScormEngineSdk\Model\Dto\UserDto;
use ScormEngineSdk\Model\Form\CreateLaunchForm;
use ScormEngineSdk\Model\Form\CreateUserForm;
use ScormEngineSdk\Model\Form\EnrollUserForm;
use ScormEngineSdk\Model\Dto\AttemptDto;
use ScormEngineSdk\Model\Query\AttemptListQuery;
use ScormEngineSdk\Model\Query\CourseListQuery;
use ScormEngineSdk\Model\Query\EnrollmentListQuery;

final readonly class ScormEngineGateway
{
    public function __construct(private ScormEngineClient $client)
    {
    }

    public function importCourse(UploadedFile $file, ?string $code, ?string $versionLabel): CourseDto
    {
        $originalName = trim($file->getClientOriginalName());
        $filename = $originalName !== '' ? basename($originalName) : basename($file->getRealPath() ?: $file->path());

        return $this->client->courses()->importCourse(
            zipPath: $file->getRealPath() ?: $file->path(),
            code: $code,
            versionLabel: $versionLabel,
            filename: $filename
        );
    }

    /**
     * @return array<int,CourseDto>
     */
    public function listCourses(int $page = 0, int $size = 100): array
    {
        $result = $this->client->courses()->listCourses(new CourseListQuery(page: $page, size: $size));
        return $result->getItems();
    }

    public function syncUser(User $user): UserDto
    {
        $username = $user->email !== '' ? Str::before($user->email, '@') : 'user' . $user->id;

        return $this->client->users()->createUser(new CreateUserForm(
            externalRef: (string) $user->id,
            username: $username,
            email: $user->email,
            firstName: $user->name,
            lastName: null,
            locale: 'en'
        ));
    }

    public function enroll(string $engineUserId, string $courseId): EnrollmentDto
    {
        return $this->client->enrollments()->enroll(new EnrollUserForm(
            userId: $engineUserId,
            courseId: $courseId
        ));
    }

    /**
     * @return array<int,EnrollmentDto>
     */
    public function listEnrollments(?string $engineUserId = null, ?string $courseId = null): array
    {
        $result = $this->client->enrollments()->listEnrollments(new EnrollmentListQuery(
            userId: $engineUserId,
            courseId: $courseId
        ));

        return $result->getItems();
    }

    public function createLaunch(string $engineUserId, string $courseId): LaunchCreatedDto
    {
        return $this->client->launches()->createLaunch(new CreateLaunchForm(
            userId: $engineUserId,
            courseId: $courseId,
            forceNewAttempt: true
        ));
    }

    /**
     * @return array<int,AttemptDto>
     */
    public function listAttempts(string $engineUserId, ?string $courseId = null): array
    {
        $result = $this->client->attempts()->listAttempts(new AttemptListQuery(
            userId: $engineUserId,
            courseId: $courseId
        ));

        return $result->getItems();
    }

    public function getProgress(string $attemptId): AttemptProgressDto
    {
        return $this->client->attempts()->getProgress($attemptId);
    }
}
