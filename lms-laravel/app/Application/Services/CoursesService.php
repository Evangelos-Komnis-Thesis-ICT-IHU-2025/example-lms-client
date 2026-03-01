<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Infrastructure\ScormEngine\ScormEngineGateway;
use Illuminate\Http\UploadedFile;
use ScormEngineSdk\Model\Dto\CourseDto;

final readonly class CoursesService
{
    public function __construct(private ScormEngineGateway $gateway)
    {
    }

    public function importCourse(UploadedFile $file, ?string $code, ?string $versionLabel): CourseDto
    {
        return $this->gateway->importCourse($file, $code, $versionLabel);
    }

    /**
     * @return array<int,CourseDto>
     */
    public function listCourses(): array
    {
        return $this->gateway->listCourses();
    }
}
