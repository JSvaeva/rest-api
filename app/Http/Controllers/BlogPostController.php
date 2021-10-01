<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use App\Models\BlogPost;
use App\Models\User;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type, $field)
    {
        if (($type != 'asc' && $type != 'desc') || ($field != 'created' && field != 'updated')) {
            abort(404);
        }

        $blogPosts = BlogPost::orderBy($field.'_at', $type)->get();
        $blogPosts = $blogPosts->simplePaginate(10);
        
        $blogPosts = $blogPosts->map(function ($item, $key) {
            return collect($item)->except(['created_at', 'updated_at'])->toArray();
        });

        return $blogPosts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) //admin
    {
        $is_admin = Auth::user()->is_admin;
        if (!$is_admin) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|max:100',
            'content' => 'required|min:10|max:5000',
            'author_id' => 'required'
        ]);

        return BlogPost::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return BlogPost::find($id)->except(['created_at', 'updated_at']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) //admin
    {
        $is_admin = Auth::user()->is_admin;
        if (!$is_admin) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|max:100',
            'content' => 'required|min:10|max:5000',
            'author_id' => 'required'
        ]);

        $blogPost = BlogPost::find($id);        
        $blogPost->update($request->all());
    
        return $blogPost;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) //admin
    {
        $is_admin = Auth::user()->is_admin;
        if (!$is_admin) {
            abort(404);
        }

        return Post::destroy($id);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
