<?php

namespace App;

class Utils
{
    public static function enumValues($enumClass): array
    {
        return array_map(fn ($case) => $case->value, $enumClass::cases());
    }
}
