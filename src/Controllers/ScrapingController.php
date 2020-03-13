<?php
namespace Sdkconsultoria\BlogScraping\Controllers;

use Illuminate\Http\Request;
use Sdkconsultoria\Base\Controllers\Controller;
use Sdkconsultoria\BlogScraping\Drivers\ExampleDriver;

/**
 * [class description]
 */
class ScrapingController extends Controller
{
    public function index()
    {
        $example = new ExampleDriver();
        $example->getData();
    }
}
