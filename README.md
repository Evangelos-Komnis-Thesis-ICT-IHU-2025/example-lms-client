# example-lms-client

This repository contains the Laravel demo LMS client for the SCORM Engine stack.

## App path

`lms-laravel/`

## Quick run

```bash
cd lms-laravel
composer install
php artisan key:generate
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
```

Then open `http://localhost:8000`.
