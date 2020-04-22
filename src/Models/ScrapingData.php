<?php

namespace Sdkconsultoria\BlogScraping\Models;

use Sdkconsultoria\Base\Models\ResourceModel;
use Symfony\Component\DomCrawler\Crawler;

class ScrapingData extends ResourceModel
{
    private $html;

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

    public function images()
    {
        return $this->hasMany('Sdkconsultoria\BlogScraping\Models\ScrapingDataImage', 'scraping_data_id', 'id');
    }

    public function getData()
    {
        $this->html = [];

        $crawler = new Crawler($this->description);
        $crawler = $crawler->filter('div')->first();

        foreach ($crawler->children() as $domElement) {
            $this->html[] = [
                'type'       => $domElement->nodeName,
                'attributes' => $this->parseAttr($domElement->attributes??[]),
                'childs'     => $this->parse($domElement->childNodes),
                'text'       => $this->parseText($domElement),
            ];
        }

        dd($this->html);
    }

    protected function parse($elements)
    {
        $html = [];

        foreach ($elements as $domElement) {
            if ($domElement->nodeName != '#text') {
                $html[] = [
                    'type'       => $domElement->nodeName,
                    'attributes' => $this->parseAttr($domElement->attributes??[]),
                    'childs'     => $this->parse($domElement->childNodes),
                    'text'       => $this->parseText($domElement),
                ];
            }
        }

        return $html;
    }

    private function parseAttr($attributes)
    {
        $attributes_array = [];

        foreach ($attributes as $key => $attribute) {
            $attributes_array[] = [
                'name'   => $attribute->nodeName,
                'value'  => $attribute->nodeValue,
            ];
        }

        return $attributes_array;
    }

    private function parseText($domElement)
    {
        if ($domElement->nodeName == 'p' ||
            $domElement->nodeName == 'span' ||
            $domElement->nodeName == 'label' ||
            $domElement->nodeName == 'em' ||
            $domElement->nodeName == 'mark' ||
            $domElement->nodeName == 'i' ||
            $domElement->nodeName == 'b' ||
            $domElement->nodeName == 'u' ||
            $domElement->nodeName == 's' ||
            $domElement->nodeName == 'cite' ||
            $domElement->nodeName == 'small' ||
            $domElement->nodeName == 'strong') {
            return $domElement->textContent;
        }

        return '';
    }
}
