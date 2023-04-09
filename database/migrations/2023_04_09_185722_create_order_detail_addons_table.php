<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_detail_id')->constrained()->cascadeOnDelete();
            $table->foreignId('addon_id')->nullable()->constrained();
            $table->string('addon_name')->nullable();
            $table->double('price')->nullable();
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('order_detail_addons');
    }
}
