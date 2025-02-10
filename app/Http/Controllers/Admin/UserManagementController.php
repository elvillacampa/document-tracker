<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('lastSession')->get();

        return view('users.index', compact('users'));
    }

    /**
     * Approve a user's registration.
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->approved = true;
        $user->save();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User approved successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user's details (role and/or password).
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Check if this is an "edit details" update or a "change password" update
        if ($request->has('name')) {
            // This is an edit details request
            $data = $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'role'  => 'required|in:admin,encoder,viewer',
            ]);

            $user->name  = $data['name'];
            $user->email = $data['email'];
            $user->role  = $data['role'];
        } else {
            // This is a change password request (the form includes a hidden role field)
            $data = $request->validate([
                'role'                 => 'required|in:admin,encoder,viewer',
                'password'             => 'required|min:8|confirmed',
            ]);

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            // Optionally, you can update the role too, if that's intended:
            // $user->role = $data['role'];
        }

        $user->save();

        // Return a JSON response if the request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'user'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
                'message' => 'User updated successfully.'
            ]);
        }

        // Fallback for non-AJAX requests
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
}
