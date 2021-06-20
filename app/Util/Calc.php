<?php


namespace App\Util;


class Calc
{
    /**
     * 计算变动的百分比
     *
     * @param float $open
     * @param float $end
     *
     * @return float
     */
    public static function percent(float $open, float $end): float
    {
        return ($end == 0 || $open == $end || $open == 0)
            ? 0
            : bcmul(bcdiv(bcsub($end, $open, 4), $open, 4), 100, 2);
    }
}
