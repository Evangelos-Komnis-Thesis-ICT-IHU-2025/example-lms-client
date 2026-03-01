@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Admin: Enroll User to Course</h1>
        <form method="post" action="{{ route('admin.enrollments.store') }}">
            @csrf
            <label for="user_id">User</label>
            <select id="user_id" name="user_id" required>
                <option value="">Select user</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('user_id', optional($selectedUser)->id) === (string) $user->id)>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>

            <label for="course_id">Course</label>
            <select id="course_id" name="course_id" required>
                <option value="">Select course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->getId() }}">{{ $course->getTitle() ?? $course->getId() }} ({{ $course->getStandard() ?? '-' }})</option>
                @endforeach
            </select>

            <button type="submit">Create Enrollment</button>
        </form>
    </div>

    <div class="card">
        <h2>Selected User Enrollments</h2>
        @if($selectedUser)
            <p><strong>User:</strong> {{ $selectedUser->name }} ({{ $selectedUser->email }})</p>
            <table>
                <thead>
                <tr>
                    <th>Enrollment ID</th>
                    <th>Course ID</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($enrollments as $enrollment)
                    <tr>
                        <td class="mono">{{ $enrollment->getId() }}</td>
                        <td class="mono">{{ $enrollment->getCourseId() }}</td>
                        <td>{{ $enrollment->getStatus() ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No enrollments found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        @else
            <p>Select a user via query param `?user_id=` to view enrollments.</p>
        @endif
    </div>
@endsection
