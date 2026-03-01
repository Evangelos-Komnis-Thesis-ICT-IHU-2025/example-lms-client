@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Learner: {{ $user->name }}</h1>
        <p><strong>Local user id:</strong> {{ $user->id }}</p>
        <p><strong>Engine user id:</strong> <span class="mono">{{ $user->engine_user_id ?? '-' }}</span></p>
    </div>

    <div class="card">
        <h2>My Enrollments</h2>
        <table>
            <thead>
            <tr>
                <th>Course</th>
                <th>Standard</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($enrollments as $enrollment)
                @php($course = $coursesById[$enrollment->getCourseId()] ?? null)
                <tr>
                    <td>{{ $course?->getTitle() ?? $enrollment->getCourseId() }}</td>
                    <td>{{ $course?->getStandard() ?? '-' }}</td>
                    <td>
                        <form method="post" action="{{ route('learner.launch', ['user' => $user->id, 'courseId' => $enrollment->getCourseId()]) }}">
                            @csrf
                            <button type="submit">Launch</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No enrollments.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Attempts</h2>
        <table>
            <thead>
            <tr>
                <th>Attempt ID</th>
                <th>Course ID</th>
                <th>Status</th>
                <th>Completion</th>
                <th>Progress</th>
            </tr>
            </thead>
            <tbody>
            @forelse($attempts as $attempt)
                <tr>
                    <td class="mono">{{ $attempt->getId() }}</td>
                    <td class="mono">{{ $attempt->getCourseId() }}</td>
                    <td>{{ $attempt->getStatus() ?? '-' }}</td>
                    <td>{{ $attempt->getCompletionStatus() ?? '-' }}</td>
                    <td>
                        <a href="{{ route('learner.progress', ['user' => $user->id, 'attempt_id' => $attempt->getId(), 'course_id' => $attempt->getCourseId()]) }}">View progress</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No attempts yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
