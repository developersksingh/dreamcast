<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

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
            'description' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validatedData = $request->validate($rules);

        $user = User::create($validatedData);

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->update(['profile_image' => $imagePath]);
        }
        return response()->json([
            'user' => $user->load('role'),
            'message' => 'User created successfully',
            'success' => true,
            'profile_image_url' => asset('storage/' . $user->profile_image)
        ]);
    }

    public function getUsersData()
    {
        $users = User::orderBy('id', 'desc')->with('role')->get();
        return response()->json($users);

    }
}
