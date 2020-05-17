<?php

namespace Capmega\BlogScraping\Commands;

use Illuminate\Console\Command;
use Capmega\BlogScraping\Models\{ScrapingData, ScrapingUrl};

class FollowUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdk:ScrapingFollowUrl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $blogs = ScrapingData::where('status', ScrapingData::STATUS_ACTIVE)->get();
        $pattern = '/:code=([a-z-0-9A-Z\/_]+)/';

        foreach ($blogs as $key => $blog) {
            preg_match_all($pattern, $blog->description, $matches);
            $this->info('scaneando data: ' . $blog->scraping_url_id);

            foreach ($matches[1]??[] as $key => $match) {
                if (strpos($match, ':code=') !== 0) {
                    $blog_link = ScrapingUrl::where('url', 'like', '%'.$match)->where('status', ScrapingUrl::STATUS_ACTIVE)->first();
                    if (!$blog_link) {
                        $this->info('No existe la url: ' . $match);
                        $url = new ScrapingUrl();
                        $url->name = $match;
                        $url->url = '/' . $match;
                        $url->scraping_source_id = 1;
                        $url->scraping_category_id = 1;
                        $url->driver = 'App\Scraper\WantToKnow';
                        $url->status = ScrapingUrl::STATUS_ACTIVE;
                        $url->save();
                    }
                }
            }
        }
    }
}
