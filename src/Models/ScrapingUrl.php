<?php

namespace Capmega\BlogScraping\Models;

use Sdkconsultoria\Base\Models\ResourceModel;

class ScrapingUrl extends ResourceModel
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public static function rules($params)
    {
        return [
            'scraping_source_id' => 'required',
            'url'                => 'required',
            'driver'             => 'required',
            'name'               => 'required',
        ];
    }

    /**
     * Get client attributes values.
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = parent::attributes();
        return array_merge($attributes, [
            'scraping_source_id' => __('scraping::attributes.url.scraping_source_id'),
            'url' => __('scraping::attributes.url.url'),
            'driver' => __('scraping::attributes.url.driver'),
            'scraping_category_id' => __('scraping::attributes.url.scraping_category_id'),
        ]);
    }

    /**
     * Get attributes for search.
     *
     * @return array
     */
    public function getFiltersAttribute()
    {
        $attributes = parent::getFiltersAttribute();
        return array_merge([
        ], $attributes);
    }

    /**
     * Save item
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    public function save(array $options = [])
    {
        $this->generateSeoname();
        parent::save($options);
    }

    public function category()
    {
        return $this->belongsTo('Capmega\BlogScraping\Models\ScrapingCategory', 'scraping_category_id', 'id');
    }

    public function source()
    {
        return $this->belongsTo('Capmega\BlogScraping\Models\ScrapingSource', 'scraping_source_id', 'id');
    }

    public function data()
    {
        return $this->hasOne('Capmega\BlogScraping\Models\ScrapingData', 'scraping_url_id', 'id');
    }
}
