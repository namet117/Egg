<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\UserStock;
use App\Util\Calc;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Arr;

class UserController extends AbstractController
{
    /**
     * @Inject
     * @var \App\Service\Auth
     */
    private $auth;

    public function getUserStocks()
    {
        $rows = UserStock::where(['user_id' => $this->auth->id()])->with('stocks')->get();
        if ($rows->isEmpty()) {
            return $this->success([], '暂无持仓信息');
        }
        $rows = $rows->toArray();
        $estimate_date = $real_date = [];

        $stocks = [];
        $total_cost = $total_profit = 0;
        $estimate_amount = $real_amount = [];
        foreach ($rows as $row) {
            $last_real_time = !empty($row['stocks']['last_real_date'])
                ? strtotime($row['stocks']['last_real_date'])
                : 0;
            $last_real = !empty($row['stocks']['last_real'])
                ? (float)$row['stocks']['last_real']
                : 0;
            $row['estimate_date'] = !empty($row['stocks']['estimate_date'])
                ? date('m-d', strtotime($row['stocks']['estimate_date']))
                : '';
            if (empty($row['stocks']['estimate_date'])) {
                $row['estimate_updated'] = false;
            } elseif (!isset($estimate_date[$row['estimate_date']])) {
                $estimate_date[$row['estimate_date']] = 1;
            } else {
                $estimate_date[$row['estimate_date']]++;
            }

            $row['real_date'] = !empty($row['stocks']['real_date'])
                ? date('m-d', strtotime($row['stocks']['real_date']))
                : '';
            if (empty($row['stocks']['real_date'])) {
                $row['real_updated'] = false;
            } elseif (!isset($real_date[$row['real_date']])) {
                $real_date[$row['real_date']] = 1;
            } else {
                $real_date[$row['real_date']]++;
            }
            // 持仓成本金额
            $row['cost_amount'] = (float)bcmul($row['cost'], $row['hold_num'], 2);
            // 持仓收益率
            $row['profit_ratio'] = Calc::percent(floatval($row['cost']), floatval($row['stocks']['real']));
            // 持仓收益金额
            $row['profit_amount'] = (float)bcmul($row['hold_num'], bcsub($row['stocks']['real'], $row['cost'], 4), 2);
            // 总持仓成本金额
            $total_cost = (float)bcadd((string)$total_cost, (string)$row['cost_amount'], 2);
            // 总收益金额
            $total_profit = (float)bcadd((string)$total_profit, (string)$row['profit_amount'], 2);
            // 格式化数据
            $row['stocks']['estimate_ratio'] = (float)$row['stocks']['estimate_ratio'];
            $row['stocks']['real_ratio'] = (float)$row['stocks']['real_ratio'];
            $row['hold_num'] = (float)$row['hold_num'];

            // 计算今日预计盈亏
            $row['today_estimate'] = $row['today_real'] = 0;
            if ($last_real_time && $last_real) {
                // 当前估值时间大于上日净值时间，则认为是交易中
                if ($row['estimate_date'] && (strtotime($row['stocks']['estimate_date']) > $last_real_time)) {
                    $row['today_estimate'] =
                        (float)bcmul(
                            (string)$row['hold_num'],
                            bcsub($row['stocks']['estimate'], (string)$last_real, 4),
                            2
                        );
                    $row['last_real'] = $last_real;
                }
                // 当前净值时间大于上日净值日期，则认为是净值有更新
                if ($row['real_date'] && (strtotime($row['stocks']['real_date']) > $last_real_time)) {
                    $row['today_real'] =
                        (float)bcmul(
                            (string)$row['hold_num'],
                            bcsub($row['stocks']['real'], (string)$last_real, 4),
                            2
                        );
                    $row['last_real'] = $last_real;
                }
            }

            $estimate_amount[$row['stocks']['estimate_date']] = $estimate_amount[$row['stocks']['estimate_date']] ?? 0;
            $real_amount[$row['stocks']['real_date']] = $real_amount[$row['stocks']['real_date']] ?? 0;

            $stocks[] = array_merge(
                Arr::only(
                    $row,
                    [
                        'cate1', 'cost', 'hold_num', 'cost_amount', 'profit_amount', 'profit_ratio',
                        'real_date', 'estimate_date', 'last_real', 'today_estimate', 'today_real',
                    ]
                ),
                Arr::only($row['stocks'], ['code', 'name', 'estimate_ratio', 'real_ratio', 'real', 'estimate'])
            );
        }

        arsort($real_date);
        arsort($estimate_date);
        $today_date = date('m-d');
        $t_real_date = !empty($real_date[$today_date]) ? $today_date : ($real_date ? key($real_date) : '');
        $t_estimate_date = !empty($estimate_date[$today_date]) ? $today_date : ($estimate_date ? key($real_date) : '');

        $data = compact('total_cost', 'total_profit', 'stocks', 't_real_date', 't_estimate_date');

        return $this->success($data, '读取成功');
    }

    public function updateStock()
    {

    }
}
