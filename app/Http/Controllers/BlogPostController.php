<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
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
        if ($type != 'asc' && $type != 'desc') {
            $type = 'asc';
        }

        if ($field != 'created' && field != 'updated') {
            $field = 'updated';
        }
        
        $perPage = 15;

        $blogPosts = BlogPost::orderBy($field.'_at', $type)->paginate($perPage); //only take some of the pagination info!!!

        return response()->json(['data' => $blogPosts], 200);
    }

    public function create(Request $request) //admin
    {
        if (!Auth::check()) {
            return response()->json([
                'http_code' => 401,
                'code' => 1, 
                'title' => 'Log In Error',
                'message' => 'You must be logged in to view this page'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'content' => 'required|min:10|max:5000'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => $validator->messages()
            ], 422);
        }

        $blogPost = BlogPost::create([
            'id' => $request->id,
            'author_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content
        ]);
        return response()->json(['data' => $blogPost], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => 'Id ' . $id . ' is not integer'
            ], 422);
        }

        $blogPost = BlogPost::find($id);

        if (is_null($blogPost)) {
            return response()->json([
                'http_code' => 404,
                'code' => 1, 
                'title' => 'Post Not Found',
                'message' => 'Post with id ' . $id . " does not exist"
            ], 404);
        }
        
        $blogPost->except(['created_at', 'updated_at']);
        
        return response()->json(['data' => $blogPost], 202);
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
        if (!Auth::check()) {
            return response()->json([
                'http_code' => 401,
                'code' => 1, 
                'title' => 'Log In Error',
                'message' => 'You must be logged in to view this page'
            ], 401);
        }

        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => 'Id ' . $id . ' is not integer'
            ], 422);
        }
        $blogPost = BlogPost::find($id);

        if (!Auth::user()->is_admin && $blogPost->author_id !== Auth::id()) {
            return response()->json([
                'http_code' => 403,
                'code' => 1, 
                'title' => 'Access Error',
                'message' => 'You don\'t have the access to this page'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'content' => 'required|min:10|max:5000',
            'author_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => $validator->messages()
            ], 422);
        }

                
        $blogPost->update($request->all());
    
        return response()->json(['data' => $blogPost], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) //admin
    {
        if (!Auth::check()) {
            return response()->json([
                'http_code' => 401,
                'code' => 1, 
                'title' => 'Log In Error',
                'message' => 'You must be logged in to view this page'
            ], 401);
        }

        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => 'Id ' . $id . ' is not integer'
            ], 422);
        }
        $blogPost = BlogPost::find($id);

        if (!Auth::user()->is_admin && $blogPost->author_id !== Auth::id()) {
            return response()->json([
                'http_code' => 403,
                'code' => 1, 
                'title' => 'Access Error',
                'message' => 'You don\'t have the access to this page'
            ], 403);
        }
        
        $blogPost->delete();

        return response()->json(['data' => [ 'id' => $id ]], 203);
    }

    public function getBlogComments($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => 'Id ' . $id . ' is not integer'
            ], 422);
        }

        $perPage = 15;

        return response()->json(['data' => BlogPost::find($id)->comments()->paginate($perPage)], 200); //only take some of the pagination info!!!
    }

    public function leaveComment(Request $request, $blogPostId) {
        if (!Auth::check()) {
            return response()->json([
                'http_code' => 401,
                'code' => 1, 
                'title' => 'Log In Error',
                'message' => 'You must be logged in to view this page'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'text' => 'required|min:10|max:500'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => $validator->messages()
            ], 422);
        }

        $comment = Comment::create([
            'id' => $request->id,
            'author_id' => Auth::id(),
            'blog_post_id' => $blogPostId,
            'text' => $request->content
        ]);
        return response()->json(['data' => $comment], 201);
    }
}
