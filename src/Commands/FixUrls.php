<?php

namespace Sdkconsultoria\BlogScraping\Commands;

use Illuminate\Console\Command;
use Sdkconsultoria\Blog\Models\BlogPost;

class FixUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdk:ScrapingFixUrl';

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
        $blogs = BlogPost::where('status', BlogPost::STATUS_ACTIVE)->get();
        $pattern = '/:code=([a-z-0-9A-Z\/_]+)/';
        $replacement = 'https://conspiracytheory.net/${1}';

        foreach ($blogs as $key => $blog) {
            preg_match($pattern, $blog->description, $matches);

            foreach ($matches as $key => $match) {
                if (strpos($match, ':code=') !== 0) {
                    $blog_link = BlogPost::where('identifier', 'like', '%'.$match)->where('status', BlogPost::STATUS_ACTIVE)->first();
                    $blog->description = str_replace(':code='.$match, $blog_link->getUrl(), $blog->description);
                    $blog->save();
                }
            }
            // dd($matches);
            // dd(preg_replace($pattern, $replacement, $blog->description));
        }
    }
}
