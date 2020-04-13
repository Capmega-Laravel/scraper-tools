<?php
namespace Sdkconsultoria\BlogScraping\Drivers;

use Storage;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Sdkconsultoria\BlogScraping\Models\{ScrapingData , ScrapingDataKey, ScrapingUrl, ScrapingDataImage, ScrapingCategory, ScrapingSource};

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
    protected $client;
    protected $resource;

    abstract protected function parseData($data);
    abstract protected function getUrls();
    abstract protected function parseUrl($data);
    abstract protected function clearData($data);

    function __construct() {
        $this->client = new Client(HttpClient::create(['timeout' => $this->timeout]));
        $this->resource = $this->getSource();
    }

    public function getData()
    {
        $urls = ScrapingUrl::where('driver', get_class($this))->get()->toArray();

        foreach ($urls as $key => $url) {
            $this->scraping_url_id = $url['id'];
            $blog_post = ScrapingData::where('scraping_url_id', $this->scraping_url_id)->first();

            if (!$blog_post) {
                $data = $this->parseData($this->crawl($url['url']));
                if ($data) {
                    $this->insertData($data);
                }
            }
        }
    }

    public function crawl($url = '')
    {
        return $this->client->request($this->method, $this->url.$url);
    }


    protected function insertData(array $data)
    {
        $blog_post = ScrapingData::where('scraping_url_id', $this->scraping_url_id)->first();

        if (!$blog_post) {
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

            if (isset($data['images'])) {
                foreach ($data['images'] as $image) {
                    $this->insertImage($image, $blog_post->id);
                }
            }
        }
    }

    protected function insertImage($image, $id)
    {
        $info     = pathinfo($image['url']);
        $contents = file_get_contents(str_replace(' ', '%20', $image['url']));

        if (strlen($info['extension']) < 5) {
            $image                   = new ScrapingDataImage();
            $image->scraping_data_id = $id;
            $image->extension        = $info['extension'];
            $image->alt              = $image['alt'];
            $image->name             = $info['filename'];
            $image->save();

            Storage::disk('local')->put('scraping/' . $id . '/' . $image->id . '.' . $info['extension'], $contents);
        }
    }

    protected function processUrl()
    {

    }

    protected function insertCategory($category_name, $parent_category = false)
    {
        $category = ScrapingCategory::where('name', $category_name)->where('scraping_source_id', $this->resource['id']);
        if ($parent_category) {
            $category = $category->where('scraping_category_id', $parent_category->id);
        }
        $category = $category->first();

        if ($category) {
            return $category;
        }

        $category = new ScrapingCategory();
        if ($parent_category) {
            $category->scraping_category_id = $parent_category->id;
        }
        $category->name               = $category_name;
        $category->scraping_source_id = $this->resource['id'];
        $category->save();

        return $category;

    }

    protected function insertUrl($data)
    {
        $new_url = substr($data['url'], 1);

        $url = ScrapingUrl::where('url', $new_url)->first();
        if ($url) {
            return $url;
        }

        $url                       = new ScrapingUrl();
        $url->created_by           = 1;
        $url->scraping_source_id   = $this->resource['id'];
        if ($data['category']) {
            $url->scraping_category_id = $data['category']->id;
        }
        $url->name                 = $data['name'];
        $url->url                  = $data['url'];
        $url->driver               = static::class;
        $url->status               = ScrapingUrl::STATUS_ACTIVE;
        $url->save();

        return $url;
    }

    protected function getSource()
    {
        return ScrapingSource::select('id')->where('name', $this->identifier)->first()->toArray();
    }

    protected function clearPhp($data)
    {
        $data = preg_replace('/<!--(.|\s)*?-->/', '', $data);
        return preg_replace(array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('',''), $data);
    }
}
