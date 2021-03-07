<?php

namespace App\Console\Commands;

use App\Models\Stock;
use Illuminate\Console\Command;

class RefreshAllStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egg:refresh_all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '【egg】pull all stocks from remote and then update them';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->refreshFund();

        return 0;
    }

    private function refreshFund(): bool
    {
        $url = 'http://fund.eastmoney.com/js/fundcode_search.js';
        $content = file_get_contents($url);
        if (empty($content)) {
            $this->error('get fund list from: '.$url.' failed!');

            return false;
        }
        $json = str_replace(['varr=', ';', "\r\n", "\n"], '', str_replace(' ', '', $content));
        if (substr($json, 0, 1) !== '[') {
            $json = preg_replace('/^[^a-zA-Z\d]+\[\[/', '[[', $json);
        }
        $rows = json_decode($json, true);
        if (empty($rows)) {
            $this->error('json decode failed, error: '.json_last_error_msg());

            return false;
        }

        $created_num = 0;
        $ignore_num = 0;
        $total_num = count($rows);
        $begin_time = time();
        $data = [];
        foreach ($rows as $k => $row) {
            $where = [
                'code' => $row[0],
                'type' => 'fund',
            ];
            if (Stock::where($where)->count() == 0) {
                $data[] = array_merge($where, [
                    'name' => $row[2],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                ++$created_num;
            } else {
                ++$ignore_num;
            }
            if (count($data) > 200) {
                Stock::insert($data);
                $data = [];
            }
            echo "\r";
            ++$k;
            echo "{$k}/{$total_num}: {$where['code']}";
        }
        if ($data) {
            Stock::insert($data);
        }

        $spend_time = time() - $begin_time;
        echo "\n";
        $this->info("total: {$total_num}, created: {$created_num}, ignored: {$ignore_num}, spend: {$spend_time}s");

        return true;
    }
}
