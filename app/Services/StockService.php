<?php


namespace App\Services;


use App\Http\Models\Stock;
use App\Utils\Helper;
use QL\QueryList;

class StockService
{
    public function refreshStock($id): bool
    {
        $stock = Stock::find($id);
        if (empty($stock)) {
            return false;
        }
        switch ($stock->type) {
            case 'fund':
                return $this->refreshFund($stock);
                break;
            default:
                return false;
        }
    }

    public function refreshFund(Stock $stock): bool
    {
        $url = "https://fund.eastmoney.com/{$stock->code}.html";
        $ql = QueryList::get($url);

        $real = $ql->find('.dataItem02>.dataNums>.ui-font-large')->text();
        $real_ratio = trim($ql->find('.dataItem02>.dataNums>.ui-font-middle')->text(), '%');
        $real_date = $this->extractDateFromString($ql->find('.dataItem02>dt>p')->text());
        if (empty($real) || empty($real_date) || empty($real_ratio)) {
            return false;
        }
        if (!is_numeric($real_ratio)) {
            $real_ratio = 0;
        }
        $data = $this->getEstimateInfo($stock->code);
        $data = array_merge($data, compact('real', 'real_date', 'real_ratio'));

        $stock->fill($data);

        return $stock->save();
    }

    private function getEstimateInfo(string $code): array
    {
        $url = "https://fundgz.1234567.com.cn/js/{$code}.js?rt=" . Helper::microTime();
        $data = file_get_contents($url);
        if (empty($data)) {
            return [];
        }
        $data = preg_replace('/^jsonpgz\((.*)\);$/', '$1', $data);
        if (empty($json = json_decode($data))) {
            return [];
        }
        return [
            'estimate' => $json->gsz,
            'estimate_date' => date('Y-m-d', strtotime($json->gztime)),
            'estimate_ratio' => $json->gszzl,
            'open' => $json->dwjz,
        ];
    }

    private function extractDateFromString(string $string): string
    {
        $preg = '\d{4}-\d{2}-\d{2}';
        $result = preg_replace("/.+({$preg}).+/", '$1', $string);

        return preg_match("/^{$preg}$/", $result) ? $result : '';
    }
}
