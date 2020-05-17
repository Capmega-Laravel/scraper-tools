<?php

namespace Capmega\BlogScraping\Models;

use Sdkconsultoria\Base\Models\ResourceModel;

class ScrapingCategory extends ResourceModel
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public static function rules($params)
    {
        return [
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

    public function parent()
    {
        return $this->belongsTo('Capmega\BlogScraping\Models\ScrapingCategory', 'scraping_category_id', 'id');
    }
}
