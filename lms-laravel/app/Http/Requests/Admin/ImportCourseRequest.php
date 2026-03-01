<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class ImportCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:zip'],
            'code' => ['nullable', 'string', 'max:120'],
            'versionLabel' => ['nullable', 'string', 'max:120'],
        ];
    }
}
