<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Infrastructure\ScormEngine\ScormEngineGateway;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final readonly class EnrollmentsService
{
    public function __construct(private ScormEngineGateway $gateway)
    {
    }

    public function enrollUserToCourse(User $user, string $courseId): void
    {
        if ($user->engine_user_id === null || $user->engine_user_id === '') {
            throw ValidationException::withMessages([
                'user' => 'User is not synced to engine yet.',
            ]);
        }

        $this->gateway->enroll($user->engine_user_id, $courseId);
    }

    /**
     * @return array<int,mixed>
     */
    public function listUserEnrollments(User $user): array
    {
        if ($user->engine_user_id === null || $user->engine_user_id === '') {
            return [];
        }

        return $this->gateway->listEnrollments($user->engine_user_id);
    }
}
