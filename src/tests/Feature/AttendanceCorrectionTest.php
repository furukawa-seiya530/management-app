<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '17:00',
        ]);
    }

    /** @test */
    public function validation_error_when_start_time_is_after_end_time()
    {
        $response = $this->actingAs($this->user)->post('/correction/request/' . $this->attendance->id, [
            'start_time' => '18:00',
            'end_time' => '17:00',
            'note' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['start_time']);
    }

    /** @test */
    public function validation_error_when_break_start_is_after_end_time()
    {
        $response = $this->actingAs($this->user)->post('/correction/request/' . $this->attendance->id, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'break_start' => '18:00',
            'note' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['break_start']);
    }

    /** @test */
    public function validation_error_when_break_end_is_after_end_time()
    {
        $response = $this->actingAs($this->user)->post('/correction/request/' . $this->attendance->id, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'break_end' => '18:00',
            'note' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['break_end']);
    }

    /** @test */
    public function validation_error_when_note_is_empty()
    {
        $response = $this->actingAs($this->user)->post('/correction/request/' . $this->attendance->id, [
            'start_time' => '08:00',
            'end_time' => '17:00',
        ]);

        $response->assertSessionHasErrors(['note']);
    }

    /** @test */
    public function successful_correction_request()
    {
        $response = $this->actingAs($this->user)->post('/correction/request/' . $this->attendance->id, [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'note' => '修正申請テスト',
        ]);

        $response->assertRedirect('/attendance/' . $this->attendance->id);

        $this->assertDatabaseHas('attendance_correction_requests', [
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'note' => '修正申請テスト',
        ]);
    }

    /** @test */
    public function pending_corrections_displayed_in_user_view()
    {
        AttendanceCorrectionRequest::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'note' => '保留中の申請',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get('/stamp_correction_request/list?status=pending');
        $response->assertStatus(200);
        $response->assertSee('保留中の申請');
    }

    /** @test */
    public function approved_corrections_displayed_in_user_view()
    {
        AttendanceCorrectionRequest::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'note' => '承認済みの申請',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->user)->get('/stamp_correction_request/list?status=approved');
        $response->assertStatus(200);
        $response->assertSee('承認済みの申請');
    }

    /** @test */
    public function correction_detail_link_navigates_to_detail_page()
    {
        $this->withoutMiddleware(\App\Http\Middleware\IsAdmin::class);

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $request = AttendanceCorrectionRequest::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'note' => '詳細遷移テスト',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get("/stamp_correction_request/approve/{$request->id}");

        $response->assertStatus(200);
        $response->assertSee('詳細遷移テスト');
    }
}
