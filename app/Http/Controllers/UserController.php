<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
}
