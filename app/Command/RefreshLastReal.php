<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\DbConnection\Db;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class RefreshLastReal extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('egg:refresh_last_real');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('update last_real info');
    }

    public function handle()
    {
        // FIXME 判断今天是否交易日，是的话才更新
        $sql = 'UPDATE `stocks` SET `last_real` = `real`, `last_real_date` = `real_date` where `real` > 0';
        $affected = Db::update($sql);

        echo "affected: {$affected} rows\n";

    }
}
