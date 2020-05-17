<?php
namespace Capmega\BlogScraping\Controllers;

use Illuminate\Http\Request;
use Sdkconsultoria\Base\Controllers\Controller;
use Capmega\BlogScraping\Drivers\ExampleDriver;
use Capmega\BlogScraping\Spinner\SpinRewriter;
use Sdkconsultoria\Blog\Models\{Blog, BlogPost, BlogImage};

/**
 * [class description]
 */
class ScrapingController extends Controller
{
    public function index()
    {
        $post = \Capmega\BlogScraping\Models\ScrapingData::where('status', \Capmega\BlogScraping\Models\ScrapingData::STATUS_ACTIVE)->first();

        $text = $post->getDataString();
        $spinner = new SpinRewriter();
        $spinner->protected_terms = $post->protected_terms;
        $post->string_spin = $spinner->spin($text);
        $post->getDataHtml($post->html);
        // dump($text);
        // echo ($post->description);
        // dump($post->string_spin);
        // echo ($post->html_spin);
        dd('');

        // // echo($post->description);
        // // dump('');
        // // echo($post->html_spin);
        // // dd('');
        //
        // $spinner = new SpinRewriter();
        // $spinner->protected_terms = $post->protected_terms;
        // $post->string_spin = $spinner->spin($post->getDataString());
        //
        // // echo htmlentities($post->description);
        // // echo $post->description;
        // dd($result);
        // echo htmlentities($result);
        // echo $result;
        // dd('gg');
        // $example = new ExampleDriver();
        // $example->getData();
    }

    public function catchCategory(Request $request)
    {
        $category = $request->input('name');
        $parent   = $request->input('parent');
        $blog     = Blog::where('name', $category);

        if ($parent) {
            $parent_blog = Blog::where('name', $parent)->first();
            $blog = $blog->where('parent_id', $parent_blog->id);
        }

        $blog = $blog->first();

        if (!$blog) {
            $blog = new Blog();

            if ($parent) {
                $blog->parent_id = $parent_blog->id;
            }
            $blog->sizes = serialize(config('base.images'));
            $blog->images_types = serialize(config('base.images_types'));
            $blog->status     = Blog::STATUS_ACTIVE;
            $blog->name       = $category;
            $blog->created_by = 1;
            $blog->save();
        }

        return response()->json([
            'category' => $category,
            'parent'   => $parent
        ]);
    }

    public function catchPost(Request $request)
    {
        $post     = $request->input('name');
        $category = $request->input('category');

        $blog = Blog::where('name', $category)->first();
        if ($blog) {
            $new_blog = BlogPost::where('name', $post)->where('blog_id', $blog->id)->orWhere('identifier', $request->url)->first();
            if (!$new_blog) {
                $new_blog = new BlogPost();
                $new_blog->name         = $post;
                $new_blog->images_types = serialize(config('base.images_types'));
                $new_blog->sizes        = serialize(config('base.images'));
                $new_blog->identifier   = $request->url;
                $new_blog->language     = config('app.locale');
                $new_blog->blog_id      = $blog->id;
                $new_blog->description  = $request->description;

                // $new_blog->short_description  = substr(strip_tags($request->description), 0, 300);
                $new_blog->status       = BlogPost::STATUS_ACTIVE;
                $new_blog->created_by   = 1;
                $new_blog->published_at = date('Y-m-d');
                $new_blog->save();
                $new_blog->saveKey('Spin level', $request->spin_lvl);

                $this->saveImages($new_blog, $request);
            }
        }

        return response()->json([
            'category' => $category,
            'post'   => $post
        ]);
    }

    private function saveImages($model, $request)
    {
        if($request->hasfile('images'))
        {
            foreach($request->file('images') as $file)
            {
                $extension = $file->getClientOriginalExtension();
                $filename  = $file->getClientOriginalName();

                $image = new BlogImage();
                $image->created_by   = 1;
                $image->extension    = $file->extension();
                $image->blog_post_id = $model->id;
                $image->save();
                $file->storeAs('blogs/' . $model->id, $image->id . '.' . $file->extension(), 'public');
                try {
                    $image->convertImage();
                } catch (\Exception $e) {

                }

            }
        }
    }
}
