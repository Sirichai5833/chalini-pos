<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'all');

        $suppliers = Supplier::query()
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($filter) use ($search) {
                        $filter
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere(
                                'contact_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('tax_id', 'like', "%{$search}%")
                            ->orWhere(
                                'supplied_products',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                $status === 'active',
                fn ($query) => $query->where('is_active', true)
            )
            ->when(
                $status === 'inactive',
                fn ($query) => $query->where('is_active', false)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'suppliers.index',
            compact('suppliers', 'search', 'status')
        );
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateSupplier($request);

        $validated['is_active'] =
            $request->boolean('is_active');

        Supplier::create($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'เพิ่มผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(
        Request $request,
        Supplier $supplier
    ) {
        $validated = $this->validateSupplier(
            $request,
            $supplier
        );

        $validated['is_active'] =
            $request->boolean('is_active');

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'แก้ไขผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function destroy(Supplier $supplier)
    {
        $hasReceipts = $supplier
            ->stockReceipts()
            ->exists();

        if ($hasReceipts) {
            return back()->with(
                'error',
                'ไม่สามารถลบผู้จำหน่ายที่มีประวัติรับสินค้าได้ กรุณาปิดใช้งานแทน'
            );
        }

        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'ลบผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update([
            'is_active' => !$supplier->is_active,
        ]);

        return back()->with(
            'success',
            $supplier->is_active
                ? 'เปิดใช้งานผู้จำหน่ายแล้ว'
                : 'ปิดใช้งานผู้จำหน่ายแล้ว'
        );
    }

    private function validateSupplier(
        Request $request,
        ?Supplier $supplier = null
    ): array {
        return $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('suppliers', 'name')
                        ->ignore($supplier?->id),
                ],
                'contact_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],
                'address' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
                'tax_id' => [
                    'nullable',
                    'string',
                    'max:30',
                    Rule::unique('suppliers', 'tax_id')
                        ->ignore($supplier?->id),
                ],
                'supplied_products' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
                'is_active' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'name.required' =>
                    'กรุณากรอกชื่อผู้จำหน่าย',
                'name.unique' =>
                    'ชื่อผู้จำหน่ายนี้มีอยู่แล้ว',
                'email.email' =>
                    'รูปแบบอีเมลไม่ถูกต้อง',
                'tax_id.unique' =>
                    'เลขประจำตัวผู้เสียภาษีนี้มีอยู่แล้ว',
                'supplied_products.max' =>
                    'รายละเอียดสินค้าที่จำหน่ายต้องไม่เกิน 2,000 ตัวอักษร',
            ]
        );
    }
}