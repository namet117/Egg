<?php

namespace App\Egg\Controllers;

use App\Egg\Models\Stock;
use App\Egg\Models\UserStock;
use App\Egg\Requests\UserStockPost;
use App\Egg\Services\StockService;
use App\Utils\Calc;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    /**
     * @var \App\Egg\Services\StockService
     */
    private $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    // 首页
    public function index()
    {
        $rows = UserStock::where(['user_id' => Auth::id()])->with('stocks')->get()->toArray();
        $estimate_date = $real_date = [];

        $groups = $stocks = [];
        $total_cost = $total_profit = 0;
        $estimate_amount = $real_amount = [];
        foreach ($rows as $row) {
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
                $real_date[$row['real_date']] ++;
            }
            // 持仓成本金额
            $row['cost_amount'] = (float)bcmul($row['cost'], $row['hold_num'], 2);
            // 持仓收益率
            $row['profit_ratio'] = Calc::percent($row['cost'], $row['stocks']['real']);
            // 持仓收益金额
            $row['profit_amount'] = (float)bcmul($row['hold_num'], bcsub($row['stocks']['real'], $row['cost'], 4), 2);
            // 总持仓成本金额
            $total_cost = (float)bcadd($total_cost, $row['cost_amount'], 2);
            // 总收益金额
            $total_profit = (float)bcadd($total_profit, $row['profit_amount'], 2);
            // 格式化数据
            $row['stocks']['estimate_ratio'] = (float)$row['stocks']['estimate_ratio'];
            $row['stocks']['real_ratio'] = (float)$row['stocks']['real_ratio'];
            $row['hold_num'] = (float)$row['hold_num'];

            $estimate_amount[$row['stocks']['estimate_date']] = $estimate_amount[$row['stocks']['estimate_date']] ?? 0;
            $real_amount[$row['stocks']['real_date']] = $real_amount[$row['stocks']['real_date']] ?? 0;

            // 合并单元格分组
            if ($row['cate1']) {
                $groups[$row['cate1']][] = $row;
            } else {
                $groups[] = [$row];
            }
            $stocks[] = array_merge(
                Arr::only(
                    $row,
                    [
                        'id', 'cate1', 'cate2', 'cost', 'hold_num', 'cost_amount', 'profit_amount', 'profit_ratio',
                        'real_date', 'estimate_date',
                    ]
                ),
                Arr::only($row['stocks'], ['code', 'name', 'estimate_ratio', 'real_ratio', 'real', 'estimate']),
                [
                    'edit' => route('egg.userStock.update', $row['id']),
                    'delete' => route('egg.userStock.destroy', $row['id']),
                ]
            );
        }

        arsort($real_date);
        arsort($estimate_date);
        $today_date = date('m-d');
        $t_real_date = !empty($real_date[$today_date]) ? $today_date : ($real_date ? key($real_date) : '');
        $t_estimate_date = !empty($estimate_date[$today_date]) ? $today_date : ($estimate_date ? key($real_date) : '');
        $groups = array_values($groups);

        return view(
            'egg.home',
            compact('groups', 'total_cost', 'total_profit', 'stocks', 't_real_date', 't_estimate_date')
        );
    }

    public function update($id, UserStockPost $request)
    {
        $data = $request->validated();

        $detail = UserStock::where(['user_id' => Auth::id(), 'id' => $id])->first();
        if (empty($detail)) {
            return response()->setStatusCode(403);
        }
        $stock = Stock::where('code', $data['code'])->first();
        $data['stock_id'] = $stock->id;

        $detail->fill($data);
        $detail->save();

        $this->stockService->refreshStock($detail->stock_id);

        return ['code' => 0, 'msg' => 'Update Success'];
    }

    public function store(UserStockPost $request)
    {
        $data = $request->validated();
        $stock = Stock::where('code', $data['code'])->first();
        $data['stock_id'] = $stock->id;
        $data['user_id'] = Auth::id();
        $detail = UserStock::create($data);

        $this->stockService->refreshStock($detail->stock_id);

        return ['code' => 0, 'msg' => 'Store Success'];
    }

    public function destroy($id)
    {
        UserStock::where(['user_id' => Auth::id(), 'id' => $id])->delete();

        return ['code' => 0, 'msg' => 'Delete Success'];
    }

    public function search(Request $request)
    {
        $keyword = trim($request->get('keyword'));
        $type = trim($request->get('type'));
        $type = in_array($type, ['fund', 'shares', 'etf']) ? $type : 'fund';
        if (!$keyword) {
            return [];
        }

        return Stock::where('type', $type)
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%");
            })
            ->limit(50)
            ->get(['code', 'name']);
    }
}