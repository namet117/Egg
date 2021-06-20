<?php

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateStocks extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
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
            $table->decimal('last_real', 8, 4)->default(0)->comment('上日净值');
            $table->date('last_real_date')->nullable()->comment('上日净值日期');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
}
