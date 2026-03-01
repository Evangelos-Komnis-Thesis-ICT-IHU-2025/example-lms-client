# example-lms-client

## Scope
Laravel LMS reference client that consumes:
- `scorm-engine` API through `scorm-engine-php-sdk`
- `player` launch URLs for SCORM runtime delivery

Repository app path: `lms-laravel/`.

## Runtime
- Default HTTP port: `8000`
- Engine target base URL: `http://localhost:8080/api/v1`
- Player target base URL: `http://localhost:3000`

## Architecture
```mermaid
graph LR
  Browser[Browser] --> LMS[Laravel LMS :8000]
  LMS --> SDK[scorm-engine-php-sdk]
  SDK --> Engine[SCORM Engine :8080]
  Browser --> Player[SCORM Player :3000]
```

## Request Flow (Learner Launch)
```mermaid
sequenceDiagram
  participant B as Browser
  participant L as LMS
  participant E as Engine
  participant P as Player

  B->>L: POST /learner/users/{user}/courses/{courseId}/launch
  L->>E: POST /api/v1/launches (via SDK)
  E-->>L: launchUrl + attemptId
  L-->>B: launch page with iframe(src=launchUrl)
  B->>P: GET /launch/{launchId}?token=...
  P->>E: runtime context + commit/terminate calls
```

## Run
```bash
cd lms-laravel
composer install
php artisan key:generate
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
```

Open `http://localhost:8000`.
