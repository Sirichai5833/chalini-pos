<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout()
{
    $member = Auth::user(); // หรือดึงจากฐานข้อมูลแบบอื่น

    return view('online.checkout', compact('member'));
}
    
    public function placeOrder(Request $request) {
        // จำลองการสั่งซื้อ
        return redirect()->route('online.track')->with('success', 'ทำรายการสำเร็จ!');
    }
    
    public function trackPage() {
        return view('online.track');
    }
    
    public function track(Request $request) {
        $orderId = $request->input('order_id');
        // จำลองข้อมูลการติดตาม
        return view('online.track', ['orderId' => $orderId, 'status' => 'กำลังจัดส่ง']);
    }
    
}
