<?php

namespace App\Http\Traits\Auth;

use App\Models\APIKEY;
use Illuminate\Http\Request;

trait BearerTrait
{
    public function __invoke(Request $request)
    {
        return self::verifyToken($request);
    }

    private static function verifyToken(Request $request)
    {
        $token = $request->bearerToken();

        $bearerToken = APIKEY::where('live_secret_key', $token)
            ->orWhere('test_secret_key', $token)->first();

        if (!$bearerToken) {
            return response()->error(true, 'Authorization Token not found or invalid');
        }

        // Extract the token from the Authorization header

        // If no token is provided, return an error response
        if (!$token) {
            return response()->error(true, 'The request was not authorized');
        }

        // Decode the token
        // Token is valid, return it for further processing
        return base64_decode($token);
    }
}
