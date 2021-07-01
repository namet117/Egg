<?php


namespace App\Controller;


use Hyperf\Di\Annotation\Inject;

class StockController extends AbstractController
{
    /**
     * @Inject
     * @var \App\Service\StockService
     */
    private $stockService;

    public function searchStocks()
    {
        $keyword = $this->request->post('keyword', '');
        $keyword = is_string($keyword) ? str_replace([' '], '', $keyword) : '';
        if (empty($keyword)) {
            return $this->success([]);
        }
        $data = $this->stockService->getStocksByKeyword($keyword);
        return $this->success($data, '获取成功');
    }
}
