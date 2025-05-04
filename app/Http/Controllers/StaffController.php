<?php

namespace App\Http\Controllers;

use App\Models\User; // เพิ่มบรรทัดนี้
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; // เพิ่มบรรทัดนี้

class StaffController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string',
            'image' => 'nullable|image|max:2048' // รองรับไฟล์ภาพ
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('staff_images', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'image' => $imagePath,
        ]);

        return redirect()->route('staff.create')->with('success', 'เพิ่มพนักงานเรียบร้อยแล้ว');
    }

    public function create()
{
    return view('staff.create-staff'); // หรือเปลี่ยนชื่อ view ตามที่คุณตั้งจริง
}
public function index()
{
    $staff = \App\Models\User::where('role', 'staff')->get(); // หรือเอาทุก role ถ้าต้องการ
    return view('staff.index', compact('staff'));
}

}
