<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_root_redirects_to_admin_courses(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin/courses');
    }

    public function test_health_endpoint_is_available(): void
    {
        $response = $this->get('/up');

        $response->assertStatus(200);
    }
}
