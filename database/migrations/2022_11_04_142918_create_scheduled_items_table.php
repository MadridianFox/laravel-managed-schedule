<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('managed_schedule_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('command', 512);
            $table->string('schedule');
            $table->boolean('enabled');
            $table->timestamp('last_start_at', 6)->nullable();
            $table->timestamp('last_end_at', 6)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('managed_schedule_items');
    }
};
