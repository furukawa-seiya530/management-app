<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 出勤ボタンが正しく機能し、ステータスが「出勤中」に変わることを確認
     */
    public function test_user_can_clock_in_and_status_changes_to_working()
    {
        $user = User::factory()->create(['status' => '勤務外']);

        $response = $this->actingAs($user)->post('/attendance');

        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendances', [
            'user_id'   => $user->id,
            'work_date' => Carbon::today()->toDateString(),
        ]);

        $this->assertEquals('出勤中', $user->fresh()->status);
    }

    /**
     * 出勤は一日一回のみできることを確認
     */
    public function test_user_cannot_clock_in_twice_a_day()
    {
        $user = User::factory()->create(['status' => '勤務外']);

        $this->actingAs($user)->post('/attendance');

        $response = $this->actingAs($user)->post('/attendance');

        $response->assertSessionHasErrors('attendance');

        $this->assertCount(1, Attendance::where('user_id', $user->id)->get());
    }

    /**
     * 管理画面で出勤時刻が確認できることを確認
     */
    public function test_admin_can_see_user_clock_in_time()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id'    => $user->id,
            'work_date'  => Carbon::today()->toDateString(),
            'start_time' => '09:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('09:00');
    }
}
