AuthAlumnosProvider.php<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider as UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;


class AuthAlumnosProvider extends UserProvider {

    public function validateCredentials(UserContract $user, array $credentials)
    {

        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

}