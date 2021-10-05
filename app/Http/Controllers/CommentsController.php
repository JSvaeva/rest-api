<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\BlogPost;
use App\Models\User;
use Validator;

class CommentsController extends Controller
{
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

        $comment = Comment::find($id);

        if (is_null($comment)) {
            return response()->json([
                'http_code' => 404,
                'code' => 1, 
                'title' => 'Post Not Found',
                'message' => 'Post with id ' . $id . " does not exist"
            ], 404);
        }

        return response()->json(['data' => [
            'id' => $comment->id,
            'author_id' => $comment->author_id,
            'blog_post_id' => $comment->blog_post_id,
            'text' => $comment->text ]], 202);
    }

    public function update(Request $request, $id) {
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
        $comment = Comment::find($id);

        if (!Auth::user()->is_admin && $comment->author_id !== Auth::id()) {
            return response()->json([
                'http_code' => 403,
                'code' => 1, 
                'title' => 'Access Error',
                'message' => 'You don\'t have the access to this page'
            ], 403);
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
        
        $comment->update($request->all());
    
        return response()->json(['data' => $comment], 202);
    }

    public function destroy($id)
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
        $comment = Comment::find($id);

        if (!Auth::user()->is_admin && $comment->author_id !== Auth::id()) {
            return response()->json([
                'http_code' => 403,
                'code' => 1, 
                'title' => 'Access Error',
                'message' => 'You don\'t have the access to this page'
            ], 403);
        }
        
        $comment->delete();

        return response()->json(['data' => [ 'id' => $id ]], 203);
    }
}
