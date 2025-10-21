<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test import index page loads
     */
    public function test_import_index_page_loads(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/import');

        $response->assertStatus(200);
    }

    /**
     * Test CSV upload queues import job
     */
    public function test_csv_upload_queues_job(): void
    {
        Queue::fake();
        
        $user = User::factory()->create(['email_verified_at' => now()]);
        
        $file = UploadedFile::fake()->create('health_data.csv', 100, 'text/csv');

        $response = $this->actingAs($user)->post('/import/upload', [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        Queue::assertPushed(\App\Jobs\ImportMetricsJob::class);
    }

    /**
     * Test upload requires authentication
     */
    public function test_upload_requires_authentication(): void
    {
        $file = UploadedFile::fake()->create('health_data.csv', 100, 'text/csv');

        $response = $this->post('/import/upload', [
            'file' => $file,
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * Test upload validates file type
     */
    public function test_upload_validates_file_type(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/import/upload', [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }
}

