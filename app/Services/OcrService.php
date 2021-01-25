<?php

namespace App\Services;

use App\Lib\Aip\Ocr;
use App\Models\OcrLog;
use App\Traits\ServiceTrait;

class OcrService
{
    use ServiceTrait;


    private Ocr $driver;

    private function instance(): Ocr
    {
        if (!$this->driver) {
            $this->driver = new Ocr(config('ocr.app_id'), config('ocr.key'), config('ocr.secret'));
        }

        return $this->driver;
    }

    public function getInfoFromImage(string $image_path): array
    {
        if (!file_exists($image_path) || !is_readable($image_path)) {
            $this->setError("File:{$image_path} is not readable");
            return [];
        }
        if (!$response = $this->getResultFromLogs($image_path)) {
            $content = file_get_contents($image_path);
            $response = $this->instance()->basicAccurate($content);
            $this->setResponse($image_path, json_encode($response));
        }

        return $this->extractInfoFromWords($response);
    }

    private function getResultFromLogs(string $image_path): array
    {
        $image_hash = md5_file($image_path);
        $row = OcrLog::where('image_hash', '=', $image_hash)->first();
        if ($row) {
            return json_decode($row->response, true);
        }
        $driver = 'baidu';
        $ocr = OcrLog::create(compact('image_hash', 'image_path', 'driver'));
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

    private function extractInfoFromWords($response): array
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

        }
    }
}
