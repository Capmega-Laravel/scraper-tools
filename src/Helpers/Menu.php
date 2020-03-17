<?php
namespace Sdkconsultoria\BlogScraping\Helpers;

/**
*
*/
class Menu
{
    public static function generate()
    {
        return [
            [
                'name' => __('scraping::attributes.scraping'),
                'icon' => 'cloud',
                'visible' => auth()->user()->hasRole('admin'),
                'items' => [
                    [
                        'name' => __('scraping::attributes.source.items'),
                        'icon' => 'cloud-download',
                        'url'  => 'source.index',
                        'crud' => 'source',
                    ],
                    [
                        'name' => __('scraping::attributes.url.items'),
                        'icon' => 'sellsy',
                        'url'  => 'url.index',
                        'crud' => 'url',
                    ],
                    [
                        'name' => __('scraping::attributes.target.items'),
                        'icon' => 'cloud-upload',
                        'url'  => 'target.index',
                        'crud' => 'target',
                    ],
                ]
            ]
        ];
    }
}
