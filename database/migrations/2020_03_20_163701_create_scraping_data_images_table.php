<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScrapingDataImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scraping_data_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->commonFields();

            $table->unsignedBigInteger('scraping_data_id')->unsigned()->index()->nullable();
            $table->foreign('scraping_data_id')->references('id')->on('scraping_data')->onDelete('restrict');
            $table->string('type', 30)->nullable();
            $table->string('extension', 6);
            $table->string('name')->nullable();
            $table->string('alt', 124)->nullable();
            $table->text('sizes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scraping_data_images');
    }
}
