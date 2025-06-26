<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
</head>
<body>
    <header class="header">
        <img src="{{ asset('image/logo.svg') }}" alt="COACHTECHロゴ" class="logo">
    </header>

    <main class="main">
        <h1 class="title">管理者ログイン</h1>

        <form action="/admin/login" method="POST" class="admin-login-form">
            @csrf

            <label for="email">メールアドレス</label>
            @if ($errors->has('email'))
                <p class="error-message">{{ $errors->first('email') }}</p>
            @endif
            <input type="text" id="email" name="email" value="{{ old('email') }}">

            <label for="password">パスワード</label>
            @if ($errors->has('password'))
                <p class="error-message">{{ $errors->first('password') }}</p>
            @endif
            <input type="password" id="password" name="password">

            <button type="submit">管理者ログインする</button>
        </form>
    </main>
</body>
</html>
