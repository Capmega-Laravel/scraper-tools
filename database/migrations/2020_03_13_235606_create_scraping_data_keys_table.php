<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScrapingDataKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scraping_data_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->commonFields();

            $table->unsignedBigInteger('scraping_data_id')->unsigned()->index()->nullable();
            $table->foreign('scraping_data_id')->references('id')->on('scraping_data')->onDelete('restrict');

            $table->string('name', 64);
            $table->string('seoname', 64);
            $table->string('category', 64)->nullable();
            $table->string('seocategory', 64)->nullable();
            $table->string('value')->nullable();
            $table->string('seovalue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scraping_data_keys');
    }
}
