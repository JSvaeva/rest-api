<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Session;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $perPage = 15;
        $users = User::paginate($perPage);

        return response()->json(['data' => [
            'items' => $users->items(),
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'last_page' => $users->lastPage(),
            'total' => $users->total()
        ]], 200);
        //return response()->json($users);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => $validator->messages()
            ], 422);
        }

        $user = User::create(array(
            'name' => $request->name, 
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ));

        return response()->json(['data' => $user], 201);
    }

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

        $user = User::find($id);
        
        if (!is_null($user)) {
            $user->except(['created_at', 'updated_at']);
        }
        return response()->json(['data' => $user], 202);
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
        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => 'Id ' . $id . ' is not integer'
            ], 422);
        }
        $user = User::find($id);

        if (is_null($user)) {
            return response()->json([
                'http_code' => 404,
                'code' => 1, 
                'title' => 'User Not Found',
                'message' => 'User with id ' . $id . " does not exist"
            ], 404);
        }

        if (!Auth::user()->is_admin && $user->id !== Auth::id()) {
            return response()->json([
                'http_code' => 403,
                'code' => 1, 
                'title' => 'Access Error',
                'message' => 'You don\'t have the access to this page'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => $validator->messages()
            ], 422);
        }

        $user->update($request->all());
    
        return response()->json(['data' => $user], 202);
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

        $user = user::find($id);

        if (is_null($user)) {
            return response()->json([
                'http_code' => 404,
                'code' => 1, 
                'title' => 'User Not Found',
                'message' => 'User with id ' . $id . " does not exist"
            ], 404);
        }

        if (!Auth::user()->is_admin && $user->id !== Auth::id()) {
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

    public function getUserComments($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => 'Id ' . $id . ' is not integer'
            ], 422);
        }

        $perPage = 15;

        $user = User::find($id);

        if (is_null($user)) {
            return response()->json([
                'http_code' => 404,
                'code' => 1, 
                'title' => 'User Not Found',
                'message' => 'User with id ' . $id . " does not exist"
            ], 404);
        }
        
        $comments = $user->comments();
        $comments->paginate($perPage);

        return response()->json(['data' => [
            'items' => $comments->items(),
            'current_page' => $comments->currentPage(),
            'per_page' => $comments->perPage(),
            'last_page' => $comments->lastPage(),
            'total' => $comments->total()
        ]], 200);
    }

    public function getUserBlogPosts($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => 'Id ' . $id . ' is not integer'
            ], 422);
        }

        $perPage = 15;

        $user = User::find($id);

        if (is_null($user)) {
            return response()->json([
                'http_code' => 404,
                'code' => 1, 
                'title' => 'User Not Found',
                'message' => 'User with id ' . $id . " does not exist"
            ], 404);
        }
        
        $posts = $user->blogPosts();
        $posts->paginate($perPage);

        return response()->json(['data' => [
            'items' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'per_page' => $posts->perPage(),
            'last_page' => $posts->lastPage(),
            'total' => $posts->total()
        ]], 200);
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return response()->json(['message' => 'You are already logged in']);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'http_code' => 422,
                'code' => 1, 
                'title' => 'Validation Error',
                'message' => $validator->messages()
            ], 422);
        }
        
		if (Auth::attempt(array('email' => $request->email, 'password' => $request->password))) {
            return response()->json(['message' => 'You logged in successfully!']);
		} else {
            $user = User::where('email' , '=', $request->email)->first();

			if (is_null($user)) {
                return response()->json([
                    'http_code' => 422,
                    'code' => 1, 
                    'title' => 'Login Error',
                    'message' => 'Incorrect email'
                ], 422);
            }
            else if ($user->password !== bcrypt($request->password)) {
                return response()->json([
                    'http_code' => 422,
                    'code' => 1, 
                    'title' => 'Login Error',
                    'message' => 'Incorrect pwd'
                ], 422);
            }
		}
    }

    public function logout() {
		Auth::logout();
        return response()->json(['message' => 'You logged out successfully!']);
    }
}
