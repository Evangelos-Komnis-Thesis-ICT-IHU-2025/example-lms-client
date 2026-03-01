@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Admin: Upload SCORM Course</h1>
        <form method="post" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
            @csrf
            <label for="file">SCORM Zip</label>
            <input id="file" type="file" name="file" required>

            <label for="code">Code (optional)</label>
            <input id="code" type="text" name="code" value="{{ old('code') }}">

            <label for="versionLabel">Version Label (optional)</label>
            <input id="versionLabel" type="text" name="versionLabel" value="{{ old('versionLabel') }}">

            <button type="submit">Import Course</button>
        </form>
    </div>

    <div class="card">
        <h2>Courses from Engine</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Standard</th>
                <th>Entrypoint</th>
            </tr>
            </thead>
            <tbody>
            @forelse($courses as $course)
                <tr>
                    <td class="mono">{{ $course->getId() }}</td>
                    <td>{{ $course->getTitle() ?? '-' }}</td>
                    <td>{{ $course->getStandard() ?? '-' }}</td>
                    <td>{{ $course->getEntrypointPath() ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No courses yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
