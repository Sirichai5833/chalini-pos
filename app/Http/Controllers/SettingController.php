<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'shop_name' => Setting::get('shop_name', ''),
            'phone' => Setting::get('phone', ''),
            'promptpay' => Setting::get('promptpay', ''),
            'open_time' => Setting::get('open_time', ''),
            'close_time' => Setting::get('close_time', ''),
            'opening_cash' => Setting::get('opening_cash', 1000),
        ];

        return view('staff.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:9,12',
            'promptpay' => 'required|numeric|digits_between:9,12',
            'open_time' => 'nullable',
            'close_time' => 'nullable',
            'opening_cash' => 'nullable|numeric|min:0',
        ]);

        Setting::set('shop_name', $request->shop_name);
        Setting::set('phone', $request->phone);
        Setting::set('promptpay', $request->promptpay);
        Setting::set('open_time', $request->open_time);
        Setting::set('close_time', $request->close_time);
        Setting::set('opening_cash', $request->opening_cash);

        return back()->with('success', 'บันทึกการตั้งค่าสำเร็จ');
    }
}