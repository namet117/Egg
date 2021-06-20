<?php

declare(strict_types=1);

use Hyperf\Crontab\Crontab;

return [
    'enable' => true,
    'crontab' => [
        // 每天凌晨1点执行拉取全部基金、ETF、股票的操作
        (new Crontab())->setType('command')->setName('egg-refresh')->setRule('0 1 * * *')->setCallback([
            'command' => 'egg:refresh_all',
        ]),
        // 每天早上8：55更新当前净值为昨日净值
        (new Crontab())->setType('command')->setName('egg-refresh-last-real')->setRule('55 8 * * *')->setCallback([
            'command' => 'egg:refresh_last_real',
        ]),
        // 每分钟更新当前用户加入列表的基金
        (new Crontab())->setType('command')->setName('egg-refresh-user-stocks')->setRule('* * * * *')->setCallback([
            'command' => 'egg:refresh_user_stock',
        ]),
    ],
];
