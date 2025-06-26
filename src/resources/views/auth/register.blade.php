<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
</head>
<body>
    <header class="header">
        <img src="{{ asset('image/logo.svg') }}" alt="COACHTECHロゴ" class="logo">
    </header>

    <main class="main">
        <h1 class="title">会員登録</h1>

        <form action="/register" method="POST" class="register-form" novalidate>
            @csrf

            <label for="name">名前</label>
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
            <input type="text" id="name" name="name" value="{{ old('name') }}">

            <label for="email">メールアドレス</label>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
            <input type="email" id="email" name="email" value="{{ old('email') }}">

            <label for="password">パスワード</label>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
            <input type="password" id="password" name="password">

            <label for="password_confirmation">パスワード確認</label>
            @error('password_confirmation')
                <div class="error">{{ $message }}</div>
            @enderror
            <input type="password" id="password_confirmation" name="password_confirmation">

            <button type="submit">登録する</button>
        </form>

        <p class="login-link">
            <a href="/login">ログインはこちら</a>
        </p>
    </main>
</body>
</html>
