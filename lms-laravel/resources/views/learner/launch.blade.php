@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Launch Player</h1>
        <p><strong>Launch ID:</strong> <span class="mono">{{ $launch->getLaunchId() }}</span></p>
        <p><strong>Attempt ID:</strong> <span class="mono">{{ $launch->getAttemptId() }}</span></p>
        <p><a href="{{ route('learner.progress', ['user' => $user->id, 'attempt_id' => $launch->getAttemptId(), 'course_id' => $courseId]) }}">Open progress page</a></p>
        <p id="termination-status" style="display: none; color: #166534; font-weight: 600;"></p>
    </div>

    <div class="card">
        <iframe id="learner-launch-frame" src="{{ $launch->getLaunchUrl() }}" style="width: 100%; height: 640px; border: 1px solid #cbd5e1; border-radius: 8px;"></iframe>
    </div>

    <script>
        (() => {
            const frame = document.getElementById('learner-launch-frame');
            if (!frame) {
                return;
            }

            const status = document.getElementById('termination-status');
            const progressBaseUrl = @json(route('learner.progress', ['user' => $user->id]));
            const courseId = @json($courseId);
            const fallbackAttemptId = @json($launch->getAttemptId());

            let allowedOrigin = null;
            try {
                allowedOrigin = new URL(frame.src, window.location.href).origin;
            } catch (error) {
                console.warn('Failed to parse player origin', error);
            }

            window.addEventListener('message', (event) => {
                if (allowedOrigin && event.origin !== allowedOrigin) {
                    return;
                }

                if (!event.data || typeof event.data !== 'object' || event.data.type !== 'SCORM_PLAYER_TERMINATED') {
                    return;
                }

                if (status) {
                    status.textContent = 'Course session terminated. Redirecting to progress...';
                    status.style.display = 'block';
                }

                const attemptId = typeof event.data.attemptId === 'string' && event.data.attemptId !== ''
                    ? event.data.attemptId
                    : fallbackAttemptId;

                const target = new URL(progressBaseUrl, window.location.origin);
                target.searchParams.set('course_id', courseId);
                if (attemptId) {
                    target.searchParams.set('attempt_id', attemptId);
                }

                window.location.assign(target.toString());
            });
        })();
    </script>
@endsection
