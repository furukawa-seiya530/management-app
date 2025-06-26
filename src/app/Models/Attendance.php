<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // 更新可能なカラムを指定
    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'break_start',
        'break_end',
        'end_time',
        'status',
    ];

    // 日付として扱いたいカラム（Carbon インスタンス化）
    protected $dates = [
        'work_date',
        'start_time',
        'break_start',
        'break_end',
        'end_time',
        'created_at',
        'updated_at',
    ];

    // リレーション（Attendance は User に属する）
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
