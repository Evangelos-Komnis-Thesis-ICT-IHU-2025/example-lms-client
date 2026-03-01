<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Infrastructure\ScormEngine\ScormEngineGateway;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final readonly class UsersService
{
    public function __construct(private ScormEngineGateway $gateway)
    {
    }

    /**
     * @param array{name:string,email:string,password:string} $validated
     */
    public function createLocalAndSync(array $validated): User
    {
        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return $this->syncToEngine($user);
    }

    public function syncToEngine(User $user): User
    {
        if ($user->engine_user_id !== null && $user->engine_user_id !== '') {
            return $user;
        }

        $engineUser = $this->gateway->syncUser($user);

        if ($engineUser->getId() === '') {
            throw ValidationException::withMessages([
                'engine' => 'Engine user sync returned empty identifier.',
            ]);
        }

        $user->engine_user_id = $engineUser->getId();
        $user->save();

        return $user;
    }
}
