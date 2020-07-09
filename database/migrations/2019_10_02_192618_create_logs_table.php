<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.CONFIG.TABLE_PREFIX') . 'logs_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 50)->nullable();
            $table->string('from', 25)->nullable();
            $table->string('url', 50)->nullable();
            $table->text('extra')->nullable();
            $table->string('ip', 20)->nullable();
            $table->string('level', 20)->nullable();
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
        Schema::dropIfExists(config('constants.CONFIG.TABLE_PREFIX') . 'logs_data');
    }
}
