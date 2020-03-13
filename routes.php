<?php
Route::prefix('admin')
->middleware(['web', 'role:super-admin'])
->namespace('\Sdkconsultoria\BlogScraping\Controllers')
->group(function () {
    Route::get('scraping', 'ScrapingController@index');
});
