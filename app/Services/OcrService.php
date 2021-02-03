<?php

namespace App\Services;

use App\Lib\Aip\Ocr;
use App\Models\OcrLog;
use App\Traits\ServiceTrait;
use App\Utils\Helper;

class OcrService
{
    use ServiceTrait;


    private $driver = null;

    private function instance(): Ocr
    {
        if (!$this->driver) {
            $this->driver = new Ocr(config('ocr.app_id'), config('ocr.key'), config('ocr.secret'));
        }

        return $this->driver;
    }

    public function getInfoFromImage(string $full_path, string $public_path = ''): array
    {
        if (!file_exists($full_path) || !is_readable($full_path)) {
            $this->setError("File:{$full_path} is not readable");
            return [];
        }
        if (!$response = $this->getResultFromLogs($full_path, $public_path)) {
            $content = file_get_contents($full_path);
            $response = $this->instance()->basicAccurate($content);
            $this->setResponse($full_path, json_encode($response));
        }

        return $this->extractInfoFromWords($response);
    }

    private function getResultFromLogs(string $image_path, string $image_url = ''): array
    {
        $image_hash = md5_file($image_path);
        $row = OcrLog::where('image_hash', '=', $image_hash)->first();
        if ($row) {
            return !$row->response ? [] : (json_decode($row->response, true) ?: []);
        }
        $driver = 'baidu';
        $ocr = OcrLog::create(compact('image_hash', 'image_path', 'driver', 'image_url'));
        $ocr->save();

        return [];
    }

    private function setResponse(string $image_path, string $response): bool
    {
        $ocr = OcrLog::where('image_path', '=', $image_path)->first();
        if (!$ocr) {
            return false;
        }
        $ocr->response = $response;

        return $ocr->save();
    }

    public function extractInfoFromWords($response): array
    {
        if (!is_array($response) || empty($response['words_result']) || !is_array($response['words_result'])) {
            return [];
        }
        $words = array_column($response['words_result'], 'words');
        if (!$words) {
            return [];
        }
        $result = [];
        foreach ($words as $word) {
            $word = str_replace([',', '.'], '', $word);
            if (preg_match('/^持仓成本价\d+$/', $word)) {
                $result['cost'] = Helper::extractNumberFromString($word);
                $result['cost'] = substr($result['cost'], 0, -4) . '.' . substr($result['cost'], -4);
            } elseif (preg_match('/^持有份额\d+$/', $word)) {
                $result['hold_num'] = Helper::extractNumberFromString($word);
                $result['hold_num'] = substr($result['hold_num'], 0, -2) . '.' . substr($result['hold_num'], -2);
            } elseif (preg_match('/^\d{6}.+风险$/', $word)) {
                $result['code'] = mb_substr($word, 0, 6);
            }
        }

        return (isset($result['cost']) && isset($result['hold_num']) && isset($result['code'])) ? $result : [];
    }
}
