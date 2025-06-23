<?php

namespace App\Http\Controllers;

use App\Models\User; // หรือชื่อที่คุณใช้
use Illuminate\Http\Request;

class MemberController extends Controller
{

   public function index()
{
    // ดึงสมาชิก role = member แยกตาม room_number
    $members = User::where('role', 'member')->get()->keyBy('room_number');


    return view('members.index', compact('members', ));
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
        'password' => 'required|string|min:8|confirmed',
    ], [
        'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
        'room_number.unique' => 'หมายเลขห้องนี้ถูกใช้งานแล้ว',
        'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
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
        'password' => 'nullable|min:6|confirmed',
        'room_number' => 'required|string|max:255|unique:users,room_number,' . $member->id, // ตรวจสอบว่าเลขห้องไม่ซ้ำ
    ], [
        'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
        'room_number.unique' => 'หมายเลขห้องนี้ถูกใช้งานแล้ว',
        'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
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

