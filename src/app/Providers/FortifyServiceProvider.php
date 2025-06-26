<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\LogoutResponse as CustomLogoutResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // カスタムログアウトレスポンス
        $this->app->singleton(LogoutResponse::class, CustomLogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 会員登録画面の表示（Fortifyが使用）
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // ログイン画面の表示（adminとuserで切り分け）
        Fortify::loginView(function () {
            return request()->is('admin/*')
                ? view('admin.login')  // 管理者用Blade
                : view('auth.login');  // 一般ユーザー用Blade
        });

        // ログイン時の独自バリデーション + 認証処理
        Fortify::authenticateUsing(function (Request $request) {
            Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required', 'min:8'],
            ], [
                'email.required' => 'メールアドレスを入力してください',
                'email.email' => 'メールアドレスはメール形式で入力してください',
                'password.required' => 'パスワードを入力してください',
                'password.min' => 'パスワードは8文字以上で入力してください',
            ])->validate();

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // 管理者URLの場合、is_admin チェック
                if (request()->is('admin/*') && !$user->is_admin) {
                    return null; // 管理者じゃないのに /admin/login へ来た場合は失敗
                }

                return $user;
            }

            return null;
        });

        // ログイン試行のレート制限
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });

        // 登録処理の登録（既定）
        Fortify::createUsersUsing(CreateNewUser::class);
    }
}
