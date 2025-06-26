<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者 - 勤怠一覧</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/attendance/index.css') }}">
</head>
<body>
    <header class="header">
        <img src="{{ asset('image/logo.svg') }}" alt="COACHTECHロゴ" class="logo">
        <nav class="nav">
            <a href="/admin/attendance/list">勤怠一覧</a>
            <a href="/admin/staff/list">スタッフ一覧</a>
            <a href="/admin/stamp_correction_request/list">申請一覧</a>
            <form action="/logout" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="nav-link">ログアウト</button>
            </form>
        </nav>
    </header>

    <main class="main">
        @php
            use Carbon\Carbon;
            $date = Carbon::parse(request('date', now()));
            $weekdayLabel = ['日', '月', '火', '水', '木', '金', '土'][$date->dayOfWeek];
        @endphp

        <h1 class="title">{{ $date->format('Y年n月j日') }}（{{ $weekdayLabel }}）の勤怠</h1>

        <div class="date-selector">
            <a href="?date={{ $date->copy()->subDay()->format('Y-m-d') }}" class="prev-btn">← 前日</a>
            <span class="current-date">📅 {{ $date->format('Y/m/d') }}（{{ $weekdayLabel }}）</span>
            <a href="?date={{ $date->copy()->addDay()->format('Y-m-d') }}" class="next-btn">翌日 →</a>
        </div>

        <div class="attendance-table">
            <table>
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->name }}</td>
                            <td>{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}</td>
                            <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>
                            <td>{{ $attendance->break_time ?? '' }}</td>
                            <td>{{ $attendance->total_time ?? '' }}</td>
                            <td>
                                @if ($attendance->id)
                                    <a href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}">詳細</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

