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

    public function add()
    {
        return view('admin.post_add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'heading' => 'required',
            'short_content' => 'required',
            'content' => 'required',
        ]);

        $final_name = null;

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->extension();
            $final_name = time() . '.' . $ext;
            $request->file('photo')->move(public_path('uploads/'), $final_name);
        }

        $obj = new Post();
        $obj->photo = $final_name;
        $obj->heading = $request->heading;
        $obj->short_content = $request->short_content;
        $obj->content = $request->content;
        $obj->total_view = 1;
        $obj->save();

        return redirect()->back()->with('success', 'Post added successfully');
    }

    public function edit($id)
    {
        $post_data = Post::findOrFail($id);
        return view('admin.post_edit', compact('post_data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'heading' => 'required',
            'short_content' => 'required',
            'content' => 'required',
        ]);

        $obj = Post::findOrFail($id);

        if ($request->hasFile('photo')) {
            // supprimer l'ancienne photo si elle existe
            if ($obj->photo && file_exists(public_path('uploads/' . $obj->photo))) {
                unlink(public_path('uploads/' . $obj->photo));
            }

            $ext = $request->file('photo')->extension();
            $final_name = time() . '.' . $ext;
            $request->file('photo')->move(public_path('uploads/'), $final_name);

            $obj->photo = $final_name;
        }

        // ne pas toucher à la photo si aucune nouvelle n’est envoyée
        $obj->heading = $request->heading;
        $obj->short_content = $request->short_content;
        $obj->content = $request->content;
        $obj->save();

        return redirect()->back()->with('success', 'Post updated successfully');
    }


    public function delete($id)
    {
        $single_data = Post::where('id', $id)->first();
        unlink(public_path('uploads/'.$single_data->photo));
        $single_data->delete();

         return redirect()->back()->with('success', 'Post deleted successfully');
    }

}
