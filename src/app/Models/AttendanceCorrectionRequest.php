<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    use HasFactory;

    // 一括代入可能な属性
    protected $fillable = [
        'attendance_id',
        'user_id',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'break2_start',  // 追加済み：休憩2開始
        'break2_end',    // 追加済み：休憩2終了
        'note',
        'status',
    ];

    // 属性の型キャスト
    protected $casts = [
        'start_time'     => 'string',
        'end_time'       => 'string',
        'break_start'    => 'string',
        'break_end'      => 'string',
        'break2_start'   => 'string',
        'break2_end'     => 'string',
    ];

    // 勤怠レコードとのリレーション
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    // ユーザーとのリレーション（申請者）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ルートモデルバインディングで使用するキー名を明示
    public function getRouteKeyName()
    {
        return 'id';
    }
}
