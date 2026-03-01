<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Services\UsersService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateLmsUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

final class UserController extends Controller
{
    public function __construct(private readonly UsersService $usersService)
    {
    }

    public function index(): View
    {
        return view('admin.users', [
            'users' => User::query()->orderByDesc('id')->get(),
        ]);
    }

    public function store(CreateLmsUserRequest $request): RedirectResponse
    {
        try {
            $user = $this->usersService->createLocalAndSync($request->validated());

            return redirect()
                ->route('admin.users.index')
                ->with('status', 'Created and synced user: ' . $user->email);
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['user' => $e->getMessage()])
                ->withInput();
        }
    }
}
