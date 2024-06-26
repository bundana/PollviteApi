<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Http\Traits\Auth\BearerTrait;
use AshAllenDesign\ShortURL\Models\ShortURL;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use AshAllenDesign\ShortURL\Classes\Builder;

class URLShortenerController extends Controller
{
    use BearerTrait;

    public function store(Request $request)
    {
        $method = $request->method();
        if (!$request->isMethod('post') && !$request->isMethod('get')) {
            return response()->json([
                'error' => true,
                'message' => "The {$method} request method is not allowed. Allowed methods are GET and POST."
            ], 405);
        }

        // Verify the bearer token
        $decodedToken = $this->verifyToken($request);
        if ($decodedToken instanceof \Illuminate\Http\JsonResponse) {
            return $decodedToken; // Return error response
        }

        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'enforce_https' => 'nullable|bool',
            'custom_key' => 'nullable|string',
            'active_at' => 'nullable|date',
            'deactivate_at' => 'nullable|date',
            'redirect_status' => 'nullable|string|in:301,302,303,307,308',
            'single_use' => 'nullable|bool',
            'meta_data' => 'nullable|array',
            'track_visits' => 'nullable|bool',
            'track_ip_address' => 'nullable|bool',
            'track_browser' => 'nullable|bool',
            'track_operating_system' => 'nullable|bool',
            'track_device_type' => 'nullable|bool',
            'track_referer_url' => 'nullable|bool',
        ], [
            'redirect_status.in' => 'Invalid redirect status, must be in 301,302,303,307 and 308'
        ]);

        if ($validator->fails()) {
            return response()->error(true, 'Url shortener validation fails', $validator->errors());
        }

        $url = $request->url;
        $shortURLObject = app(Builder::class)->destinationUrl($url);

        if ($request->custom_key) {
            $existed = ShortURL::findByKey($request->custom_key);
            if ($existed) {
                return response()->json([
                    'success' => false, 'message' => 'A short URL with this key already exists.'
                ], 422);
            }
            $shortURLObject->urlKey($request->custom_key);

        }

        if ($request->has('active_at')) {
            $shortURLObject->activateAt(Carbon::parse($request->input('active_at', Carbon::now())));
        }

        if ($request->has('deactivate_at')) {
            $shortURLObject->deactivateAt(Carbon::parse($request->input('deactivate_at', Carbon::now())));
        }

        if ($request->redirect_status) {
            $shortURLObject->redirectStatusCode($request->redirect_status);
        }

        if ($request->single_use) {
            $shortURLObject->singleUse();
        }

        if ($request->meta_data) {
            $shortURLObject->beforeCreate(function (ShortURL $model) use ($request): void {
                $request->meta_data;
            });
        }

        if ($request->enforce_https) {
            $shortURLObject->secure();
        }

        if ($request->track_visits) {
            $shortURLObject->trackVisits($request->track_visits);
        }

        if ($request->track_ip_address) {
            $shortURLObject->trackIPAddress($request->track_ip_address);
        }

        if ($request->track_browser) {
            $shortURLObject->trackBrowser($request->track_browser);
        }

        if ($request->track_operating_system) {
            $shortURLObject->trackOperatingSystem($request->track_operating_system);
        }

        if ($request->track_device_type) {
            $shortURLObject->trackDeviceType($request->track_device_type);
        }

        if ($request->track_referer_url) {
            $shortURLObject->trackRefererUrl($request->track_referer_url);
        }

        $finalURL = $shortURLObject->make();
        $shortURL = $finalURL->default_short_url;

        $response_data = [
            'short_url' => $shortURL,
            'long_url' => $request->url,
            'configurations' => $request->all()
        ];

        return response()->success(true, 'URL shortener successful', $response_data);
    }

}
