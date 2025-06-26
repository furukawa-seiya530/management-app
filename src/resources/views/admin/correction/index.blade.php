<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修正申請一覧</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/correction/index.css') }}">
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
    <h1 class="title">修正申請一覧</h1>

    <div class="tab-buttons">
        <a href="{{ route('correction.admin.list', ['status' => 'pending']) }}"
           class="tab-btn {{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('correction.admin.list', ['status' => 'approved']) }}"
           class="tab-btn {{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>日付</th>
                    <th>氏名</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>休憩2</th>
                    <th>備考</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $request)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') }}</td>
                        <td>{{ $request->attendance->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->start_time)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->end_time)->format('H:i') }}</td>
                        <td>
                            {{ $request->break_start ? \Carbon\Carbon::parse($request->break_start)->format('H:i') : 'ー' }}～
                            {{ $request->break_end ? \Carbon\Carbon::parse($request->break_end)->format('H:i') : 'ー' }}
                        </td>
                        <td>
                            {{ $request->break2_start ? \Carbon\Carbon::parse($request->break2_start)->format('H:i') : 'ー' }}～
                            {{ $request->break2_end ? \Carbon\Carbon::parse($request->break2_end)->format('H:i') : 'ー' }}
                        </td>
                        <td>{{ $request->note ?? 'ー' }}</td>
                        <td>
                            <a href="{{ route('correction.approve.show', ['attendance_correct_request' => $request->id]) }}">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">申請はありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
