<?php

namespace App\Traits;

trait EnumOptions
{
    public static function options(): array {
        return collect(self::cases())->pluck('value', 'value')->toArray();
    }
}
