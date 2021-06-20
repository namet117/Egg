<?php


namespace App\Util;


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

    public static function extractNumberFromString(string $string): string
    {
        return preg_replace('/[^\d.]/', '', $string);
    }

    public static function getExt(string $filename): string
    {
        $filename = strrev($filename);
        return strrev(substr($filename, 0, strpos($filename, '.')));
    }
}
