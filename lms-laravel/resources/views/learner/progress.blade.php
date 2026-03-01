@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Learner Progress: {{ $user->name }}</h1>
        <p><a href="{{ route('learner.courses', ['user' => $user->id]) }}">Back to my courses</a></p>
    </div>

    <div class="card">
        <h2>Attempts</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Course</th>
                <th>Status</th>
                <th>Completion</th>
                <th>Action</th>
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
                        <a href="{{ route('learner.progress', ['user' => $user->id, 'attempt_id' => $attempt->getId(), 'course_id' => $attempt->getCourseId()]) }}">Details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No attempts.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Selected Attempt Progress</h2>
        @if($progress)
            @php($normalized = $progress->getNormalizedProgress())
            @php($normalizedPayload = $normalized ? [
                'completionStatus' => $normalized->getCompletionStatus(),
                'successStatus' => $normalized->getSuccessStatus(),
                'score' => $normalized->getScore(),
                'time' => $normalized->getTime(),
                'lastLocation' => $normalized->getLastLocation(),
                'bookmark' => $normalized->getBookmark(),
                'raw' => $normalized->getRaw(),
            ] : null)
            <p><strong>Attempt:</strong> <span class="mono">{{ $progress->getAttemptId() }}</span></p>
            <pre class="mono">{{ json_encode($normalizedPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        @else
            <p>Select an attempt to view normalized progress.</p>
        @endif
    </div>
@endsection
