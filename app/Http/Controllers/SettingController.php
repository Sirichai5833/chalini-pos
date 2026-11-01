<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    /**
     * แสดงหน้าตั้งค่าระบบ
     */
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
            $roomsPerFloor = [24, 24, 24];
        }

        $settings = [
            // ข้อมูลร้าน
            'shop_name' => Setting::get('shop_name', ''),
            'phone' => Setting::get('phone', ''),

            // การชำระเงิน
            'promptpay' => Setting::get('promptpay', ''),
            'opening_cash' => Setting::get('opening_cash', 1000),

            // การจัดส่ง
            'shipping_fee' => Setting::get('shipping_fee', 0),

            'shipping_free_enabled' => (bool) Setting::get(
                'shipping_free_enabled',
                0
            ),

            'shipping_free_minimum' => Setting::get(
                'shipping_free_minimum',
                300

                
            ),

            // การขายส่ง
'wholesale_minimum_amount' => Setting::get(
    'wholesale_minimum_amount',
    500
),

            // เวลาทำการ
            'open_time' => Setting::get('open_time', ''),
            'close_time' => Setting::get('close_time', ''),

            // อาคารและห้อง
            'floors' => (int) Setting::get('floors', 3),
            'roomsPerFloor' => $roomsPerFloor,
        ];

        return view(
            'staff.settings',
            compact('settings')
        );
    }


    /**
     * บันทึกการตั้งค่าระบบ
     */
    public function update(Request $request)
    {
        $validated = $request->validate(
            [
                // ข้อมูลร้าน
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

                // การชำระเงิน
                'promptpay' => [
                    'required',
                    'numeric',
                    'digits_between:9,12',
                ],

                'opening_cash' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                // ค่าจัดส่งปกติ
                'shipping_fee' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:999999.99',
                ],

                // โปรโมชั่นจัดส่งฟรี
                'shipping_free_enabled' => [
                    'required',
                    'in:0,1',
                ],

                'shipping_free_minimum' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:999999.99',
                ],

                // เวลาทำการ
                'open_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'close_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                // อาคารและห้อง
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
                // การขายส่ง
'wholesale_minimum_amount' => [
    'required',
    'numeric',
    'min:0',
    'max:999999.99',
],
            ],
            [
                'shop_name.required' =>
                    'กรุณาระบุชื่อร้าน',

                'phone.required' =>
                    'กรุณาระบุเบอร์โทรศัพท์ร้าน',

                'phone.digits_between' =>
                    'เบอร์โทรศัพท์ต้องมี 9-12 หลัก',

                'promptpay.required' =>
                    'กรุณาระบุหมายเลขพร้อมเพย์',

                'shipping_fee.required' =>
                    'กรุณาระบุค่าจัดส่ง',

                'shipping_fee.numeric' =>
                    'ค่าจัดส่งต้องเป็นตัวเลข',

                'shipping_fee.min' =>
                    'ค่าจัดส่งต้องไม่ติดลบ',

                'shipping_free_enabled.required' =>
                    'กรุณาระบุสถานะโปรโมชั่นจัดส่งฟรี',

                'shipping_free_enabled.in' =>
                    'สถานะโปรโมชั่นไม่ถูกต้อง',

                'shipping_free_minimum.required' =>
                    'กรุณาระบุยอดซื้อขั้นต่ำสำหรับจัดส่งฟรี',

                'shipping_free_minimum.numeric' =>
                    'ยอดซื้อขั้นต่ำต้องเป็นตัวเลข',

                'shipping_free_minimum.min' =>
                    'ยอดซื้อขั้นต่ำต้องไม่ติดลบ',

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
                    'wholesale_minimum_amount.required' =>
    'กรุณาระบุยอดซื้อขั้นต่ำสำหรับขายส่ง',

'wholesale_minimum_amount.numeric' =>
    'ยอดซื้อขั้นต่ำสำหรับขายส่งต้องเป็นตัวเลข',

'wholesale_minimum_amount.min' =>
    'ยอดซื้อขั้นต่ำสำหรับขายส่งต้องไม่ติดลบ',

'wholesale_minimum_amount.max' =>
    'ยอดซื้อขั้นต่ำสำหรับขายส่งต้องไม่เกิน 999,999.99 บาท',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | ตรวจสอบจำนวนห้องแต่ละชั้น
        |--------------------------------------------------------------------------
        */

        $roomsPerFloor = collect(
            explode(',', $validated['roomsPerFloor'])
        )
            ->map(fn ($value) => (int) trim($value))
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


        /*
        |--------------------------------------------------------------------------
        | เตรียมข้อมูลโปรโมชั่นจัดส่งฟรี
        |--------------------------------------------------------------------------
        */

        $freeShippingEnabled =
            $validated['shipping_free_enabled'] === '1';

        $freeShippingMinimum = number_format(
            (float) $validated['shipping_free_minimum'],
            2,
            '.',
            ''
        );


        /*
        |--------------------------------------------------------------------------
        | บันทึกข้อมูลร้าน
        |--------------------------------------------------------------------------
        */

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
        /*
|--------------------------------------------------------------------------
| บันทึกยอดซื้อขั้นต่ำสำหรับขายส่ง
|--------------------------------------------------------------------------
*/

Setting::set(
    'wholesale_minimum_amount',
    number_format(
        (float) $validated['wholesale_minimum_amount'],
        2,
        '.',
        ''
    )
);


        /*
        |--------------------------------------------------------------------------
        | บันทึกการชำระเงิน
        |--------------------------------------------------------------------------
        */

        Setting::set(
            'opening_cash',
            $validated['opening_cash'] ?? 0
        );


        /*
        |--------------------------------------------------------------------------
        | บันทึกค่าจัดส่ง
        |--------------------------------------------------------------------------
        */

        Setting::set(
            'shipping_fee',
            number_format(
                (float) $validated['shipping_fee'],
                2,
                '.',
                ''
            )
        );


        /*
        |--------------------------------------------------------------------------
        | บันทึกโปรโมชั่นซื้อครบส่งฟรี
        |--------------------------------------------------------------------------
        */

        Setting::set(
            'shipping_free_enabled',
            $freeShippingEnabled ? '1' : '0'
        );

        Setting::set(
            'shipping_free_minimum',
            $freeShippingMinimum
        );


        /*
        |--------------------------------------------------------------------------
        | บันทึกเวลาทำการ
        |--------------------------------------------------------------------------
        */

        Setting::set(
            'open_time',
            $validated['open_time'] ?? null
        );

        Setting::set(
            'close_time',
            $validated['close_time'] ?? null
        );


        /*
        |--------------------------------------------------------------------------
        | บันทึกข้อมูลอาคารและห้อง
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | ล้าง Cache
        |--------------------------------------------------------------------------
        */

        cache()->forget('shop_settings');
        cache()->forget('online_shop_settings');

        return back()->with(
            'success',
            'บันทึกการตั้งค่าสำเร็จ'
        );
    }
}
