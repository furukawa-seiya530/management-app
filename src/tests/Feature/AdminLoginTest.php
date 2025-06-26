<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メールアドレスが未入力の場合、バリデーションエラーになる
     */
    public function test_admin_login_validation_error_when_email_is_empty()
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * パスワードが未入力の場合、バリデーションエラーになる
     */
    public function test_admin_login_validation_error_when_password_is_empty()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * 誤った管理者資格情報の場合、ログインに失敗する
     */
    public function test_admin_login_fails_with_incorrect_credentials()
    {
        // 管理者ユーザー作成
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('correctpassword'),
            'is_admin' => true,
        ]);

        // 誤った情報でログインを試みる
        $response = $this->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
