<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Http\Middleware\IsAdmin;

class AdminUserAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => 1,
        ]);
        $this->admin = User::find($this->admin->id);

        $this->user = User::create([
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        // 勤怠データ：前月・今月・翌月
        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::now()->startOfMonth()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::now()->addMonth()->startOfMonth()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);
    }

    /** @test */
    public function 一般ユーザーの氏名とメールアドレスがスタッフ一覧に表示される()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get('/admin/staff/list');

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->email);
    }

    /** @test */
    public function ユーザーの今月の勤怠情報が表示される()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get("/admin/staff/{$this->user->id}/attendance");

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function 前月の勤怠情報が表示される()
    {
        $date = Carbon::now()->subMonth()->format('Y-m');
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get("/admin/staff/{$this->user->id}/attendance?month={$date}");

        $response->assertStatus(200);
        $response->assertSee('09:00');
    }

    /** @test */
    public function 翌月の勤怠情報が表示される()
    {
        $date = Carbon::now()->addMonth()->format('Y-m');
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get("/admin/staff/{$this->user->id}/attendance?month={$date}");

        $response->assertStatus(200);
        $response->assertSee('09:00');
    }

    /** @test */
    public function 詳細ボタンから勤怠詳細画面に遷移できる()
    {
        $attendance = Attendance::where('user_id', $this->user->id)->first();

        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get("/admin/attendance/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('出勤'); // 任意の項目キーワード
    }
}
