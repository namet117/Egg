<?php

namespace App\Console\Commands;

use App\Services\StockService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RefreshUserStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egg:refresh_user_stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh User Stock';

    /**
     * @var \App\Services\StockService
     */
    private $stockService;

    /**
     * RefreshUserStock constructor.
     *
     * @param \App\Services\StockService $stockService
     */
    public function __construct(StockService $stockService)
    {
        parent::__construct();
        $this->stockService = $stockService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rows = DB::table('user_stocks')
            ->leftJoin('stocks', 'user_stocks.stock_id', '=', 'stocks.id')
            ->select('stocks.id')
            ->distinct()
            ->get();
        foreach ($rows as $row) {
            // if ($this->isRecentlyUpdated($row->id)) {
            //     continue;
            // }
            // Cache::put($this->createKey($row->id), 1, 30);
            $this->stockService->refreshStock($row->id);
            // Cache::forget($this->createKey($row->id));
            $this->info("{$row->id} success!");
        }

        return 0;
    }

    private function createKey($id)
    {
        return 'egg_stock_' . $id;
    }

    private function isRecentlyUpdated($id)
    {
        return Cache::has($this->createKey($id));
    }
}
