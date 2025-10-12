<?php

namespace App\Http\Controllers;
use App\Models\Setting;
use App\Models\User; // หรือชื่อที่คุณใช้
use Illuminate\Http\Request;

class MemberController extends Controller
{

  public function index()
{
    $members = \App\Models\User::where('role', 'member')->get()->keyBy('room_number');

    // หาชั้นสูงสุดจาก room_number เช่น 801 => ชั้น 8
    $maxFloor = $members->map(function ($user) {
        return intval(substr($user->room_number, 0, 1));
    })->max();

    // หาจำนวนห้องสูงสุดต่อชั้น (เช่นมีถึง 25 ห้อง)
    $maxRoom = $members->map(function ($user) {
        return intval(substr($user->room_number, 1));
    })->max();

    return view('members.index', [
        'members' => $members,
        'totalFloors' => $maxFloor,
        'roomsPerFloor' => $maxRoom,
    ]);
}


public function setFloors(Request $request)
{
    $request->validate([
        'floors' => 'required|integer|min:1',
        'roomsPerFloor' => 'required|array',
        'roomsPerFloor.*' => 'required|integer|min:1'
    ]);

    Setting::set('floors', $request->floors);
    Setting::set('roomsPerFloor', $request->roomsPerFloor);

    return response()->json(['success' => true]);
}



public function view()
{
    $members = User::where('role', 'member')->get()->keyBy('room_number');

    return view('members.index', compact('members'));
}

    public function create()
    {
        return view('members.create-member');
    }

    public function store(Request $request)
{

    $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email',
    'role' => 'required|string',
    'room_number' => 'nullable|unique:users,room_number',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    'password' => [
        'required',
        'string',
        'min:8',
        'confirmed',
        'regex:/[A-Z]/',       // ต้องมีตัวพิมพ์ใหญ่ 1 ตัว
        'regex:/[@$!%*?&-]/',   // ต้องมีตัวอักษรพิเศษ 1 ตัว
    ],
], [
    'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
    'room_number.unique' => 'หมายเลขห้องนี้ถูกใช้งานแล้ว',
    'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
    'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัว',
    'password.regex' => 'รหัสผ่านต้องมีตัวพิมพ์ใหญ่ 1 ตัวและอักขระพิเศษอย่างน้อย 1 ตัว เช่น @#$%',
]);


    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'room_number' => $request->room_number,
        'password' => bcrypt($request->password),
        'image' => $request->image ?? null,
    ]);

    return redirect()->route('members.index')->with('success', 'เพิ่มสมาชิกเรียบร้อยแล้ว!');
}

    

    public function show(User $member)
    {
        return view('members.show', compact('member'));
    }

    public function edit(User $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, User $member)
{
    // Validate the incoming request
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $member->id, // ตรวจสอบว่าอีเมลไม่ซ้ำ
        'password' => [
        'required',
        'string',
        'min:8',
        'regex:/[A-Z]/',       // ต้องมีตัวพิมพ์ใหญ่ 1 ตัว
        'regex:/[@$!%*?&-]/',   // ต้องมีตัวอักษรพิเศษ 1 ตัว
    ],
        'room_number' => 'required|string|max:255|unique:users,room_number,' . $member->id, // ตรวจสอบว่าเลขห้องไม่ซ้ำ
    ], [
        'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
        'room_number.unique' => 'หมายเลขห้องนี้ถูกใช้งานแล้ว',
        'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัว',
        'password.regex' => 'รหัสผ่านต้องมีตัวพิมพ์ใหญ่ 1 ตัวและอักขระพิเศษอย่างน้อย 1 ตัว เช่น @#$%',
    ]);

    // อัปเดตข้อมูลสมาชิก
    $member->name = $request->name;
    $member->email = $request->email;
    $member->room_number = $request->room_number;

    // อัปเดตรหัสผ่านถ้ามีการเปลี่ยน
    if ($request->password) {
        $member->password = bcrypt($request->password);
    }

    $member->save();

    return redirect()->route('members.show', $member->id)->with('success', 'ข้อมูลสมาชิกถูกอัปเดตเรียบร้อย');
}

    

    public function destroy(User $member)
    {
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member deleted successfully!');
    }
}

