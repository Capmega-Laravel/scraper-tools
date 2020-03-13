<?php
namespace Sdkconsultoria\BlogScraping\Drivers;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Sdkconsultoria\Blog\Models\{Blog , BlogPost};

/**
 *
 */
abstract class BaseDriver
{
    protected $timeout = 60;
    protected $method  = 'GET';
    protected $limit   = 10;
    protected $identifier;
    protected $blog_id;

    abstract protected function parseData($data);

    function __construct() {
        $this->findBlog();
    }

    public function getData()
    {
        $client     = new Client(HttpClient::create(['timeout' => $this->timeout]));
        $parse_data = $this->parseData($client->request($this->method, $this->url));

        foreach ($parse_data as $key => $data) {
            if ($key < $this->limit) {
                $this->insertBlogPost($data);
            }else{
                break;
            }
        }
    }

    protected function insertBlogPost(array $data)
    {
        $blog_post                   = new BlogPost();
        $blog_post->blog_id          = $this->blog_id;
        $blog_post->created_by       = 1;
        $blog_post->status           = BlogPost::STATUS_ACTIVE;
        $blog_post->name             = $data['name']??'';
        $blog_post->title            = $data['title']??'';
        $blog_post->subtitle         = $data['subtitle']??'';
        $blog_post->meta_author      = $data['meta_author']??'';
        $blog_post->meta_description = $data['meta_description']??'';
        $blog_post->description      = $data['description']??'';
        $blog_post->save();
    }

    protected function findBlog()
    {
        $blog = Blog::where('identifier', $this->identifier)->first();
        if (!$blog) {
            $blog              = new Blog();
            $blog->identifier  = $this->identifier;
            $blog->name        = $this->identifier;
            $blog->created_by  = 1;
            $blog->status      = Blog::STATUS_ACTIVE;
            $blog->description = 'Created automatically by BlogScraping';
            $blog->save();
        }
        $this->blog_id = $blog->id;
    }
}
