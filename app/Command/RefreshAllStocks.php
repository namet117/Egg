<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Stock;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class RefreshAllStocks extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('egg:refresh_all');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('【egg】pull all stocks from remote and then update them');
    }

    public function handle()
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
