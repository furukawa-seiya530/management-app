<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>
    <header class="header">
        <img src="{{ asset('image/logo.svg') }}" alt="COACHTECHロゴ" class="logo">
    </header>

    <main class="main">
        <h1 class="title">ログイン</h1>

        <form action="/login" method="POST" class="login-form" novalidate>
            @csrf

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

            <button type="submit">ログインする</button>
        </form>

        <p class="register-link">
            <a href="/register">会員登録はこちら</a>
        </p>
    </main>
</body>
</html>

