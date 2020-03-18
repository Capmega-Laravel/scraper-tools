<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScrapingSourceTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scraping_source_targets', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->unsigned();
            $table->unsignedBigInteger('target_id')->unsigned();


            $table->foreign('source_id')->references('id')->on('scraping_sources')->onDelete('restrict');
            $table->foreign('target_id')->references('id')->on('scraping_targets')->onDelete('restrict');

            // $table->unique(['scraping_source_id', 'scraping_target_id']);
            $table->primary(['source_id', 'target_id']);

            $table->timestamps();
            $table->commonFields();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scraping_source_targets');
    }
}
