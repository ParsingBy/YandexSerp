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
        Schema::create('yandex_serp', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->smallInteger('region_id');
            $table->string('phrase');
            $table->smallInteger('fetch_result_pages_count');            
            $table->enum('status', ['new', 'in_progress', 'done'])->default('new');
            $table->longText('result')->nullable(); 
        });

        Schema::create('yandex_serp_jobs', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->bigInteger('data_id')->unsigned();
            $table->smallInteger('page');
            $table->enum('status', ['new', 'done'])->default('new');
            $table->text('result')->nullable(); 
        });    

        Schema::table('yandex_serp_jobs', function($table) {
           $table->foreign('data_id')->references('id')->on('parsing_by_yandex_serp')->onDelete('cascade');
        });   

        Schema::create('yandex_serp_positions', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->bigInteger('keyword_id')->unsigned();
            $table->smallInteger('region_id');
            $table->enum('device_type', ['desktop', 'mobile'])->default('desktop');
            $table->date('last_parse_date');
            $table->enum('status', ['new', 'in_progress', 'done'])->default('new');
        });  

        Schema::create('yandex_serp_positions_history', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->bigInteger('positions_id')->unsigned();
            $table->date('date');
            $table->longText('result')->nullable();
        });  

        Schema::table('yandex_serp_positions_history', function($table) {
           $table->foreign('positions_id')->references('id')->on('yandex_serp_positions')->onDelete('cascade');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yandex_serp');
        Schema::dropIfExists('yandex_serp_jobs');
    }
}
