<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠登録</title>
    <link rel="stylesheet" href="{{ asset('css/attendance/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
</head>
<body>
    <header class="header">
        <img src="{{ asset('image/logo.svg') }}" alt="COACHTECHロゴ" class="logo">
        <nav class="nav">
            <a href="/attendance">勤怠</a>
            <a href="/attendance/list">勤怠一覧</a>
            <a href="/stamp_correction_request/list">申請</a>
            <form action="/logout" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="nav-link">ログアウト</button>
            </form>
        </nav>
    </header>

    <main class="main">
        <div class="status">
            @switch(auth()->user()->status)
                @case('出勤中')
                    出勤中
                    @break
                @case('休憩中')
                    休憩中
                    @break
                @case('退勤済')
                    退勤済
                    @break
                @default
                    勤務外
            @endswitch
        </div>

        @php
            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
            $now = \Carbon\Carbon::now();
        @endphp

        <p class="date">{{ $now->format('Y年n月j日') }}（{{ $weekdays[$now->dayOfWeek] }}）</p>
        <p class="time">{{ $now->format('H:i') }}</p>

        @switch(auth()->user()->status)
            @case('出勤中')
                <div class="attendance-buttons">
                    <form action="/attendance/leave" method="POST">
                        @csrf
                        <button type="submit" class="btn black">退勤</button>
                    </form>
                    <form action="/attendance/break" method="POST">
                        @csrf
                        <button type="submit" class="btn white">休憩入</button>
                    </form>
                </div>
                @break

            @case('休憩中')
                <form action="/attendance/return" method="POST">
                    @csrf
                    <button type="submit" class="btn white">休憩戻</button>
                </form>
                @break

            @case('退勤済')
                <p class="thanks-message">お疲れ様でした。</p>
                @break

            @default
                <form action="/attendance" method="POST">
                    @csrf
                    <button type="submit" class="btn">出勤</button>
                </form>
        @endswitch
    </main>
</body>
</html>
