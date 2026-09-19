<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User; // เพิ่มบรรทัดนี้
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; // เพิ่มบรรทัดนี้
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class StaffController extends Controller
{

public function store(Request $request)
{
    $validated = $request->validate([
        'first_name' => [
            'required',
            'string',
            'max:100',
        ],

        'last_name' => [
            'required',
            'string',
            'max:100',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email'),
        ],

        'phone' => [
            'required',
            'regex:/^0[0-9]{8,9}$/',
            Rule::unique('users', 'phone'),
        ],

        'address' => [
            'required',
            'string',
            'max:1000',
        ],

        'role' => [
            'required',
            'string',
            Rule::in($this->assignableRoles($request->user())),
        ],

        'image' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ],
    ], [
        'first_name.required' => 'กรุณากรอกชื่อ',
        'first_name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',

        'last_name.required' => 'กรุณากรอกนามสกุล',
        'last_name.max' => 'นามสกุลต้องไม่เกิน 100 ตัวอักษร',

        'email.required' => 'กรุณากรอกอีเมล',
        'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
        'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',

        'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
        'phone.regex' => 'เบอร์โทรศัพท์ต้องขึ้นต้นด้วย 0 และมี 9-10 หลัก',
        'phone.unique' => 'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว',

        'address.required' => 'กรุณากรอกที่อยู่',
        'address.max' => 'ที่อยู่ต้องไม่เกิน 1,000 ตัวอักษร',

        'role.required' => 'กรุณาเลือกตำแหน่ง',
        'role.in' => 'ตำแหน่งที่เลือกไม่ได้รับอนุญาต',

        'image.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพ',
        'image.mimes' => 'รูปภาพต้องเป็นไฟล์ JPG, JPEG, PNG หรือ WEBP',
        'image.max' => 'รูปภาพต้องมีขนาดไม่เกิน 2 MB',
    ]);

    // สร้างรหัสผ่านเริ่มต้นอัตโนมัติ
    $randomPassword = Str::password(
        length: 10,
        letters: true,
        numbers: true,
        symbols: true
    );

    // อัปโหลดรูปโปรไฟล์
    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request
            ->file('image')
            ->store('staff', 'public');
    }

    // รวมชื่อเต็มไว้รองรับโค้ดเดิมที่ยังเรียก $user->name
    $fullName = trim(
        $validated['first_name'] . ' ' . $validated['last_name']
    );

    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'name' => $fullName,
        'email' => $validated['email'],
        'phone' => $validated['phone'],
        'address' => $validated['address'],
        'role' => $validated['role'],
        'password' => Hash::make($randomPassword),
        'image' => $imagePath,
    ]);

    return redirect()
        ->back()
        ->with('success', 'เพิ่มพนักงานสำเร็จ')
        ->with('generated_password', $randomPassword);
}

    public function create()
{
    return view('staff.create-staff'); // หรือเปลี่ยนชื่อ view ตามที่คุณตั้งจริง
}
public function index(Request $request)
{
    $search = trim($request->input('search', ''));

    $staff = User::query()
        ->whereIn('role', ['admin', 'staff'])
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(12)
        ->withQueryString();

    return view('staff.index', compact('staff'));
}

public function edit(User $member)
{
    $this->authorizeSelfOrAdmin($member);

    return view('staff.edit', compact('member'));
}

public function alledit($id)
{
    abort_unless(auth()->user()?->role === 'admin', 403);

    // ค้นหาข้อมูลผู้ใช้จากฐานข้อมูล
    $user = User::findOrFail($id);
    
    // ส่งข้อมูล $user ไปยังวิว
    return view('staff.alledit', compact('user'));
}


