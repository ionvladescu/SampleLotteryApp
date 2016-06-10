<?php

namespace App\Http\Controllers;

use Mail;
use Hash;
use App\Util;
use App\JSON;
use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use Psy\Exception\ErrorException;

class SignUpController extends Controller {

    public function signUp(Request $request) {

        $input = JSON::decode($request->getContent());

        $vldEmail  = null;
        $vldMobile = null;

        try {
            $email  = Util::valueOrNullTrim($input->data, 'email');
            $mobile = Util::valueOrNullTrim($input->data, 'mobile');

            if(!$email && !$mobile) {
                return response()->json(['message' => 'No email or phone num entered.'], 422);
            }

            if($email) {
                $vldEmail = Validator::make(
                    [
                        'email' => $email,
                    ],
                    [
                        'email' => 'required|email|unique:users',
                    ]
                );

            }

            if($mobile) {
                $vldMobile = Validator::make(
                    [
                        'mobile' => $input->data->mobile,
                    ],
                    [
                        'mobile' => 'required|min:10|max:10',
                    ]
                );
            }

        } catch(ErrorException $e) {
            return response()->json(['message' => 'No email or phone num entered.'], 422);
        }

        if($vldEmail->fails()) {
            $failed = $vldEmail->failed();

            if(array_key_exists('email', $failed) && array_key_exists('Unique', $failed['email'])) {
                $user = User::where('email', 'LIKE', $email)->first();

                switch($user->state) {
                    case 'signedup':
                        return response()->json(['message' => 'Already signed up. Resend activation code?'], 422);

                    case 'registered':
                        return response()->json(['message' => 'You have already registered.'], 422);
                }

            }

            return response()->json(['message' => 'The email you have entered is invalid.'], 422);

        }

        $activationCode = Util::makeActivationCode();

        $user = User::signUp($email, $mobile, null, null, $activationCode);

        if($user->email) {
            $url     = env('APP_URL') . "/#activate/";
            $subject = "Your activation link!";
            $body    = "Hi! Thanks for registering to our lotteries, please use the following link to activate your account.\r\n" . $url . $activationCode;

            Mail::raw($body, function ($m) use ($user, $subject, $body) {
                $m->to($user->email)->subject($subject);
            });
        }

        if($user->mobile) {
            $username = env('SMSAPI_USER');
            $password = env('SMSAPI_PASS');
            $to       = env('SMSAPI_MOBILEPREFIX') . $user->mobile;
            $from     = env('SMSAPI_FROM');
            $message  = "Hi! Thanks for registering to our lotteries, please check your email for more info on how to activate your account.";
            $url      = 'https://api.smsapi.com/sms.do';
            $c        = curl_init();
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, 'username=' . $username . '&password=' . $password . '&from=' . $from . '&to=' . $to . '&message=' . $message);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($c);
            curl_close($c);
        }

        return response()->json(['popup' => ['message' => 'Thanks for signing up, check your email. Cheers!']]);

    }

    public function checkCode(Request $request) {
        $input = JSON::decode($request->getContent());

        $activationCode = Util::valueOrNull($input->data, 'activationCode');
        $validator      = null;
        try {
            $validator = Validator::make(
                [ //data
                  'activationCode' => $activationCode,
                ],
                [ //rules
                  'activationCode' => 'required|exists:users,activation_code',
                ],
                [ //messages
                  'activationCode.required' => 'Invalid activation code.',
                  'activationCode.exists'   => 'Invalid activation code.',
                ]

            );
        } catch(ErrorException $e) {
            return response()->json(['message' => 'missing data'], 422);
        }

        if($validator->fails()) {
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        $user = User::where('activation_code', '=', $activationCode)->firstOrFail();

        return response()->json(['result' => ['firstname' => $user->first_name, 'lastname' => $user->last_name, 'email' => $user->email, 'mobile' => $user->mobile]]);

    }

    public function register(Request $request) {
        $input = JSON::decode($request->getContent());

        $activationCode = Util::valueOrNull($input->data, 'activationCode');
        $firstname      = Util::valueOrNullTrim($input->data, 'firstname');
        $lastname       = Util::valueOrNullTrim($input->data, 'lastname');
        $password       = Util::valueOrNullTrim($input->data, 'password');

        $validator = null;
        try {
            $validator = Validator::make(
                [ //data
                  'activationCode' => $activationCode,
                  'password'       => $password,

                ],
                [ //rules
                  'activationCode' => 'required|exists:users,activation_code',
                  'password'       => 'required|min:4',
                ],
                [ //messages
                  'activationCode.required' => 'Invalid activation code.',
                  'activationCode.exists'   => 'Invalid activation code.',
                  'password.min'            => 'Your password is too short.',
                ]

            );
        } catch(ErrorException $e) {
            return response()->json(['message' => 'missing data'], 422);
        }

        if($validator->fails()) {
            return response()->json(['message' => $validator->messages()->first()], 422);
        }

        $user = User::where('activation_code', '=', $activationCode)->firstOrFail();

        $passwordHash = Hash::make($input->data->password);
        $user->register($passwordHash, $firstname, $lastname);

        return response()->json(['result' => ['id' => $user->id, 'email' => $user->email]]);
    }

}