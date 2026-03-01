<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SCORM LMS Demo</title>
    <style>
        body { font-family: "Segoe UI", sans-serif; margin: 0; background: #f5f7fb; color: #1e293b; }
        header { background: #0f172a; color: #fff; padding: 14px 20px; }
        nav a { color: #93c5fd; margin-right: 14px; text-decoration: none; font-weight: 600; }
        main { padding: 20px; max-width: 1100px; margin: 0 auto; }
        .card { background: #fff; border: 1px solid #dbe3ee; border-radius: 10px; padding: 16px; margin-bottom: 16px; }
        h1, h2, h3 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        label { display: block; font-weight: 600; margin-top: 8px; }
        input, select { width: 100%; max-width: 460px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { margin-top: 12px; background: #2563eb; color: #fff; border: 0; padding: 8px 14px; border-radius: 8px; cursor: pointer; }
        .status { background: #ecfdf5; border: 1px solid #34d399; padding: 8px; border-radius: 8px; margin-bottom: 12px; }
        .errors { background: #fef2f2; border: 1px solid #f87171; padding: 8px; border-radius: 8px; margin-bottom: 12px; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    </style>
</head>
<body>
<header>
    <nav>
        <a href="{{ route('admin.courses.index') }}">Admin Courses</a>
        <a href="{{ route('admin.users.index') }}">Admin Users</a>
        <a href="{{ route('admin.enrollments.index') }}">Admin Enrollments</a>
    </nav>
</header>
<main>
    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="errors">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
