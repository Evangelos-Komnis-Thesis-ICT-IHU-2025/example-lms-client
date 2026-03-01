<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Infrastructure\ScormEngine\ScormEngineGateway;
use App\Models\User;
use ScormEngineSdk\Model\Dto\AttemptProgressDto;

final readonly class ProgressService
{
    public function __construct(private ScormEngineGateway $gateway)
    {
    }

    /**
     * @return array<int,mixed>
     */
    public function listAttempts(User $user, ?string $courseId = null): array
    {
        if ($user->engine_user_id === null || $user->engine_user_id === '') {
            return [];
        }

        return $this->gateway->listAttempts($user->engine_user_id, $courseId);
    }

    public function getProgress(string $attemptId): AttemptProgressDto
    {
        return $this->gateway->getProgress($attemptId);
    }
}
