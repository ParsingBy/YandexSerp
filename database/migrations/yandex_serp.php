<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYandexSerpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parsing_by_yandex_serp_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('region_id', 7);
            $table->integer('fetch_result_pages_count', 2);
            $table->string('phrase');
            $table->enum('status', ['new', 'in_progress', 'done'])->default('new');
        });

        Schema::create('parsing_by_yandex_serp_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('data_id');
            $table->foreign('data_id')->references('id')->on('parsing_by_yandex_serp_data')->onDelete('cascade');
            $table->integer('page', 2);
            $table->enum('status', ['new', 'done'])->default('new');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parsing_by_yandex_serp_jobs');
        Schema::dropIfExists('parsing_by_yandex_serp_jobs');
    }
}
