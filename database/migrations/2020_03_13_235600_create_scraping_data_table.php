<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScrapingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scraping_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->commonFields();

            $table->unsignedBigInteger('scraping_url_id')->unsigned()->index()->nullable();
            $table->foreign('scraping_url_id')->references('id')->on('scraping_urls')->onDelete('restrict');

            $table->unsignedBigInteger('scraping_data_id')->unsigned()->index()->nullable();
            $table->foreign('scraping_data_id')->references('id')->on('scraping_data')->onDelete('restrict');

            $table->string('version')->nullable();
            $table->string('identifier', 64)->unique()->nullable();
            $table->string('name', 64)->nullable();
            $table->string('seoname', 64)->nullable();
            $table->string('title', 120)->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('meta_author', 120)->nullable();
            $table->string('meta_description', 120)->nullable();
            $table->string('meta_keywords', 120)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scraping_data');
    }
}
