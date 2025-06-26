<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class AttendanceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_time'   => ['required', 'date_format:H:i'],
            'end_time'     => ['required', 'date_format:H:i'],
            'break_start'  => ['nullable', 'date_format:H:i'],
            'break_end'    => ['nullable', 'date_format:H:i'],
            'note'         => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'start_time.required'   => '出勤時間を入力してください',
            'start_time.date_format' => '出勤時間は「HH:MM」形式で入力してください',
            'end_time.required'     => '退勤時間を入力してください',
            'end_time.date_format'  => '退勤時間は「HH:MM」形式で入力してください',
            'break_start.date_format' => '休憩開始は「HH:MM」形式で入力してください',
            'break_end.date_format'   => '休憩終了は「HH:MM」形式で入力してください',
            'note.required'         => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input('start_time');
            $end = $this->input('end_time');
            $breakStart = $this->input('break_start');
            $breakEnd = $this->input('break_end');

            if ($start && $end && $start >= $end) {
                $validator->errors()->add('start_time', '出勤時間もしくは退勤時間が不適切な値です');
            }

            if ($start && $end) {
                if ($breakStart && ($breakStart < $start || $breakStart > $end)) {
                    $validator->errors()->add('break_start', '休憩時間が勤務時間外です');
                }
                if ($breakEnd && ($breakEnd < $start || $breakEnd > $end)) {
                    $validator->errors()->add('break_end', '休憩時間が勤務時間外です');
                }
            }
        });
    }
}
