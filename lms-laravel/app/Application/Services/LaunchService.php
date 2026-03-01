<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Infrastructure\ScormEngine\ScormEngineGateway;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use ScormEngineSdk\Model\Dto\LaunchCreatedDto;

final readonly class LaunchService
{
    public function __construct(private ScormEngineGateway $gateway)
    {
    }

    public function createLaunch(User $user, string $courseId): LaunchCreatedDto
    {
        if ($user->engine_user_id === null || $user->engine_user_id === '') {
            throw ValidationException::withMessages([
                'user' => 'User is not synced to engine.',
            ]);
        }

        return $this->gateway->createLaunch($user->engine_user_id, $courseId);
    }
}
