<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clock_out_and_status_changes_to_finished()
    {
        $user = User::factory()->create(['status' => '出勤中']);
        $this->actingAs($user)->post('/attendance'); // 出勤処理

        $response = $this->post('/attendance/leave'); // 退勤処理

        $response->assertRedirect('/attendance');
        $this->assertEquals('退勤済', $user->fresh()->status);

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', Carbon::today())->first();

        $this->assertNotNull($attendance->end_time);
    }

    public function test_clock_out_time_appears_in_admin_list()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin);

        Attendance::create([
            'user_id' => $admin->id,
            'work_date' => Carbon::today(),
            'start_time' => Carbon::now()->subHours(5),
            'end_time' => Carbon::now(),
            'status' => '退勤済',
        ]);

        $response = $this->get('/admin/attendance/list?date=' . Carbon::today()->toDateString());
        $response->assertStatus(200);
        $response->assertSee(Carbon::now()->format('H:i'));
    }
}
