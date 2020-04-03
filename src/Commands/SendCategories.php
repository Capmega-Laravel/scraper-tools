<?php

namespace Sdkconsultoria\BlogScraping\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Sdkconsultoria\BlogScraping\Models\ScrapingCategory;

class SendCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdk:ScrapingSendCategories';

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
        $client = new Client([
            'base_uri' => 'http://127.0.0.1:8001/admin/scraping/',
            'timeout'  => 3.0,
        ]);

        $categories = ScrapingCategory::all();

        foreach ($categories as $key => $category) {
            $parent = $category->parent;
            if ($parent) {
                $parent = $parent->name;
            }
            $response = $client->request('POST', 'catch-category', [
                'form_params' => [
                    'name'   => $category->name,
                    'parent' => $parent,
                ]
            ]);
        }

    }
}
