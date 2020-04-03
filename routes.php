<?php
Route::prefix('admin/scraping')
->namespace('\Sdkconsultoria\BlogScraping\Controllers')
->group(function () {
    Route::post('catch-category', 'ScrapingController@catchCategory');
    Route::post('catch-post', 'ScrapingController@catchPost');
        
    Route::middleware(['web', 'role:super-admin'])->group(function () {
        Route::resource('/source', 'ScrapingSourceController');
        Route::resource('/url'   , 'ScrapingUrlController');
        Route::resource('/target', 'ScrapingTargetController');
        Route::get('/source/{seoname}/scan', 'ScrapingSourceController@scanMenu')->name('source.scan-menu');
        Route::get('/source/{seoname}/scan-url', 'ScrapingSourceController@scanUrls')->name('source.scan-urls');
    });
});