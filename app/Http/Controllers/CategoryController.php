<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;



class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:categories,name',
    ], [
        'name.required' => 'กรุณากรอกชื่อประเภท',
        'name.unique' => 'ชื่อประเภทนี้ถูกใช้ไปแล้ว',
    ]);

    Category::create($request->all());

    return redirect()->route('categories.index')->with('success', 'เพิ่มประเภทเรียบร้อยแล้ว!');
}



    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

   public function update(Request $request, Category $category)
{
    $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('categories')->ignore($category->id),
        ],
    ], [
        'name.required' => 'กรุณากรอกชื่อประเภท',
        'name.unique' => 'ชื่อประเภทนี้ถูกใช้ไปแล้ว',
    ]);

    $category->update($request->all());

    return redirect()->route('categories.index')->with('success', 'แก้ไขประเภทเรียบร้อยแล้ว!');
}


    public function destroy(Category $category)
{
    // ตรวจสอบว่าหมวดหมู่มีสินค้าอยู่หรือไม่
    if ($category->products()->count() > 0) {
        return redirect()->route('categories.index')->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้ เพราะยังมีสินค้าอยู่');
    }

    $category->delete();

    return redirect()->route('categories.index')->with('success', 'ลบหมวดหมู่เรียบร้อยแล้ว!');
}

}
