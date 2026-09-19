<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function index()
    {
        $roomsPerFloorRaw = Setting::get(
            'roomsPerFloor',
            '[24,24,24]'
        );

       $roomsPerFloor = is_array($roomsPerFloorRaw)
    ? $roomsPerFloorRaw
    : json_decode((string) $roomsPerFloorRaw, true);

if (!is_array($roomsPerFloor)) {
    $roomsPerFloor = [];
}

        if (!is_array($roomsPerFloor)) {
            $roomsPerFloor = [];
        }

        $settings = [
            'shop_name' => Setting::get('shop_name', ''),
            'phone' => Setting::get('phone', ''),
            'promptpay' => Setting::get('promptpay', ''),
            'open_time' => Setting::get('open_time', ''),
            'close_time' => Setting::get('close_time', ''),
            'opening_cash' => Setting::get(
                'opening_cash',
                1000
            ),
            'shipping_fee' => Setting::get(
                'shipping_fee',
                0
            ),
            'floors' => (int) Setting::get('floors', 3),
            'roomsPerFloor' => $roomsPerFloor,
        ];

        return view(
            'staff.settings',
            compact('settings')
        );
    }

    public function update(Request $request)
    {
        $validated = $request->validate(
            [
                'shop_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'phone' => [
                    'required',
                    'numeric',
                    'digits_between:9,12',
                ],
                'promptpay' => [
                    'required',
                    'numeric',
                    'digits_between:9,12',
                ],
                'open_time' => [
                    'nullable',
                    'date_format:H:i',
                ],
                'close_time' => [
                    'nullable',
                    'date_format:H:i',
                ],
                'opening_cash' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],
                'shipping_fee' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:999999.99',
                ],
                'floors' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:100',
                ],
                'roomsPerFloor' => [
                    'required',
                    'string',
                    'regex:/^\s*\d+\s*(,\s*\d+\s*)*$/',
                ],
            ],
            [
                'floors.required' =>
                    'กรุณาระบุจำนวนชั้น',
                'floors.integer' =>
                    'จำนวนชั้นต้องเป็นตัวเลขจำนวนเต็ม',
                'floors.min' =>
                    'จำนวนชั้นต้องมีอย่างน้อย 1 ชั้น',
                'floors.max' =>
                    'จำนวนชั้นต้องไม่เกิน 100 ชั้น',
                'roomsPerFloor.required' =>
                    'กรุณาระบุจำนวนห้องในแต่ละชั้น',
                'roomsPerFloor.regex' =>
                    'รูปแบบจำนวนห้องไม่ถูกต้อง ตัวอย่างที่ถูกต้อง: 24,24,24',
            ]
        );

        $roomsPerFloor = collect(
            explode(',', $validated['roomsPerFloor'])
        )
            ->map(
                fn ($value) => (int) trim($value)
            )
            ->values()
            ->all();

        if (
            count($roomsPerFloor)
            !== (int) $validated['floors']
        ) {
            throw ValidationException::withMessages([
                'roomsPerFloor' =>
                    'จำนวนค่าห้องต้องตรงกับจำนวนชั้น เช่น 3 ชั้น ต้องกรอก 3 ค่า',
            ]);
        }

        foreach ($roomsPerFloor as $index => $rooms) {
            if ($rooms < 0 || $rooms > 10000) {
                throw ValidationException::withMessages([
                    'roomsPerFloor' =>
                        'จำนวนห้องของชั้นที่ '
                        . ($index + 1)
                        . ' ต้องอยู่ระหว่าง 0 ถึง 10,000 ห้อง',
                ]);
            }
        }

        Setting::set(
            'shop_name',
            $validated['shop_name']
        );

        Setting::set(
            'phone',
            $validated['phone']
        );

        Setting::set(
            'promptpay',
            $validated['promptpay']
        );

        Setting::set(
            'open_time',
            $validated['open_time'] ?? null
        );

        Setting::set(
            'close_time',
            $validated['close_time'] ?? null
        );

        Setting::set(
            'opening_cash',
            $validated['opening_cash'] ?? 0
        );

        Setting::set(
            'shipping_fee',
            number_format(
                (float) $validated['shipping_fee'],
                2,
                '.',
                ''
            )
        );

        Setting::set(
            'floors',
            (string) $validated['floors']
        );

        Setting::set(
            'roomsPerFloor',
            json_encode(
                $roomsPerFloor,
                JSON_UNESCAPED_UNICODE
            )
        );

        cache()->forget('shop_settings');
        cache()->forget('online_shop_settings');

        return back()->with(
            'success',
            'บันทึกการตั้งค่าสำเร็จ'
        );
    }
}