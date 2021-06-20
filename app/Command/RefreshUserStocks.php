<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class RefreshUserStocks extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;



    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('egg:refresh_user_stock');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Refresh User Stock');
    }

    public function handle()
    {
        $this->line('Hello Hyperf!', 'info');
    }
}
