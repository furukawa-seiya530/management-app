<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠一覧</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/attendance/index.css') }}">
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
        <h1 class="title">勤怠一覧</h1>

        @php
            use Carbon\Carbon;

            $current = Carbon::parse($month);
            $start = $current->copy()->startOfMonth();
            $end = $current->copy()->endOfMonth();
            $prev = $current->copy()->subMonth()->format('Y-m');
            $next = $current->copy()->addMonth()->format('Y-m');
            $attendanceMap = $attendances->keyBy(fn($a) => Carbon::parse($a->work_date)->format('Y-m-d'));
            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        @endphp

        <div class="month-selector">
            <a href="?month={{ $prev }}" class="month-btn">← 前月</a>
            <span class="current-month">{{ $current->format('Y年n月') }}</span>
            <a href="?month={{ $next }}" class="month-btn">翌月 →</a>
        </div>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @for ($date = $start->copy(); $date->lte($end); $date->addDay())
                    @php
                        $record = $attendanceMap->get($date->format('Y-m-d'));
                        $week = $weekdays[$date->dayOfWeek];
                    @endphp
                    <tr>
                        <td>{{ $date->format('m/d') }}（{{ $week }}）</td>
                        <td>{{ optional($record)->start_time ? Carbon::parse($record->start_time)->format('H:i') : '' }}</td>
                        <td>{{ optional($record)->end_time ? Carbon::parse($record->end_time)->format('H:i') : '' }}</td>
                        <td>{{ optional($record)->break_time }}</td>
                        <td>{{ optional($record)->total_time }}</td>
                        <td>
                            @if ($record)
                                <a href="{{ route('attendance.detail', ['id' => $record->id]) }}" class="btn-detail">詳細</a>
                            @endif
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </main>
</body>
</html>
