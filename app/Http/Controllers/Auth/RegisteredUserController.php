<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
      */
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'dob' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'phone' => 'nullable|numeric|min_digits:10|max_digits::14|unique:'.User::class,
            'password' => 'required|', Rules\Password::defaults(),
        ]);

        if ($validator->fails()) {
            return response()->error(false, 'User registration failed', $validator->errors());
        }


        $user = User::create([
            'user_id' => User::user_id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'avatar' => $request->avatar,
            'password' => Hash::make($request->string('password')),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $authFacade = [
            'access_token'  => $token,
            'token_type'    => 'Bearer'
        ];

        $user['auth'] = $authFacade;

        return response()->success(true, 'User registration successfully', $user);
    }
}
