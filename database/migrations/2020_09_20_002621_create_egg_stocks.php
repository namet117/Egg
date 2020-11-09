<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEggStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['shares', 'etf', 'fund'])->comment('类型：shares股票；etf场内基金；fund场外基金');
            $table->string('code', 20)->unique()->comment('股票或基金编号');
            $table->string('name', 100)->comment('股票或基金名字');
            $table->decimal('open', 8, 4)->default(0)->comment('开盘价');
            $table->decimal('estimate', 8, 4)->default(0)->comment('估值');
            $table->decimal('estimate_ratio', 6, 2)->default(0)->comment('估值跌涨幅');
            $table->decimal('real', 8, 4)->default(0)->comment('净值');
            $table->decimal('real_ratio', 6, 2)->default(0)->comment('净值跌涨幅');
            $table->date('estimate_date')->nullable()->comment('估值的日期');
            $table->date('real_date')->nullable()->comment('净值的日期');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
