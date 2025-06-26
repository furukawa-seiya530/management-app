<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>修正申請詳細</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/correction/approve.css') }}">
</head>
<body>
<header class="header">
    <img src="{{ asset('image/logo.svg') }}" alt="COACHTECHロゴ" class="logo">
    <nav class="nav">
        <a href="/admin/attendance/list">勤怠一覧</a>
        <a href="/admin/staff/list">スタッフ一覧</a>
        <a href="/admin/stamp_correction_request/list">申請一覧</a>
        <form method="POST" action="/logout" class="logout-form">
            @csrf
            <button type="submit" class="nav-link">ログアウト</button>
        </form>
    </nav>
</header>

<main class="main">
    <h1 class="title">修正申請詳細</h1>

    <div class="card">
        <table>
            <tr>
                <th class="left-label">名前</th>
                <td colspan="3" class="name-cell">{{ $correction->attendance->user->name }}</td>
            </tr>
            <tr>
                <th class="left-label">日付</th>
                <td colspan="3">
                    <span class="date-year">{{ \Carbon\Carbon::parse($correction->attendance->work_date)->format('Y年') }}</span>
                    <span>{{ \Carbon\Carbon::parse($correction->attendance->work_date)->format('n月j日') }}</span>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>{{ \Carbon\Carbon::parse($correction->start_time)->format('H:i') }}</td>
                <td class="tilde">〜</td>
                <td>{{ \Carbon\Carbon::parse($correction->end_time)->format('H:i') }}</td>
            </tr>
            <tr>
                <th>休憩1</th>
                <td>{{ $correction->break_start ? \Carbon\Carbon::parse($correction->break_start)->format('H:i') : 'ー' }}</td>
                <td class="tilde">〜</td>
                <td>{{ $correction->break_end ? \Carbon\Carbon::parse($correction->break_end)->format('H:i') : 'ー' }}</td>
            </tr>
            <tr>
                <th>休憩2</th>
                <td>{{ $correction->break2_start ? \Carbon\Carbon::parse($correction->break2_start)->format('H:i') : 'ー' }}</td>
                <td class="tilde">〜</td>
                <td>{{ $correction->break2_end ? \Carbon\Carbon::parse($correction->break2_end)->format('H:i') : 'ー' }}</td>
            </tr>
            <tr>
                <th>備考</th>
                <td colspan="3">{{ $correction->note ?? 'ー' }}</td>
            </tr>
        </table>
    </div>

    <div class="button-container">
        @if ($correction->status === 'approved')
            <div class="approved-message">承認済み</div>
        @else
            <form method="POST" action="{{ route('correction.approve', ['attendance_correct_request' => $correction->id]) }}">
                @csrf
                <button type="submit" class="submit-btn">承認</button>
            </form>
        @endif
    </div>
</main>
</body>
</html>
