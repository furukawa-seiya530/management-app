<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $staff->name }}さんの勤怠</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/staff/staff-attendance.css') }}">
</head>
<body>
<header class="header">
    <img src="{{ asset('image/logo.svg') }}" alt="COACHTECHロゴ" class="logo">
    <nav class="nav">
        <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
        <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
        <a href="{{ route('correction.admin.list') }}">申請一覧</a>
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">ログアウト</button>
        </form>
    </nav>
</header>

<main class="main">
    <h1 class="title">{{ $staff->name }}さんの勤怠</h1>

    <!-- 月変更機能 -->
    <div class="month-selector">
        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="month-btn">← 前月</a>
        <span class="current-month">📅 {{ $currentMonth->format('Y年m月') }}</span>
        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="month-btn">翌月 →</a>
    </div>

    <!-- 勤怠一覧テーブル -->
    <div class="attendance-table">
        <table>
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
    @foreach ($records as $record)
        <tr>
            <td>{{ \Carbon\Carbon::parse($record->work_date)->format('m/d(D)') }}</td>
            <td>{{ $record->start_time ? \Carbon\Carbon::parse($record->start_time)->format('H:i') : '' }}</td>
            <td>{{ $record->end_time ? \Carbon\Carbon::parse($record->end_time)->format('H:i') : '' }}</td>
            <td>{{ $record->break_time ?? '' }}</td>
            <td>{{ $record->total_time ?? '' }}</td>
            <td>
                @if ($record->id)
                    <a href="{{ route('admin.attendance.detail', ['id' => $record->id]) }}">詳細</a>
                @else
                    ー
                @endif
            </td>
        </tr>
    @endforeach
</tbody>

        </table>
    </div>

    <!-- CSV出力ボタン -->
    <div class="export-button">
        <form method="GET" action="{{ route('admin.staff.attendance.export', ['id' => $staff->id]) }}">
            <input type="hidden" name="month" value="{{ $currentMonth->format('Y-m') }}">
            <button type="submit" class="csv-btn">CSV出力</button>
        </form>
    </div>
</main>
</body>
</html>
