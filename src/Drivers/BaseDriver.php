<?php
namespace Sdkconsultoria\BlogScraping\Drivers;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Sdkconsultoria\BlogScraping\Models\{ScrapingData , ScrapingDataKey, ScrapingUrl};

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

    function __construct() {
        $url = ScrapingUrl::where('driver', get_class($this))->first();
        $this->scraping_url_id = $url->id;
    }

    public function getData()
    {
        $client     = new Client(HttpClient::create(['timeout' => $this->timeout]));
        $parse_data = $this->parseData($client->request($this->method, $this->url));

        foreach ($parse_data as $key => $data) {
            if ($key < $this->limit) {
                $this->insertData($data);
            }else{
                break;
            }
        }
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
    }
}
