<?php
namespace Capmega\BlogScraping;

use Illuminate\Support\ServiceProvider;
use Sdkconsultoria\Blog\Models\BlogKey;
use Illuminate\Database\Schema\Blueprint;

class ScrapingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../views', 'scraping');
        $this->loadTranslationsFrom(__DIR__.'/../translations', 'scraping');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/scraping'),
            __DIR__.'/../config/scraping.php' => config_path('scraping.php'),
            __DIR__.'/../translations' => resource_path('lang/vendor/scraping'),
        ]);

        $this->commands([
            \Capmega\BlogScraping\Commands\Scraping::class,
            \Capmega\BlogScraping\Commands\SendCategories::class,
            \Capmega\BlogScraping\Commands\SendBlogs::class,
            \Capmega\BlogScraping\Commands\TestApi::class,
            \Capmega\BlogScraping\Commands\FixUrls::class,
            \Capmega\BlogScraping\Commands\FollowUrls::class,
            \Capmega\BlogScraping\Commands\Spin::class,

        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/scraping.php', 'scraping'
        );
    }

}
