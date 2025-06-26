<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ステータスが「勤務外」の場合に正しく表示されるかを確認
     */
    public function test_status_display_as_off_duty()
    {
        // 勤務外ステータスのユーザー作成
        $user = User::factory()->create(['status' => '勤務外']);

        // 認証して勤怠打刻ページへアクセス
        $response = $this->actingAs($user)->get('/attendance');

        // ステータスが「勤務外」と表示されていることを確認
        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /**
     * ステータスが「出勤中」の場合に正しく表示されるかを確認
     */
    public function test_status_display_as_working()
    {
        $user = User::factory()->create(['status' => '出勤中']);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    /**
     * ステータスが「休憩中」の場合に正しく表示されるかを確認
     */
    public function test_status_display_as_on_break()
    {
        $user = User::factory()->create(['status' => '休憩中']);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    /**
     * ステータスが「退勤済」の場合に正しく表示されるかを確認
     */
    public function test_status_display_as_finished()
    {
        $user = User::factory()->create(['status' => '退勤済']);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
