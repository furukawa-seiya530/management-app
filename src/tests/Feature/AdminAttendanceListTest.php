<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Http\Middleware\IsAdmin;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $today;
    protected $yesterday;
    protected $tomorrow;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ユーザー作成
        $this->admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => 1,
        ]);
        $this->admin = User::find($this->admin->id);

        // 一般ユーザー作成
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        // 日付定義
        $this->today = Carbon::today();
        $this->yesterday = $this->today->copy()->subDay();
        $this->tomorrow = $this->today->copy()->addDay();

        // 勤怠レコード作成
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $this->yesterday->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $this->today->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $this->tomorrow->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);
    }

    /** @test */
    public function 管理者は当日の全ユーザーの勤怠情報を確認できる()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee($this->today->format('Y年n月j日'));
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function 遷移時に現在の日付が表示される()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee($this->today->format('Y年n月j日'));
    }

    /** @test */
    public function 前日ボタンで前日の勤怠情報が表示される()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get('/admin/attendance/list?date=' . $this->yesterday->toDateString());

        $response->assertStatus(200);
        $response->assertSee($this->yesterday->format('Y年n月j日'));
    }

    /** @test */
    public function 翌日ボタンで翌日の勤怠情報が表示される()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get('/admin/attendance/list?date=' . $this->tomorrow->toDateString());

        $response->assertStatus(200);
        $response->assertSee($this->tomorrow->format('Y年n月j日'));
    }
}
