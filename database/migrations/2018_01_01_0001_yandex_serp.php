<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class YandexSerp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parsing_by_yandex_serp', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->smallInteger('region_id');
            $table->string('phrase');
            $table->smallInteger('fetch_result_pages_count');            
            $table->enum('status', ['new', 'in_progress', 'done'])->default('new');
            $table->text('result')->nullable(); 
        });

        Schema::create('parsing_by_yandex_serp_jobs', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->bigInteger('data_id')->unsigned();
            $table->smallInteger('page');
            $table->enum('status', ['new', 'done'])->default('new');
        });    

        Schema::table('parsing_by_yandex_serp_jobs', function($table) {
           $table->foreign('data_id')->references('id')->on('parsing_by_yandex_serp')->onDelete('cascade');
        });    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parsing_by_yandex_serp');
        Schema::dropIfExists('parsing_by_yandex_serp_jobs');
    }
}
