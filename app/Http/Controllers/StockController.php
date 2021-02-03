<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\UserStock;
use App\Http\Requests\UserStockPost;
use App\Services\OcrService;
use App\Services\StockService;
use App\Utils\Calc;
use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StockController extends Controller
{
    /**
     * @var \App\Services\StockService
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

            // 计算今日预计盈亏
            $row['today_estimate'] = $row['today_real'] = 0;
            if ($last_real_time && $last_real) {
                // 当前估值时间大于上日净值时间，则认为是交易中
                if ($row['estimate_date'] && (strtotime($row['stocks']['estimate_date']) > $last_real_time)) {
                    $row['today_estimate'] =
                        (float)bcmul($row['hold_num'], bcsub($row['stocks']['estimate'], $last_real, 4), 2);
                    $row['last_real'] = $last_real;
                }
                // 当前净值时间大于上日净值日期，则认为是净值有更新
                if ($row['real_date'] && (strtotime($row['stocks']['real_date']) > $last_real_time)) {
                    $row['today_real'] =
                        (float)bcmul($row['hold_num'], bcsub($row['stocks']['real'], $last_real, 4), 2);
                    $row['last_real'] = $last_real;
                }
            }

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
                        'real_date', 'estimate_date', 'last_real', 'today_estimate', 'today_real',
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
            'home',
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

    public function uploadImg(Request $request, OcrService $ocrService)
    {
        if (empty($_FILES['image']) || !is_array($_FILES['image'])) {
            return ['code' => 1, 'msg' => '请上传图片'];
        }

        $max_size = 2 * 1024 * 1024;
        $allowed_types = ['image/png', 'image/jpeg'];
        $images = [];

        foreach ($_FILES['image']['name'] as $k => $name) {
            $single = [
                'name' => $_FILES['image']['name'][$k],
                'type' => $_FILES['image']['type'][$k],
                'tmp_name' => $_FILES['image']['tmp_name'][$k],
                'error' => $_FILES['image']['error'][$k],
                'size' => $_FILES['image']['size'][$k],
            ];
            $tmp[] = $single;
            if (
                $single['error'] == 0
                && $single['size'] < $max_size
                && in_array($single['type'], $allowed_types)
                && getimagesize($single['tmp_name'])
            ) {
                $filename = md5_file($single['tmp_name']) . '.' . (Helper::getExt($single['name']) ?: 'png');
                $dirname = 'public/upload/' . date('Ymd');
                $file_path = "{$dirname}/{$filename}";
                if (Storage::put($file_path, file_get_contents($single['tmp_name']))) {
                    $images[] = [
                        'url' => Storage::url($file_path),
                        'file_path' => $file_path,
                        'file_full_path' => Storage::path($file_path),
                        'filename' => $filename,
                    ];
                }
            }
        }
        if (empty($images)) {
            return ['code' => 1, 'msg' => '没有可保存的图片，请重新选择'];
        }

        $result = [];
        foreach ($images as $image) {
            $row = ['url' => $image['url']];
            $data = [];
            if (
                ($info = $ocrService->getInfoFromImage($image['file_full_path'], $image['url']))
                && ($stock = Stock::whereCode($info['code'])->first())
            ) {
                $row['code'] = $stock->code;
                $row['name'] = $stock->name;
                $row['cate1'] = '';
                $where = [
                    'stock_id' => $stock->id,
                    'user_id' => Auth::id(),
                ];
                if ($detail = UserStock::where($where)->first()) {
                    $data['old'] = [
                        'cost' => $detail->cost,
                        'hold_num' => $detail->hold_num,
                    ];
                    $row['cate1'] = $detail->cate1;
                }
                $data['new'] = [
                    'cost' => $info['cost'],
                    'hold_num' => $info['hold_num'],
                ];
                $is_delete = floatval($data['new']['cost']) == 0;
                if (!$detail && $is_delete) {
                    $data = [];
                }
                if ($data) {
                    $row['request'] = $detail
                        ? route('egg.userStock.' . ($is_delete ? 'destroy' : 'update'), $detail->id)
                        : route('egg.userStock.store');
                    $row['method'] = $detail ? ($is_delete ? 'DELETE' : 'PUT') : 'POST';
                }
            }
            $row['data'] = $data ?: false;
            $result[] = $row;
        }
\Log::info(json_encode($result));
        return ['code' => 0, 'data' => $result, 'msg' => '识别成功'];
    }
}
