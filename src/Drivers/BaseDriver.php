<?php
namespace Sdkconsultoria\BlogScraping\Drivers;

use Storage;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Sdkconsultoria\BlogScraping\Models\{ScrapingData , ScrapingDataKey, ScrapingUrl, ScrapingDataImage};

/**
 *
 */
abstract class BaseDriver
{
    protected $timeout = 60;
    protected $method  = 'GET';
    protected $limit   = 10;
    protected $identifier;
    protected $scraping_url_id;

    abstract protected function parseData($data);
    abstract protected function getUrls();

    function __construct() {

    }

    public function getData()
    {
        $url = ScrapingUrl::where('driver', get_class($this))->first();
        $this->scraping_url_id = $url->id;

        $client = new Client(HttpClient::create(['timeout' => $this->timeout]));
        $data   = $this->parseData($client->request($this->method, $this->url.$url->url));

        $this->insertData($data);
    }


    protected function insertData(array $data)
    {
        $blog_post                   = new ScrapingData();
        $blog_post->created_by       = 1;
        $blog_post->status           = ScrapingData::STATUS_ACTIVE;
        $blog_post->scraping_url_id  = $this->scraping_url_id;
        $blog_post->name             = $data['name']??'';
        $blog_post->title            = $data['title']??'';
        $blog_post->subtitle         = $data['subtitle']??'';
        $blog_post->meta_author      = $data['meta_author']??'';
        $blog_post->meta_description = $data['meta_description']??'';
        $blog_post->description      = $data['description']??'';
        $blog_post->save();

        foreach ($data['images'] as $image) {
            $this->insertImage($image, $blog_post->id);
        }
    }

    protected function insertImage($image, $id)
    {
        $info     = pathinfo($image['url']);
        $contents = file_get_contents($image['url']);

        $image                   = new ScrapingDataImage();
        $image->scraping_data_id = $id;
        $image->extension        = $info['extension'];
        $image->alt              = $image['alt'];
        $image->name             = $info['filename'];
        $image->save();

        Storage::disk('local')->put('scraping/' . $id . '/' . $image->id . '.' . $info['extension'], $contents);
    }
}
