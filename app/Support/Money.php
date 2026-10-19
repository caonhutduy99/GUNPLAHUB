<?php

namespace App\Support;

class Money
{
    /** Định dạng tiền Việt: 1250000 -> 1.250.000đ */
    public static function format(float|int|string|null $amount): string
    {
        return number_format((float) $amount, 0, ',', '.') . 'đ';
    }
}
