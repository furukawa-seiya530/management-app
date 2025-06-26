<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠詳細（管理者）</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/attendance/show.css') }}">
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
    <h1 class="title">勤怠詳細（管理者）</h1>

    <form action="{{ route('attendance.update', ['id' => $attendance->id]) }}" method="POST">
        @csrf
        <div class="card">
            <table>
                <tr>
                    <th>名前</th>
                    <td colspan="3" class="name-cell">{{ $attendance->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td colspan="3">
                        <span class="date-year">{{ $attendance->work_date->format('Y年') }}</span>
                        <span>{{ $attendance->work_date->format('n月j日') }}</span>
                    </td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        @error('start_time') <p class="error">{{ $message }}</p> @enderror
                        <input type="time" name="start_time" value="{{ old('start_time', $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '') }}">
                    </td>
                    <td class="tilde">〜</td>
                    <td>
                        @error('end_time') <p class="error">{{ $message }}</p> @enderror
                        <input type="time" name="end_time" value="{{ old('end_time', $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '') }}">
                    </td>
                </tr>

                <tr>
                    <th>休憩1</th>
                    <td>
                        @error('break_start') <p class="error">{{ $message }}</p> @enderror
                        <input type="time" name="break_start" value="{{ old('break_start', $attendance->break_start ? \Carbon\Carbon::parse($attendance->break_start)->format('H:i') : '') }}">
                    </td>
                    <td class="tilde">〜</td>
                    <td>
                        @error('break_end') <p class="error">{{ $message }}</p> @enderror
                        <input type="time" name="break_end" value="{{ old('break_end', $attendance->break_end ? \Carbon\Carbon::parse($attendance->break_end)->format('H:i') : '') }}">
                    </td>
                </tr>

                <tr>
                    <th>休憩2</th>
                    <td>
                        @error('break2_start') <p class="error">{{ $message }}</p> @enderror
                        <input type="time" name="break2_start" value="{{ old('break2_start', $attendance->break2_start ? \Carbon\Carbon::parse($attendance->break2_start)->format('H:i') : '') }}">
                    </td>
                    <td class="tilde">〜</td>
                    <td>
                        @error('break2_end') <p class="error">{{ $message }}</p> @enderror
                        <input type="time" name="break2_end" value="{{ old('break2_end', $attendance->break2_end ? \Carbon\Carbon::parse($attendance->break2_end)->format('H:i') : '') }}">
                    </td>
                </tr>

                <tr>
                    <th>備考</th>
                    <td colspan="3">
                        @error('note') <p class="error">{{ $message }}</p> @enderror
                        <textarea name="note">{{ old('note', $attendance->note) }}</textarea>
                    </td>
                </tr>
            </table>
        </div>

        <div class="button-container">
            @if (session('updated'))
                <div class="approved-message">修正済み</div>
            @else
                <button type="submit" class="submit-btn">修正</button>
            @endif
        </div>
    </form>
</main>
</body>
</html>
