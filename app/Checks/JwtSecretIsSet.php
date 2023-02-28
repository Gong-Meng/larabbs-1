<?php

namespace App\Checks;

use BeyondCode\SelfDiagnosis\Checks\Check;

class JwtSecretIsSet implements Check
{
    public function name(array $config): string
    {
        return 'JWT Secret is set';
    }

    public function check(array $config): bool
    {
        return config('jwt.secret') != null;
    }

    public function message(array $config): string
    {
        return 'JWT_SECRET 没有被设置';
    }
}