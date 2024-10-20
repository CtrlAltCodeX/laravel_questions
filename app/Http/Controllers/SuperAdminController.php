<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class SuperAdminController extends Controller
{

    public function super_admin()
    {
        $users = User::where('role','Super admin')->get();
        return view('super-admin.index', compact('users'));
    }

    public function show()
    {
        // $users = User::where('role','Super admin')->get();
        return view('super-admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' =>$request->input('name'),
            'email' =>$request->input('email'),
            'password' =>Hash::make($request->input('password')),
            'role'=>'Super admin',
        ]);

        return redirect()->route('super-admin.index')->with('success','Super admin created successfully!');
    }
}
