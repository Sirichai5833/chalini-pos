<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return view('index' ,['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        Post::create($request->only(['title','content']));

        return redirect()->route('index')->with('success','Post created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        try {
            $post->update($request->only(['title','content']));
            return redirect()->route('index')->with('success', 'Post updated!');
        } catch (\Exception $e) {
            return redirect()->route('index')->with('error', 'เกิดข้อผิดพลาดในการอัปเดต');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
    try {
        $post->delete();
        return redirect()->route('index')->with('success', 'Post deleted!');
    } catch (\Exception $e) {
        return redirect()->route('index')->with('error', 'เกิดข้อผิดพลาดในการลบโพสต์');
    }
}

public function payCash(Request $request)
{
    // สมมุติชำระเงินสำเร็จ
    return redirect()->route('payment.page')->with('success', 'ชำระเงินสำเร็จเรียบร้อยแล้ว!');
}
}