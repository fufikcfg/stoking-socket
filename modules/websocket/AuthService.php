<?php

namespace app\modules\websocket;

use app\models\User;

class AuthService
{
    public function authenticate(string $token): ?User
    {
        return User::findIdentityByAccessToken($token);
    }
}