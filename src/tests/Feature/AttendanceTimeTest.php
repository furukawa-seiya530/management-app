<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class AttendanceTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_page_displays_current_datetime()
    {
        Carbon::setTestNow(Carbon::create(2025, 6, 25, 14, 30));

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $expectedDate = Carbon::now()->isoFormat('YYYY年M月D日（ddd）');
        $expectedTime = Carbon::now()->format('H:i');

        $response->assertStatus(200);
        $response->assertSee($expectedDate);
        $response->assertSee($expectedTime);
    }
}
