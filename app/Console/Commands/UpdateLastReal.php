<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateLastReal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egg:refresh_last_real';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update last_real info';

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
        \DB::update('UPDATE `stocks` SET `last_real` = `real`, `last_real_date` = `real_date` where `real` > 0');
        return 0;
    }
}
