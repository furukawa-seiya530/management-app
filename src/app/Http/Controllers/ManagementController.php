<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AttendanceUpdateRequest;
use Illuminate\Support\Facades\Response;

class ManagementController extends Controller
{
    public function showRegister(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    public function showLogin(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function handleLogin(LoginRequest $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials) && !Auth::user()->is_admin) {
            return redirect()->route('attendance.form');
        }

        return back()->withErrors(['email' => 'ログイン情報が登録されていません']);
    }

    public function showAttendanceForm(): \Illuminate\View\View
    {
        $user = auth()->user();
        $today = Carbon::today();

        $lastAttendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$lastAttendance && is_null($user->status)) {
            $user->status = '勤務外';
            $user->save();
        }

        return view('attendance.form', ['status' => $user->fresh()->status]);
    }

    public function startAttendance(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $today = Carbon::today();

        $existing = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->exists();

        if ($existing) {
            return redirect()->route('attendance.form')
                ->withErrors(['attendance' => '本日の出勤は既に記録されています。']);
        }

        $user->update(['status' => '出勤中']);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today,
            'start_time' => Carbon::now(),
            'status' => '出勤中',
        ]);

        return redirect()->route('attendance.form')->with('message', '出勤を開始しました');
    }

    public function takeBreak(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        if ($user->status === '出勤中') {
            $user->update(['status' => '休憩中']);

            $attendance = Attendance::where('user_id', $user->id)
                ->where('work_date', Carbon::today())
                ->first();

            if ($attendance) {
                $attendance->update([
                    'break_start' => Carbon::now(),
                    'status' => '休憩中',
                ]);
            }
        }

        return redirect()->route('attendance.form')->with('message', '休憩に入りました');
    }

    public function returnFromBreak(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        if ($user->status === '休憩中') {
            $user->update(['status' => '出勤中']);

            $attendance = Attendance::where('user_id', $user->id)
                ->where('work_date', Carbon::today())
                ->first();

            if ($attendance) {
                $attendance->update([
                    'break_end' => Carbon::now(),
                    'status' => '出勤中',
                ]);
            }
        }

        return redirect()->route('attendance.form')->with('message', '休憩から戻りました');
    }

    public function endAttendance(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        if ($user->status === '出勤中') {
            $user->update(['status' => '退勤済']);

            $attendance = Attendance::where('user_id', $user->id)
                ->where('work_date', Carbon::today())
                ->first();

            if ($attendance) {
                $attendance->update([
                    'end_time' => Carbon::now(),
                    'status' => '退勤済',
                ]);
            }
        }

        return redirect()->route('attendance.form')->with('message', '退勤しました');
    }

    public function showAttendanceList(Request $request): \Illuminate\View\View
    {
        $userId = auth()->id();
        $month = $request->query('month', now()->format('Y-m'));
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('work_date', [$start, $end])
            ->orderBy('work_date')
            ->get()
            ->map(fn ($attendance) => $this->calculateTotalTime($attendance));

        return view('attendance.index', compact('attendances', 'month'));
    }

    public function showAttendanceDetail(int $id): \Illuminate\View\View
    {
        $attendance = Attendance::findOrFail($id);
        return view('attendance.show', compact('attendance'));
    }

    public function submitCorrectionRequest(AttendanceUpdateRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        AttendanceCorrectionRequest::create([
            'attendance_id' => $id,
            'user_id' => auth()->id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
            'note' => $request->note,
        ]);

        return redirect()->route('attendance.detail', ['id' => $id])
            ->with('message', '修正申請を送信しました。承認をお待ちください。');
    }

    public function showUserCorrectionList(Request $request): \Illuminate\View\View
    {
        $status = $request->query('status', 'pending');

        $requests = AttendanceCorrectionRequest::where('user_id', auth()->id())
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('correction.index', compact('requests', 'status'));
    }

    public function showAdminLogin(): \Illuminate\View\View
    {
        return view('admin.login');
    }

    public function handleAdminLogin(LoginRequest $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials) && Auth::user()->is_admin) {
            return redirect()->route('admin.attendance.list');
        }

        return back()->withErrors(['email' => 'ログイン情報が登録されていません']);
    }

    public function showAdminAttendanceList(Request $request): \Illuminate\View\View
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $users = User::orderBy('id')->get();
        $records = Attendance::whereDate('work_date', $date)->get()->keyBy('user_id');

        $attendances = $users->map(function ($user) use ($records) {
            $record = $records->get($user->id);
            return (object) [
                'id' => $record ? $record->id : null,
                'name' => $user->name,
                'start_time' => $record ? $record->start_time : '',
                'end_time' => $record ? $record->end_time : '',
                'break_time' => $record ? $record->break_time : '',
                'total_time' => $record ? $record->total_time : '',
            ];
        });

        return view('admin.attendance.index', [
            'attendances' => $attendances,
            'currentDate' => $date,
        ]);
    }

    public function showAdminAttendanceDetail(int $id): \Illuminate\View\View
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        return view('admin.attendance.show', compact('attendance'));
    }

    public function showStaffList(): \Illuminate\View\View
    {
        $staffs = User::all();
        return view('admin.staff.index', compact('staffs'));
    }

    public function showStaffAttendance(Request $request, int $id): \Illuminate\View\View
    {
        $staff = User::findOrFail($id);
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $records = Attendance::where('user_id', $id)
            ->whereBetween('work_date', [$start, $end])
            ->orderBy('work_date')
            ->get()
            ->map(fn ($record) => $this->calculateTotalTime($record));

        return view('admin.staff.attendance', [
            'staff' => $staff,
            'records' => $records,
            'currentMonth' => Carbon::parse($month),
        ]);
    }

    public function exportStaffAttendance(Request $request, int $id): \Symfony\Component\HttpFoundation\Response
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $records = Attendance::where('user_id', $id)
            ->whereBetween('work_date', [$start, $end])
            ->orderBy('work_date')
            ->get()
            ->map(fn ($record) => $this->calculateTotalTime($record));

        $csv = "日付,出勤,退勤,休憩,合計\n";

        foreach ($records as $record) {
            $csv .= "{$record->work_date},{$record->start_time},{$record->end_time},{$record->break_time},{$record->total_time}\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=attendance_{$month}.csv",
        ]);
    }

    public function showAdminCorrectionList(Request $request): \Illuminate\View\View
    {
        $status = $request->query('status', 'pending');
        $requests = AttendanceCorrectionRequest::where('status', $status)
            ->with('attendance.user')
            ->get();

        return view('admin.correction.index', compact('requests', 'status'));
    }

    public function showApproveCorrectionRequest(AttendanceCorrectionRequest $attendance_correct_request): \Illuminate\View\View
    {
        $correction = AttendanceCorrectionRequest::with('attendance.user')->findOrFail($attendance_correct_request->id);

        return view('admin.correction.approve', [
            'correction' => $correction,
            'status' => $correction->status,
        ]);
    }

    public function showCorrectionRequestDetail(AttendanceCorrectionRequest $attendance_correct_request): \Illuminate\View\View
    {
        return view('admin.correction.approve', [
            'correction' => $attendance_correct_request
        ]);
    }

    public function approveCorrectionRequest(Request $request, AttendanceCorrectionRequest $attendance_correct_request): \Illuminate\Http\RedirectResponse
    {
        logger()->info('承認処理開始: ' . $attendance_correct_request->id);

        $attendance = Attendance::find($attendance_correct_request->attendance_id);
        if (!$attendance) {
            return back()->withErrors(['error' => '該当の勤怠データが存在しません']);
        }

        $attendance->start_time = $attendance_correct_request->start_time;
        $attendance->end_time = $attendance_correct_request->end_time;
        $attendance->break_start = $attendance_correct_request->break_start;
        $attendance->break_end = $attendance_correct_request->break_end;
        $attendance->break2_start = $attendance_correct_request->break2_start;
        $attendance->break2_end = $attendance_correct_request->break2_end;
        $attendance->note = $attendance_correct_request->note;
        $attendance->save();

        $attendance_correct_request->status = 'approved';
        $attendance_correct_request->save();

        return redirect()->route('correction.approve.show', [
            'attendance_correct_request' => $attendance_correct_request->id
        ])->with('message', '承認し、勤怠データも更新しました');
    }

    public function updateAttendance(AttendanceUpdateRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after_or_equal:start_time',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after_or_equal:break_start',
            'break2_start' => 'nullable|date_format:H:i',
            'break2_end' => 'nullable|date_format:H:i|after_or_equal:break2_start',
            'note' => 'nullable|string|max:255',
        ]);

        $attendance = Attendance::findOrFail($id);

        DB::beginTransaction();
        try {
            $attendance->start_time = $request->input('start_time');
            $attendance->end_time = $request->input('end_time');
            $attendance->break_start = $request->input('break_start');
            $attendance->break_end = $request->input('break_end');
            $attendance->break2_start = $request->input('break2_start');
            $attendance->break2_end = $request->input('break2_end');
            $attendance->note = $request->input('note');
            $attendance->save();

            DB::commit();
            return redirect()
                ->route('admin.attendance.show', ['id' => $id])
                ->with('message', '勤怠情報を更新しました。')
                ->with('updated', true);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => '更新処理に失敗しました。']);
        }
    }

    private function calculateTotalTime(Attendance $record): Attendance
    {
        if ($record->start_time && $record->end_time) {
            $work = Carbon::parse($record->end_time)->diffInMinutes(Carbon::parse($record->start_time));
            $break = 0;

            if ($record->break_start && $record->break_end) {
                $break += Carbon::parse($record->break_end)->diffInMinutes(Carbon::parse($record->break_start));
            }

            if ($record->break2_start && $record->break2_end) {
                $break += Carbon::parse($record->break2_end)->diffInMinutes(Carbon::parse($record->break2_start));
            }

            $record->total_time = sprintf('%02d:%02d', floor(($work - $break) / 60), ($work - $break) % 60);
            $record->break_time = sprintf('%02d:%02d', floor($break / 60), $break % 60);
        } else {
            $record->total_time = $record->break_time = '00:00';
        }

        return $record;
    }
}