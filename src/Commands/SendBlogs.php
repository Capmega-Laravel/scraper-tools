<?php

namespace Sdkconsultoria\BlogScraping\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Sdkconsultoria\BlogScraping\Models\ScrapingUrl;
use Storage;

class SendBlogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdk:ScrapingSendPosts';

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
            'timeout'  => 60.0,
        ]);

        $urls = ScrapingUrl::all();

        foreach ($urls as $key => $url) {
            $category = $url->category;

            if ($category) {
                $data   = $url->data;
                if ($data) {
                    $images = $data->images;
                    $array_images = [];
                    foreach ($images as $key => $image) {
                        $file_route = storage_path('app/scraping/') . $data->id . '/' . $image->id . '.' . $image->extension;
                        $array_images[] = [
                            'name'     => 'images[]',
                            'contents' => fopen($file_route, 'r')
                        ];
                    }

                    if ($data) {
                        $response = $client->request('POST', 'catch-post', [
                            'multipart' => array_merge([
                                [
                                    'name'     => 'name',
                                    'contents' => $url->name
                                ],
                                [
                                    'name'     => 'category',
                                    'contents' => $category->name
                                ],
                                [
                                    'name'     => 'description',
                                    'contents' => $data->description
                                ],
                            ], $array_images)
                        ]);
                        dump($response->getBody()->getContents());
                    }
                }
            }
        }
    }
}
