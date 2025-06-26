<?php

return [
    'required' => ':attribute を入力してください。',
    'email' => ':attribute は有効なメールアドレス形式で入力してください。',
    'min' => [
        'string' => ':attribute は :min 文字以上で入力してください。',
    ],
    'confirmed' => ':attribute と確認用が一致しません。',
    'max' => [
        'string' => ':attribute は :max 文字以内で入力してください。',
    ],
    'string' => ':attribute は文字列で入力してください。',
    'unique' => ':attribute は既に使用されています。',
    'regex' => ':attribute の形式が不正です。',


    'attributes' => [
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認用）',
        'name' => 'お名前',
        'note' => '備考',
        'start_time' => '出勤時間',
        'end_time' => '退勤時間',
        'break_start' => '休憩開始時間',
        'break_end' => '休憩終了時間',
    ],
];