public function allupdate(Request $request, $id)
{
    abort_unless($request->user()?->role === 'admin', 403);

    $user = User::findOrFail($id);

    $validated = $request->validate([
        'first_name' => [
            'required',
            'string',
            'max:100',
        ],

        'last_name' => [
            'required',
            'string',
            'max:100',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ],

        'phone' => [
            'required',
            'regex:/^0[0-9]{8,9}$/',
            Rule::unique('users', 'phone')->ignore($user->id),
        ],

        'address' => [
            'required',
            'string',
            'max:1000',
        ],

        'password' => [
            'nullable',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[\W_]/',
        ],

        'image' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ],

        'role' => [
            'required',
            'string',
            Rule::in(
                $this->assignableRoles($request->user())
            ),
        ],
    ], [
        'first_name.required' => 'กรุณากรอกชื่อ',
        'first_name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',

        'last_name.required' => 'กรุณากรอกนามสกุล',
        'last_name.max' => 'นามสกุลต้องไม่เกิน 100 ตัวอักษร',

        'email.required' => 'กรุณากรอกอีเมล',
        'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
        'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',

        'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
        'phone.regex' => 'เบอร์โทรศัพท์ต้องขึ้นต้นด้วย 0 และมี 9-10 หลัก',
        'phone.unique' => 'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว',

        'address.required' => 'กรุณากรอกที่อยู่',
        'address.max' => 'ที่อยู่ต้องไม่เกิน 1,000 ตัวอักษร',

        'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
        'password.confirmed' => 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน',
        'password.regex' => 'รหัสผ่านต้องมีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และอักขระพิเศษ',

        'image.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพ',
        'image.mimes' => 'รองรับเฉพาะไฟล์ JPG, JPEG, PNG และ WEBP',
        'image.max' => 'รูปภาพต้องมีขนาดไม่เกิน 2 MB',

        'role.required' => 'กรุณาเลือกตำแหน่ง',
        'role.in' => 'ตำแหน่งที่เลือกไม่ได้รับอนุญาต',
    ]);

    $fullName = trim(
        $validated['first_name'] . ' ' .
        $validated['last_name']
    );

    $updateData = [
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],

        // เก็บไว้เพื่อรองรับหน้าระบบเดิมที่ยังใช้ $user->name
        'name' => $fullName,

        'email' => $validated['email'],
        'phone' => $validated['phone'],
        'address' => $validated['address'],
        'role' => $validated['role'],
    ];

    // อัปเดตรหัสผ่านเฉพาะเมื่อผู้ใช้กรอกมา
    if (!empty($validated['password'])) {
        $updateData['password'] = Hash::make(
            $validated['password']
        );
    }

    // อัปโหลดรูปใหม่
    if ($request->hasFile('image')) {
        $file = $request->file('image');

        /*
         * Validation rule "image" ตรวจสอบไฟล์ภาพให้แล้ว
         * จึงไม่จำเป็นต้องเรียก getimagesize() ซ้ำ
         */

        $newImagePath = $file->store(
            'profile_images',
            'public'
        );

        /*
         * เก็บ path รูปเก่าไว้ก่อน
         * เพื่อให้ลบหลังจากอัปเดตฐานข้อมูลสำเร็จ
         */
        $oldImagePath = $user->image;

        $updateData['image'] = $newImagePath;
    }

    $user->update($updateData);

    // ลบรูปเก่าหลังจากอัปเดตข้อมูลสำเร็จ
    if (
        isset($oldImagePath) &&
        $oldImagePath &&
        Storage::disk('public')->exists($oldImagePath)
    ) {
        Storage::disk('public')->delete($oldImagePath);
    }

    return redirect()
        ->route('staff.index')
        ->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว');
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // อนุญาตเฉพาะเจ้าของบัญชีหรือแอดมิน
    $this->authorizeSelfOrAdmin($user);

    $validated = $request->validate([
        'first_name' => [
            'required',
            'string',
            'max:100',
        ],

        'last_name' => [
            'required',
            'string',
            'max:100',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ],

        'phone' => [
            'required',
            'regex:/^0[0-9]{8,9}$/',
            Rule::unique('users', 'phone')->ignore($user->id),
        ],

        'address' => [
            'required',
            'string',
            'max:1000',
        ],

        'password' => [
            'nullable',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[\W_]/',
        ],

        'image' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ],
    ], [
        'first_name.required' => 'กรุณากรอกชื่อ',
        'first_name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',

        'last_name.required' => 'กรุณากรอกนามสกุล',
        'last_name.max' => 'นามสกุลต้องไม่เกิน 100 ตัวอักษร',

        'email.required' => 'กรุณากรอกอีเมล',
        'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
        'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',

        'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
        'phone.regex' => 'เบอร์โทรศัพท์ต้องขึ้นต้นด้วย 0 และมี 9-10 หลัก',
        'phone.unique' => 'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว',

        'address.required' => 'กรุณากรอกที่อยู่',
        'address.max' => 'ที่อยู่ต้องไม่เกิน 1,000 ตัวอักษร',

        'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
        'password.confirmed' => 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน',
        'password.regex' => 'รหัสผ่านต้องมีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และอักขระพิเศษ',

        'image.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพ',
        'image.mimes' => 'รองรับเฉพาะไฟล์ JPG, JPEG, PNG และ WEBP',
        'image.max' => 'รูปภาพต้องมีขนาดไม่เกิน 2 MB',
    ]);

    $fullName = trim(
        $validated['first_name'] . ' ' .
        $validated['last_name']
    );

    $updateData = [
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],

        // เก็บ name เพื่อรองรับโค้ดเก่าที่ยังเรียก $user->name
        'name' => $fullName,

        'email' => $validated['email'],
        'phone' => $validated['phone'],
        'address' => $validated['address'],
    ];

    // เปลี่ยนรหัสผ่านเฉพาะเมื่อกรอกมา
    if (!empty($validated['password'])) {
        $updateData['password'] = Hash::make(
            $validated['password']
        );
    }

    $oldImagePath = null;

    // อัปโหลดรูปใหม่
    if ($request->hasFile('image')) {
        $oldImagePath = $user->image;

        $updateData['image'] = $request
            ->file('image')
            ->store('profile_images', 'public');
    }

    // อัปเดตฐานข้อมูล
    $user->update($updateData);

    // ลบรูปเก่าหลังจากบันทึกข้อมูลใหม่สำเร็จ
    if (
        $oldImagePath &&
        Storage::disk('public')->exists($oldImagePath)
    ) {
        Storage::disk('public')->delete($oldImagePath);
    }

    // พนักงานขายกลับไปหน้าขาย
    if ($user->role === 'staff') {
        return redirect()
            ->route('sale')
            ->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }

    // แอดมินกลับหน้ารายชื่อพนักงาน
    return redirect()
        ->route('staff.index')
        ->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
}

