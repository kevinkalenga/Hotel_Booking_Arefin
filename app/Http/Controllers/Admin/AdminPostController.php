<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
class AdminPostController extends Controller
{
    public function index()
    {
        $posts = Post::get();
        return view('admin.post_view', compact('posts'));
    }
}
