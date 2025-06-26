<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>申請一覧</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/correction/index.css') }}">
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
        <h1 class="title">申請一覧</h1>

        <div class="tabs">
            <a href="?status=pending" class="{{ request('status', 'pending') === 'pending' ? 'active' : '' }}">承認待ち</a>
            <a href="?status=approved" class="{{ request('status') === 'approved' ? 'active' : '' }}">承認済み</a>
        </div>

        <table class="correction-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日</th>
                    <th>申請理由</th>
                    <th>申請日</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $request)
                    <tr>
                        <td>{{ $request->status === 'approved' ? '承認済み' : '承認待ち' }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') }}</td>
                        <td>{{ $request->note }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('attendance.detail', ['id' => $request->attendance_id]) }}" class="btn">詳細</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">申請が見つかりません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>

