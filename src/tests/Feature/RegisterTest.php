<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が未入力だとバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function メールアドレスが未入力だとバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'name' => 'ユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function パスワードが8文字未満だとバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'name' => 'ユーザー',
            'email' => 'user@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワード確認が一致しないとバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'name' => 'ユーザー',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワードが未入力だとバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'name' => 'ユーザー',
            'email' => 'user@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function 入力が正しければユーザーが登録される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/attendance'); // 登録後の遷移先
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}
