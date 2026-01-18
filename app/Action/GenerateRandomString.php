<?php

namespace App\Action;

class GenerateRandomString
{
    public static function execute($length = 6)
    {
        return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
    }
}
