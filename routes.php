<?php
Route::prefix('admin/scraping')
->middleware(['web', 'role:super-admin'])
->namespace('\Sdkconsultoria\BlogScraping\Controllers')
->group(function () {
    Route::resource('/source', 'ScrapingSourceController');
    Route::resource('/url'   , 'ScrapingUrlController');
    Route::resource('/target', 'ScrapingTargetController');
    Route::post('set-data', 'ScrapingController@setData');
    Route::get('get-data', 'ScrapingController@getData');
    Route::get('/source/{seoname}/scan', 'ScrapingSourceController@scanMenu')->name('source.scan-menu');
    Route::get('/source/{seoname}/scan-url', 'ScrapingSourceController@scanUrls')->name('source.scan-urls');
});
