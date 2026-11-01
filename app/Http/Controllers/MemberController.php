<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * แสดงรายการสมาชิก
     */
    public function index()
    {
        $members = User::where('role', 'member')
            ->get()
            ->keyBy('room_number');

        // หาชั้นสูงสุดจาก room_number เช่น 801 => ชั้น 8
        $maxFloor = $members
            ->map(function ($user) {
                return intval(
                    substr($user->room_number, 0, 1)
                );
            })
            ->max();

        // หาจำนวนห้องสูงสุดต่อชั้น
        $maxRoom = $members
            ->map(function ($user) {
                return intval(
                    substr($user->room_number, 1)
                );
            })
            ->max();

        return view('members.index', [

            'members' =>
                $members,

            'totalFloors' =>
                $maxFloor,

            'roomsPerFloor' =>
                $maxRoom,

        ]);
    }


    /**
     * ตั้งค่าจำนวนชั้นและห้อง
     */
    public function setFloors(Request $request)
    {
        $request->validate([

            'floors' =>
                'required|integer|min:1',

            'roomsPerFloor' =>
                'required|array',

            'roomsPerFloor.*' =>
                'required|integer|min:1',

        ]);


        Setting::set(
            'floors',
            $request->floors
        );

        Setting::set(
            'roomsPerFloor',
            $request->roomsPerFloor
        );


        return response()->json([

            'success' =>
                true,

        ]);
    }


    /**
     * แสดงหน้าสมาชิก
     */
    public function view()
    {
        $members = User::where(
            'role',
            'member'
        )
            ->get()
            ->keyBy('room_number');


        return view(
            'members.index',
            compact('members')
        );
    }


    /**
     * แสดงหน้าเพิ่มสมาชิก
     */
    public function create()
    {
        return view(
            'members.create-member'
        );
    }


    /**
     * บันทึกสมาชิกใหม่
     */
    public function store(Request $request)
{
    $validated = $request->validate([

        'first_name' => [
            'required',
            'string',
            'max:255',
        ],

        'last_name' => [
            'required',
            'string',
            'max:255',
        ],

        'phone' => [
            'required',
            'regex:/^0[0-9]{8,9}$/',
        ],

        'email' => [
            'nullable',
            'email',
            'unique:users,email',
        ],

        'role' => [
            'required',
            'string',
            'in:member',
        ],

        'room_number' => [
            'required',
            'unique:users,room_number',
        ],

        'image' => [
            'nullable',
            'image',
            'mimes:jpeg,png,jpg,gif,svg',
            'max:2048',
        ],

    ], [

        'first_name.required' =>
            'กรุณากรอกชื่อ',

        'last_name.required' =>
            'กรุณากรอกนามสกุล',

        'phone.required' =>
            'กรุณากรอกเบอร์โทรศัพท์',

        'phone.regex' =>
            'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง เช่น 0812345678',

        'email.unique' =>
            'อีเมลนี้ถูกใช้ไปแล้ว',

        'room_number.required' =>
            'กรุณาเลือกห้อง',

        'room_number.unique' =>
            'หมายเลขห้องนี้ถูกใช้งานแล้ว',

    ]);


    // สุ่มรหัสผ่านเริ่มต้น
    $randomPassword = Str::random(8);


    $user = User::create([

    'name' =>
        $validated['first_name'] . ' ' . $validated['last_name'],

        'first_name' =>
            $validated['first_name'],

        'last_name' =>
            $validated['last_name'],

        'phone' =>
            $validated['phone'],

        'email' =>
            $validated['email'],

        'role' =>
            'member',

        'room_number' =>
            $validated['room_number'],

        'password' =>
            Hash::make(
                $randomPassword
            ),

        'image' =>
            $request->image ?? null,

    ]);


    return redirect()
    ->route('members.create')
    ->with('success', 'เพิ่มสมาชิกเรียบร้อยแล้ว')
    ->with('generated_password', $randomPassword);
}


    /**
     * แสดงรายละเอียดสมาชิก
     */
    public function show(User $member)
    {
        return view(
            'members.show',
            compact('member')
        );
    }


    /**
     * แสดงหน้าแก้ไขสมาชิก
     */
    public function edit(User $member)
    {
        abort_unless(auth()->user()?->role === 'admin' || auth()->user()?->role === 'staff', 403);

        return view(
            'members.edit',
            compact('member')
        );
    }


    /**
     * อัปเดตข้อมูลสมาชิก
     */
    public function update(
        Request $request,
        User $member
    ) {
        abort_unless(auth()->user()?->role === 'admin' || auth()->user()?->role === 'staff', 403);

        $request->validate([

           'first_name' => [
    'required',
    'string',
    'max:255',
],

'last_name' => [
    'required',
    'string',
    'max:255',
],

'phone' => [
    'required',
    'regex:/^0[0-9]{8,9}$/',
],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $member->id,
            ],

            /*
            |--------------------------------------------------------------------------
            | รหัสผ่านไม่จำเป็นต้องกรอก
            |--------------------------------------------------------------------------
            | ถ้าไม่กรอก จะใช้รหัสผ่านเดิม
            | ถ้ากรอก จะต้องผ่านเงื่อนไขทั้งหมด
            |--------------------------------------------------------------------------
            */
            'password' => [
                'nullable',
                'string',
                'min:6',
                'regex:/[A-Z]/',
                'regex:/[@$!%*?&-]/',
            ],

            'room_number' => [
                'required',
                'string',
                'max:255',
                'unique:users,room_number,' . $member->id,
            ],

        ], [

           'first_name.required' =>
    'กรุณากรอกชื่อ',

'last_name.required' =>
    'กรุณากรอกนามสกุล',

'phone.required' =>
    'กรุณากรอกเบอร์โทรศัพท์',

'phone.regex' =>
    'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง เช่น 0812345678',

            'email.required' =>
                'กรุณากรอกอีเมล',

            'email.email' =>
                'กรุณากรอกอีเมลให้ถูกต้อง',

            'email.unique' =>
                'อีเมลนี้ถูกใช้ไปแล้ว',

            'room_number.required' =>
                'กรุณากรอกหมายเลขห้อง',

            'room_number.unique' =>
                'หมายเลขห้องนี้ถูกใช้งานแล้ว',

            'password.min' =>
                'รหัสผ่านต้องมีอย่างน้อย 6 ตัว',

            'password.regex' =>
                'รหัสผ่านต้องมีตัวพิมพ์ใหญ่ 1 ตัว และอักขระพิเศษอย่างน้อย 1 ตัว เช่น @#$%',

        ]);


        /*
        |--------------------------------------------------------------------------
        | อัปเดตข้อมูลทั่วไป
        |--------------------------------------------------------------------------
        */

        $member->first_name =
    $request->first_name;

$member->last_name =
    $request->last_name;

$member->phone =
    $request->phone;

        $member->email =
            $request->email;

        $member->room_number =
            $request->room_number;


        /*
        |--------------------------------------------------------------------------
        | เปลี่ยนรหัสผ่านเฉพาะเมื่อกรอกรหัสผ่านใหม่
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('password')
        ) {

            $member->password =
                Hash::make(
                    $request->password
                );

        }


        /*
        |--------------------------------------------------------------------------
        | บันทึกข้อมูล
        |--------------------------------------------------------------------------
        */

        $member->save();


        return redirect()
            ->route(
                'members.show',
                $member->id
            )
            ->with(
                'success',
                'ข้อมูลสมาชิกถูกอัปเดตเรียบร้อย'
            );
    }


    /**
     * ลบสมาชิก
     */
    public function destroy(User $member)
    {
        abort_unless(auth()->user()?->role === 'admin' || auth()->user()?->role === 'staff', 403);

        $member->delete();


        return redirect()
            ->route(
                'members.index'
            )
            ->with(
                'success',
                'ลบสมาชิกเรียบร้อยแล้ว'
            );
    }
}
