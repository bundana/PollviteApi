<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::get();
        return response()->success(true, 'Users retrieved successfully', $users);
    }

    public function show(Request $request)
    {
        $user = User::where('user_id', $request->id)->first();

        if (!$user) {
            return response()->error(false, 'User not found',);
        }

        return response()->success(true, 'User retrieved successfully', $user);
    }
}
