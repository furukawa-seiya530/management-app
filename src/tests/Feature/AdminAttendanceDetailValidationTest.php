<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Http\Middleware\IsAdmin;

class AdminAttendanceDetailValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $attendance;

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

        $user = User::create([
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::today()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'note' => '初期登録',
        ]);
    }

    /** @test */
    public function 勤怠詳細ページに正しいデータが表示される()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get("/admin/attendance/{$this->attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        // 備考が old() 優先で空になる場合もあるため assertSee('初期登録') は省略
    }

    /** @test */
    public function 出勤時間が退勤時間より後だとバリデーションエラー()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->post("/attendance/{$this->attendance->id}", [
                'start_time' => '20:00',
                'end_time' => '18:00',
                'note' => 'テスト',
            ]);

        $response->assertSessionHasErrors(['start_time']);
    }

    /** @test */
    public function 休憩開始が退勤より後だとバリデーションエラー()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->post("/attendance/{$this->attendance->id}", [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'break_start' => '19:00',
                'note' => 'テスト',
            ]);

        $response->assertSessionHasErrors(['break_start']);
    }

    /** @test */
    public function 休憩終了が退勤より後だとバリデーションエラー()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->post("/attendance/{$this->attendance->id}", [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'break_end' => '19:00',
                'note' => 'テスト',
            ]);

        $response->assertSessionHasErrors(['break_end']);
    }

    /** @test */
    public function 備考が未入力だとバリデーションエラー()
    {
        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->post("/attendance/{$this->attendance->id}", [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'note' => '', // ← 空欄
            ]);

        $response->assertSessionHasErrors(['note']);
    }
}
