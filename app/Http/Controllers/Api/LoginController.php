<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Hash;
use App\Util;
use App\JSON;
use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use Psy\Exception\ErrorException;

class LoginController extends Controller {

    public function login(Request $request) {
        $input = JSON::decode($request->getContent());

        $username = Util::valueOrNullTrim($input->data, 'email');
        $password = Util::valueOrNull($input->data, 'password');
        $remember = Util::valueOrFalse($input->data, 'remember');

        $failResponse = response()->json(['message' => 'Invalid username/email or password'], 422);

        $validator = null;
        try {
            $validator = Validator::make(
                [
                    'username' => $username,
                    'password' => $password,
                ],
                [
                    'username' => 'required',
                    'password' => 'required',
                ]
            );
        } catch(ErrorException $e) {
            return $failResponse;
        }

        if($validator->fails()) return $failResponse;

        $user = User::authValidate($username, $password);
        if(!$user) return $failResponse;

        // succeded, login user
        Auth::loginUsingId($user->id, $remember);
        Session::flush();

        return response()->json(['login' => ['success' => true, 'id' => $user->id]]);

    }

    public function logout() {
        Auth::logout();
        Session::flush();

        return response()->json(['logout' => ['success' => true]]);
    }

}