<?php


namespace App\Service;


use App\Exception\EggException;
use App\Model\Stock;
use App\Model\UserStock;
use Hyperf\Utils\Arr;

class StockService
{
    public function validate(array $data): void
    {
        $this->_checkPriceAndNum($data['type'], (float)$data['num'], (float)$data['price']);

        // if (!empty($data['cate1']) && !preg_match('/^[\u4e00-\u9fa5a-zA-Z0-9]+$/i', $data['cate1'])) {
        //     throw new EggException('板块只能填中文字母和数字');
        // }
        if (!empty($data['cate1']) && (\mb_strlen($data['cate1']) > 5)) {
            throw new EggException('板块最多5个字');
        }
    }

    public function save(int $user_id, string $code, float $price, float $num, string $cate1 = ''): UserStock
    {
        $stock = Stock::where('code', $code)->first();
        $user_stock = UserStock::create([
            'stock_id' => $stock->id,
            'user_id' => $user_id,
            'cate1' => $cate1,
            'cost' => $price,
            'hold_num' => $num,
        ]);
        if (!$user_stock->save()) {
            throw new EggException('保存持仓信息失败');
        }

        return $user_stock;
    }

    public function getStocksByKeyword(string $keyword): array
    {
        $key = preg_match('/\d/', $keyword) ? 'code' : 'name';
        $rows = Stock::where($key, 'like', "%{$keyword}%")->limit(10)->get()->toArray();
        if (!$rows) {
            return [];
        }
        $data = [];
        foreach ($rows as $row) {
            $data[] = array_merge(
                Arr::only($row, ['code', 'type', 'name']),
                ['desc' => Stock::TYPE[$row['type']]],
            );
        }

        return $data;
    }

    private function _checkPriceAndNum(string $type, float $price, float $num): void
    {
        list(, $suffix) = explode((string)$price, '.');
        if (strlen($suffix) > 4) {
            throw new EggException('单价最多只能保留四位小数');
        }
        list(, $suffix) = explode((string)$num, '.');
        if ($type === 'fund' && strlen($suffix) > 4) {
            throw new EggException('场外基金持仓数最多只能保留四位小数');
        } elseif (strlen($suffix) > 0) {
            $name = Stock::TYPE[$type];
            throw new EggException("{$name}持仓数须为大于等于0的整数");
        }
    }
}
