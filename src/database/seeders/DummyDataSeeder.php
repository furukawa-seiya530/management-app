<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'name' => '管理者 太郎',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $user = User::create([
            'name' => '一般 花子',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        for ($i = 1; $i <= 2; $i++) {
            $date = Carbon::today()->subDays($i);

            Attendance::create([
                'user_id' => $user->id,
                'work_date' => $date->toDateString(),
                'start_time' => Carbon::createFromTime(9, 0),
                'break_start' => Carbon::createFromTime(12, 0),
                'break_end' => Carbon::createFromTime(13, 0),
                'end_time' => Carbon::createFromTime(18, 0),
                'status' => '退勤済',
                'note' => 'ダミーデータ'
            ]);
        }
    }
}
