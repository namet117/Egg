<?php

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateUserStocks extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('stock_id')->comment('股票或基金ID');
            $table->integer('user_id')->comment('用户ID');
            $table->string('cate1', 10)->default('')->comment('板块');
            $table->decimal('cost', 8, 4)->default(0)->comment('成本');
            $table->decimal('hold_num', 11, 2)->default(0)->comment('持有份数');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stocks');
    }
}
