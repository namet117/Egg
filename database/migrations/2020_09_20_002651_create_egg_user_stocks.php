<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEggUserStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_stocks', function (Blueprint $table) {
            $table->id();
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_stocks');
    }
}
