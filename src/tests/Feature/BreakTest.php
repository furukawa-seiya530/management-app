<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_break()
    {
        $user = User::factory()->create(['status' => '出勤中']);
        $this->actingAs($user)->post('/attendance');

        $response = $this->post('/attendance/break');
        $response->assertRedirect('/attendance');

        $this->assertEquals('休憩中', $user->fresh()->status);

        $attendance = Attendance::where('user_id', $user->id)->first();
        $this->assertNotNull($attendance->break_start);
    }

    public function test_user_can_take_break_multiple_times()
    {
        $user = User::factory()->create(['status' => '出勤中']);
        $this->actingAs($user)->post('/attendance');

        $this->post('/attendance/break');
        $this->post('/attendance/return');
        $this->post('/attendance/break');
        $this->post('/attendance/return');

        $attendance = Attendance::where('user_id', $user->id)->first();
        $this->assertNotNull($attendance->break_start);
        $this->assertNotNull($attendance->break_end);
    }

    public function test_user_can_return_from_break()
    {
        $user = User::factory()->create(['status' => '出勤中']);
        $this->actingAs($user)->post('/attendance');
        $this->post('/attendance/break');

        $response = $this->post('/attendance/return');
        $response->assertRedirect('/attendance');

        $this->assertEquals('出勤中', $user->fresh()->status);

        $attendance = Attendance::where('user_id', $user->id)->first();
        $this->assertNotNull($attendance->break_end);
    }

    public function test_user_can_return_from_break_multiple_times()
    {
        $user = User::factory()->create(['status' => '出勤中']);
        $this->actingAs($user)->post('/attendance');

        $this->post('/attendance/break');
        $this->post('/attendance/return');
        $this->post('/attendance/break');
        $this->post('/attendance/return');

        $this->assertEquals('出勤中', $user->fresh()->status);
    }

    public function test_break_time_displayed_on_attendance_list()
    {
        $user = User::factory()->create(['status' => '勤務外']);
        $this->actingAs($user);

        // 時間を固定して作成（09:00〜13:00、休憩10:00〜11:00）
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::today(),
            'start_time' => Carbon::createFromTime(9, 0),
            'break_start' => Carbon::createFromTime(10, 0),
            'break_end' => Carbon::createFromTime(11, 0),
            'end_time' => Carbon::createFromTime(13, 0),
        ]);

        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee('01:00'); // 休憩時間
        $response->assertSee('03:00'); // 合計勤務時間（13:00-09:00 - 1時間休憩）
    }
}
