<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger("episode_id")->default(0);
            $table->bigInteger("item_id")->default(0);
            $table->text("content")->default(null);
            $table->integer("audio_type")->default(0)->comment("0=audio, 1= link ");
            $table->integer("server")->default(0)->comment("0=current, 1= link ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audio');
    }
};
