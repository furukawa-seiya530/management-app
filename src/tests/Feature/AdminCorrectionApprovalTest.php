<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use App\Http\Middleware\IsAdmin;

class AdminCorrectionApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin'),
            'is_admin' => true,
        ]);
        $this->admin = User::find($this->admin->id);

        $this->user = User::create([
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('user'),
        ]);

        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);
    }

    /** @test */
    public function 承認待ちの修正申請一覧が表示される()
    {
        AttendanceCorrectionRequest::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
            'status' => 'pending',
        ]);

        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get('/admin/stamp_correction_request/list?status=pending');

        $response->assertStatus(200);
        $response->assertSee('10:00');
        $response->assertSee('pending');
    }

    /** @test */
    public function 承認済みの修正申請一覧が表示される()
    {
        AttendanceCorrectionRequest::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'start_time' => '08:30:00',
            'end_time' => '17:30:00',
            'status' => 'approved',
        ]);

        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get('/admin/stamp_correction_request/list?status=approved');

        $response->assertStatus(200);
        $response->assertSee('08:30');
        $response->assertSee('approved');
    }

    /** @test */
    public function 修正申請の詳細が正しく表示される()
    {
        $request = AttendanceCorrectionRequest::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'start_time' => '10:30:00',
            'end_time' => '19:30:00',
            'status' => 'pending',
        ]);

        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->get("/stamp_correction_request/approve/{$request->id}");

        $response->assertStatus(200);
        $response->assertSee('10:30');
        $response->assertSee('19:30');
    }

    /** @test */
    public function 修正申請の承認処理が実行され勤怠が更新される()
    {
        $request = AttendanceCorrectionRequest::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'status' => 'pending',
        ]);

        $response = $this
            ->withoutMiddleware(IsAdmin::class)
            ->actingAs($this->admin)
            ->post("/stamp_correction_request/approve/{$request->id}");

        // ✅ 修正後のリダイレクト先（詳細ページへ戻る仕様）
        $response->assertRedirect("/stamp_correction_request/approve/{$request->id}");

        $this->assertDatabaseHas('attendances', [
            'id' => $this->attendance->id,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        $this->assertDatabaseHas('attendance_correction_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);
    }
}
