<?php
Route::prefix('admin/scraping')
->middleware(['web', 'role:super-admin'])
->namespace('\Sdkconsultoria\BlogScraping\Controllers')
->group(function () {
    // Route::get('scraping', 'ScrapingController@index');
    Route::resource('/source', 'ScrapingSourceController');
    Route::resource('/url'   , 'ScrapingUrlController');
    Route::resource('/target', 'ScrapingTargetController');
});
