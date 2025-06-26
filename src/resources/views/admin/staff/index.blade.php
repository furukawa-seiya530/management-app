<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スタッフ一覧</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/staff/staff.css') }}">
</head>
<body>
<header class="header">
    <img src="{{ asset('image/logo.svg') }}" alt="COACHTECHロゴ" class="logo">
    <nav class="nav">
        <a href="{{ url('/admin/attendance/list') }}">勤怠一覧</a>
        <a href="{{ url('/admin/staff/list') }}">スタッフ一覧</a>
        <a href="{{ url('/admin/stamp_correction_request/list') }}">申請一覧</a>
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="nav-link">ログアウト</button>
        </form>
    </nav>
</header>

<main class="main">
    <h1 class="title">スタッフ一覧</h1>

    <div class="staff-table">
        <table>
            <thead>
                <tr>
                    <th>氏名</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staffs as $staff)
                    <tr>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>
                            <a href="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
