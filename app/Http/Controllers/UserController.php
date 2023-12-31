<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function getAllUsers()
    {
        $users = User::all();

        return response()->json([
            'status_code' => 1000,
            'data' => $users,

        ]);
    }
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'message' => 'User not found',
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $id . '|max:255',
            'password' => 'required|string|min:8',
            'is_admin' => 'sometimes|boolean',

        ]);

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'is_admin' => $request->input('is_admin', false),

        ]);

        return response()->json([
            'status_code' => 1000,
            'message' => 'User updated successfully',
        ]);
    }

    public function deleteUser(Request $request, $id)
    {
    
        if (Auth::check()) {
            try {
                
                $user = User::findOrFail($id);

                $user->delete();

                return response()->json([
                    'status_code' => 1000,
                    'message' => 'User deleted successfully.',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Error deleting user.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
    }
}
