<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('users.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^[6-9]\d{9}$/',
            'description' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $user = User::create($validatedData);

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->update(['profile_image' => $imagePath]);
        }

        return response()->json([
            'user' => $user->load('role'),
            'message' => 'User created successfully',
            'success' => true,
            'profile_image_url' => asset('storage/' . $user->profile_image),
        ]);
    }


    public function getUsersData()
    {
        $users = User::orderBy('id', 'desc')->with('role')->get();
        return response()->json($users);
    }
}
