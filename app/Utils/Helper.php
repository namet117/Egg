<?php


namespace App\Utils;


class Helper
{
    /**
     * 获取当前毫秒
     * @return float
     */
    public static function microTime(): float
    {
        list($m_sec, $sec) = explode(' ', microtime());

        return (float)sprintf('%.0f', (floatval($m_sec) + floatval($sec)) * 1000);
    }
}
