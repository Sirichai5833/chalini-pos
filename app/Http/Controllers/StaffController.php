<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
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

        return redirect()->route('staff.index')->with('success', 'เพิ่มพนักงานเรียบร้อยแล้ว');
    }

    public function create()
{
    return view('staff.create-staff'); // หรือเปลี่ยนชื่อ view ตามที่คุณตั้งจริง
}
public function index(Request $request)
{
    $search = $request->get('search'); // รับค่าจากฟอร์มค้นหา
    $staff = \App\Models\User::where('role', 'staff')
                             ->when($search, function ($query, $search) {
                                 return $query->where('name', 'like', '%' . $search . '%'); // กรองตามชื่อ
                             })
                             ->get();

    return view('staff.index', compact('staff'));
}

public function edit(User $member)
{
    return view('staff.edit', compact('member'));
}

public function alledit($id)
{
    // ค้นหาข้อมูลผู้ใช้จากฐานข้อมูล
    $user = User::findOrFail($id);
    
    // ส่งข้อมูล $user ไปยังวิว
    return view('staff.alledit', compact('user'));
}


public function allupdate(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Validate ข้อมูลจากฟอร์ม
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'password' => 'nullable|string|min:6|confirmed',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'role' => 'required|string|in:admin,staff', // ตรวจสอบตำแหน่ง (แอดมินหรือพนักงาน)
    ]);

    // อัปเดตข้อมูลพื้นฐาน
    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->role = $validated['role']; // อัปเดตตำแหน่ง

    // ถ้ามีการเปลี่ยนรหัสผ่าน
    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    // ถ้ามีการอัปโหลดรูปใหม่
    if ($request->hasFile('image')) {
        // ลบรูปเก่า (ถ้ามี)
        if ($user->image && Storage::exists('public/' . $user->image)) {
            Storage::delete('public/' . $user->image);
        }

        // ตรวจสอบว่าไฟล์ที่อัปโหลดเป็นไฟล์ภาพจริง ๆ
        $file = $request->file('image');
        $imageInfo = getimagesize($file);
        if ($imageInfo === false) {
            return redirect()->back()->withErrors('ไฟล์ที่อัปโหลดไม่ใช่ไฟล์ภาพที่ถูกต้อง');
        }

        // อัปโหลดไฟล์ภาพใหม่
        $path = $file->store('profile_images', 'public');
        $user->image = $path;
    }

    // บันทึกการเปลี่ยนแปลง
    $user->save();

    // รีไดเร็กต์ไปที่หน้า staff.index
    return redirect()->route('staff.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว!');
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'password' => 'nullable|string|min:6|confirmed',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    // อัปเดตข้อมูลพื้นฐาน
    $user->name = $validated['name'];
    $user->email = $validated['email'];

    // ถ้ามีการเปลี่ยนรหัสผ่าน
    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    // ถ้ามีการอัปโหลดรูปใหม่
    if ($request->hasFile('image')) {
        // ลบรูปเก่า (ถ้ามี)
        if ($user->image && Storage::exists('public/' . $user->image)) {
            Storage::delete('public/' . $user->image);
        }

        // ตรวจสอบว่าไฟล์ที่อัปโหลดเป็นไฟล์ภาพจริง ๆ
        $file = $request->file('image');
        $imageInfo = getimagesize($file);
        if ($imageInfo === false) {
            return redirect()->back()->withErrors('ไฟล์ที่อัปโหลดไม่ใช่ไฟล์ภาพที่ถูกต้อง');
        }

        // อัปโหลดไฟล์ภาพใหม่
        $path = $file->store('profile_images', 'public');
        $user->image = $path;
    }

    // บันทึกการเปลี่ยนแปลง
    $user->save();

    // ตรวจสอบว่า user เป็น staff หรือไม่
    if ($user->role == 'staff') {
        // ถ้าใช่, เปลี่ยนเส้นทางไปที่หน้า sale.sale
        return redirect()->route('sale')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว!');
    } else {
        // ถ้าไม่ใช่ staff, กลับไปหน้า staff.index
        return redirect()->route('staff.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว!');
    }
}

public function destroy($id)
{
    $user = User::findOrFail($id);
    
    // ตรวจสอบก่อนว่าผู้ใช้งานที่กำลังจะลบมีข้อมูลที่เกี่ยวข้องกับภาพหรือไม่
    if ($user->image && Storage::exists('public/' . $user->image)) {
        Storage::delete('public/' . $user->image);
    }

    // ลบข้อมูลจากฐานข้อมูล
    $user->delete();

    return redirect()->route('staff.index')->with('success', 'ลบพนักงานเรียบร้อยแล้ว');
}

public function auditLogs(Request $request)
{
    // ดึงเฉพาะ user ที่เป็น member
    $memberIds = User::where('role', 'member')->pluck('id');

    // ดึงข้อมูล AuditLog จากทั้ง "updated" และ "created" events
    $query = AuditLog::orderBy('created_at', 'desc')
        ->where('auditable_type', 'App\\Models\\User')
        ->whereIn('auditable_id', $memberIds)  // ตรวจสอบว่า "คนที่ถูกแก้ไข" เป็นสมาชิก
        ->whereIn('event', ['updated', 'created']); // ดึงทั้งการสร้างและการอัปเดต

    if ($request->filled('date')) {
        $query->whereDate('created_at', $request->input('date'));
    }

    $audits = $query->get();

    return view('staff.audit_logs', compact('audits'));
}






}
