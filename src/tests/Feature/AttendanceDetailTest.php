<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_detail_shows_correct_user_name()
    {
        $user = User::create([
            'name' => '山田太郎',
            'email' => 'yamada@example.com',
            'password' => bcrypt('password'),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::today(),
        ]);

        $response = $this->actingAs($user)->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee('山田太郎');
    }

    public function test_attendance_detail_shows_selected_date()
    {
        $user = User::create([
            'name' => '佐藤花子',
            'email' => 'sato@example.com',
            'password' => bcrypt('password'),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2025-06-01'),
        ]);

        $response = $this->actingAs($user)->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee('2025年');
        $response->assertSee('6月 1日');
    }

    public function test_attendance_detail_shows_clock_times()
    {
        $user = User::create([
            'name' => '田中次郎',
            'email' => 'tanaka@example.com',
            'password' => bcrypt('password'),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::today(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
