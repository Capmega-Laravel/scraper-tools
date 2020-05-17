<?php
namespace Capmega\BlogScraping\Drivers;

use Goutte\Client;
use Capmega\BlogScraping\Drivers\BaseDriver;

/**
 *
 */
class ExampleDriver extends BaseDriver
{
    protected $url        = 'http://quotes.toscrape.com/page/1/';
    protected $identifier = 'quotes';

    protected function parseData($data)
    {
        $clean_data = [];
        $counter = 0;

        $data->filter('.container > .row > .col-md-8 > .quote')->each(function ($node) use (&$counter, &$clean_data) {
            $node->filter('span:first-child')->each(function ($node_a) use (&$counter, &$clean_data){
                $clean_data[$counter]['description'] = $node_a->text();
            });

            $node->filter('.author')->each(function ($node_a) use (&$counter, &$clean_data){
                $clean_data[$counter]['title'] = $node_a->text();
                $clean_data[$counter]['name'] = $node_a->text();
            });

            $counter++;
        });

        return $clean_data;
    }
}
