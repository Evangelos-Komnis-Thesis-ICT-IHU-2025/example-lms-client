<?php

declare(strict_types=1);

return [
    'base_url' => env('SCORM_ENGINE_BASE_URL', 'http://localhost:8080/api/v1'),
    'admin_token' => env('SCORM_ENGINE_ADMIN_TOKEN', ''),
    'player_base_url' => env('SCORM_PLAYER_BASE_URL', 'http://localhost:3000'),
    'timeout_seconds' => (float) env('SCORM_ENGINE_TIMEOUT_SECONDS', 15),
];
