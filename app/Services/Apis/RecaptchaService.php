<?php

namespace App\Services\Apis;

use App\Enums\UserRoles;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RecaptchaService
{
    private static $baseUrl = "https://www.google.com/recaptcha/api/siteverify";

    public static function verify(Request $request, $user=null)
    {
        if (!has_recaptcha() || empty($request['recaptcha']) || (empty(!$user) && $user->role == UserRoles::SUPER_ADMIN) ) {
            return true;
        }

        if (!$request->has('recaptcha')) {
            throw ValidationException::withMessages(['error' => __('Sorry, we were unable to verify you as a human.')]);
        }

        $score = 0.6; 
        $validate = true; 
        $data = [ 'secret' => recaptcha_key('secret'), 'response' => $request['recaptcha'] ];

        $error_msg  = __('Your request failed to complete as bot detected.');
        $error_log  = '';

        try {
            $response = Http::asForm()->post(self::$baseUrl, $data);

            if ($response->failed()) {
                $error_msg  = __('An error occurred during response validations.');
                $validate = false;
            }

            if (isset($response['success']) && $response['success']==false) {
                $error_log = (isset($response['error-codes'][0])) ? $response['error-codes'][0] : '';
                $error_msg = ($error_log=='invalid-input-secret') ? __('An error occurred during response validations. Please feel free to contact us if issues persist.') : $error_msg;
                $validate = false;
            }

            if (isset($response['score']) && ($response['score'] < $score)) {
                $validate = false;
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        if($validate) {
            return true;
        } else {
            if($error_log) { Log::info($error_log); }
            throw ValidationException::withMessages([ 'error' => $error_msg ]);
        }
    }
}
