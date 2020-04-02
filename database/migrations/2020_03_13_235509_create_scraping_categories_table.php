<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScrapingCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scraping_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->commonFields();

            $table->string('name', 64)->nullable();
            $table->string('seoname', 64)->nullable();

            $table->unsignedBigInteger('scraping_source_id')->unsigned()->index()->nullable();
            $table->foreign('scraping_source_id')->references('id')->on('scraping_sources')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scraping_categories');
    }
}
