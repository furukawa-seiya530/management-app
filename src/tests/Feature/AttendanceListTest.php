<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_list_shows_user_records()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::today(),
            'start_time' => '16:04:42',
            'break_start' => '18:04:42',
            'break_end' => '19:04:42',
            'end_time' => '21:04:42',
        ]);

        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee('16:04');
        $response->assertSee('21:04');
        $response->assertSee('01:00');
        $response->assertSee('04:00');
    }

    public function test_attendance_list_shows_current_month_by_default()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee(now()->format('Y年n月'));
    }

    public function test_attendance_list_shows_previous_month()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $previousMonth = now()->subMonth()->format('Y-m');
        $response = $this->get('/attendance/list?month=' . $previousMonth);
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($previousMonth)->format('Y年n月'));
    }

    public function test_attendance_list_shows_next_month()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test4@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $nextMonth = now()->addMonth()->format('Y-m');
        $response = $this->get('/attendance/list?month=' . $nextMonth);
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($nextMonth)->format('Y年n月'));
    }

    public function test_attendance_detail_button_redirects_correctly()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test5@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::today(),
        ]);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');
    }
}
