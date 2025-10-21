<?php

namespace Tests\Feature;

use App\Models\LabReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LabControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test labs index page loads
     */
    public function test_labs_index_page_loads(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/labs');

        $response->assertStatus(200);
    }

    /**
     * Test lab report upload queues parsing job
     */
    public function test_lab_report_upload_queues_parsing(): void
    {
        Queue::fake();
        
        $user = User::factory()->create(['email_verified_at' => now()]);
        
        $file = UploadedFile::fake()->create('lab_report.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user)->post('/labs/upload', [
            'file' => $file,
            'report_date' => '2025-10-15',
            'facility' => 'Test Medical Center',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        Queue::assertPushed(\App\Jobs\ParseLabReportJob::class);
    }

    /**
     * Test user can only view their own lab reports
     */
    public function test_user_cannot_view_other_users_lab_reports(): void
    {
        $user1 = User::factory()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->create(['email_verified_at' => now()]);
        
        $report = LabReport::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get("/labs/{$report->id}");

        $response->assertStatus(403);
    }

    /**
     * Test upload validates file size
     */
    public function test_upload_validates_file_size(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        
        // File larger than 10MB
        $file = UploadedFile::fake()->create('large_file.pdf', 11000, 'application/pdf');

        $response = $this->actingAs($user)->post('/labs/upload', [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }
}

