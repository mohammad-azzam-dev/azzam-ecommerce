<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('last_used_index')->default(0);
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::table('twilio_calls')->insert([
            'last_used_index' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twilio_calls');
    }
}