public function destroy($id)
{
    abort_unless(auth()->user()?->role === 'admin', 403);

    $user = User::findOrFail($id);

    // ป้องกันแอดมินลบบัญชีตัวเอง
    if ($user->id === auth()->id()) {
        return redirect()
            ->route('staff.index')
            ->with('error', 'ไม่สามารถลบบัญชีที่กำลังใช้งานอยู่ได้');
    }

    // ป้องกันการลบบัญชีประเภทอื่นที่ไม่ใช่พนักงานหรือแอดมิน
    if (!in_array($user->role, ['admin', 'staff'], true)) {
        abort(403, 'ไม่สามารถลบบัญชีประเภทนี้ได้');
    }

    // เก็บ path ไว้ก่อนลบข้อมูล
    $imagePath = $user->image;

    // ลบข้อมูลพนักงาน
    $user->delete();

    // ลบรูปหลังจากลบข้อมูลสำเร็จ
    if (
        $imagePath &&
        Storage::disk('public')->exists($imagePath)
    ) {
        Storage::disk('public')->delete($imagePath);
    }

    return redirect()
        ->route('staff.index')
        ->with('success', 'ลบพนักงานเรียบร้อยแล้ว');
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

private function authorizeSelfOrAdmin(User $user): void
{
    $actor = auth()->user();

    abort_unless(
        $actor && ($actor->role === 'admin' || $actor->is($user)),
        403
    );
}

private function assignableRoles(?User $actor): array
{
    return $actor?->role === 'admin'
        ? ['admin', 'manager', 'staff']
        : ['staff'];
}



}
