@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Admin: Create LMS User + Sync to Engine</h1>
        <form method="post" action="{{ route('admin.users.store') }}">
            @csrf
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            <button type="submit">Create and Sync</button>
        </form>
    </div>

    <div class="card">
        <h2>Local Users</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Engine User ID</th>
                <th>Learner</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="mono">{{ $user->engine_user_id ?? '-' }}</td>
                    <td>
                        <a href="{{ route('learner.courses', ['user' => $user->id]) }}">Open learner view</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No users yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
