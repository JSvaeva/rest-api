<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\BlogPost;
use App\Models\Comment;
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

        if ($field != 'created' && $field != 'updated') {
            $field = 'updated';
        }
        
        $perPage = 15;

        $blogPosts = BlogPost::orderBy($field.'_at', $type)->paginate($perPage);

        return response()->json(['data' => [
            'items' => $blogPosts->items(),
            'current_page' => $blogPosts->currentPage(),
            'per_page' => $blogPosts->perPage(),
            'last_page' => $blogPosts->lastPage(),
            'total' => $blogPosts->total()
        ]], 200);
    }

    public function create(Request $request)
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
        
        return response()->json(['data' => [
            'id' => $blogPost->id,
            'author_id' => $blogPost->author_id,
            'title' => $blogPost->title,
            'content' => $blogPost->content ]], 202);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
        $blogPost = BlogPost::find($id);

        if (is_null($blogPost)) {
            return response()->json([
                'http_code' => 404,
                'code' => 1, 
                'title' => 'Post Not Found',
                'message' => 'Post with id ' . $id . " does not exist"
            ], 404);
        }
        
        $comments = $blogPost->comments()->paginate($perPage);

        return response()->json(['data' => [
            'items' => $comments->items(),
            'current_page' => $comments->currentPage(),
            'per_page' => $comments->perPage(),
            'last_page' => $comments->lastPage(),
            'total' => $comments->total()
        ]], 200);
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

        if (filter_var($blogPostId, FILTER_VALIDATE_INT) === false) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => 'Id ' . $id . ' is not integer'
            ], 422);
        }
        
        $blogPost = BlogPost::find($blogPostId);

        if (is_null($blogPost)) {
            return response()->json([
                'http_code' => 404,
                'code' => 1, 
                'title' => 'Post Not Found',
                'message' => 'Post with id ' . $id . " does not exist"
            ], 404);
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
            'author_id' => Auth::id(),
            'blog_post_id' => $blogPostId,
            'text' => $request->text
        ]);
        return response()->json(['data' => $comment], 201);
    }
}
