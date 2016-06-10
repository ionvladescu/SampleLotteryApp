<?php

namespace App;

use Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    protected $table = 'users';

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function lotteries() {
        return $this->belongsToMany('App\Lottery', 'users_lotteries', 'user_id', 'lottery_id')->withTimestamps();
    }

    public static function signUp($email, $mobile = null, $firstname = null, $lastname = null, $activationCode) {

        $self = new self();

        $self->state           = 'signedup';
        $self->email           = $email;
        $self->mobile          = $mobile;
        $self->first_name      = $firstname;
        $self->last_name       = $lastname;
        $self->is_active       = false;
        $self->activation_code = $activationCode;

        $self->save();

        return $self;
    }

    //NO CHECKS
    public function register($passwordHash, $firstname = null, $lastname = null) {
        $this->password        = $passwordHash;
        $this->first_name      = $firstname;
        $this->last_name       = $lastname;
        $this->state           = 'registered';
        $this->is_active       = 1;
        $this->activation_code = null;
        $this->activation_sms  = null;

        $this->save();
    }

    public static function authValidate($username, $password) {

        $user = self::where('email', 'LIKE', $username, 'AND')->where('is_active', '=', 1)->first();
        if(!$user) return false;

        if(!Hash::check($password, $user->password)) return false;

        return $user;
    }

}
