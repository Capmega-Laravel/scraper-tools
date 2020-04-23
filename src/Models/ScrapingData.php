<?php

namespace Sdkconsultoria\BlogScraping\Models;

use Sdkconsultoria\Base\Models\ResourceModel;
use Symfony\Component\DomCrawler\Crawler;

class ScrapingData extends ResourceModel
{
    public $html;
    private $string_origin = '';
    public $string_spin = '';
    public $html_spin = '';
    private $count_tags = [];
    public $protected_terms = '';

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

    public function getDataString()
    {
        $this->parseString($this->getDataArray(), $this->string_origin);

        return $text = preg_replace("/\r|\n/", "", $this->string_origin);
    }

    private function parseString($array, &$string)
    {
        foreach ($array as $key => $value) {
            if ($value['text'] && $value['type'] != 'a') {
                $number = $this->countTags($value['type'], $this->count_tags);
                $this->protected_terms .= '-sta'.$value['type'].$number . '\n' . '-end'.$value['type'].$number . '\n';
                $string .= ' -sta'.$value['type'].$number . ' ';
                $string .= ' ' . $this->clearText($value) . ' ';
                $string .= ' -end'.$value['type'].$number . ' ';
            }

            $this->parseString($value['childs'], $string);


        }
        return $string;
    }

    private function clearText($item, $reverse = false, $count = 0)
    {
        if ($item['type'] != 'a' && !$reverse) {
            $pattern = '/:code=([#a-z-0-9A-Z\/_]+)/';
            return preg_replace($pattern, '', $item['text']);
        }

        if ($item['type'] != 'a' && $reverse) {
            $tag = $item['type'].$count;
            // $this->string_spin = $this->string_origin;

            $startsAt = strpos($this->string_spin, "-sta$tag") + strlen("-sta$tag");
            $endsAt = strpos($this->string_spin, "-end$tag", $startsAt);
            $result = substr($this->string_spin, $startsAt, $endsAt - $startsAt);
            return $result;

            // preg_match('/\-sta'.$tag.'(.+)-end'.$tag.'/', $this->string_spin, $matches);
            //
            // if ($matches[1]??false) {
            //     return $matches[1];
            // }

            return $item['text'];
        }

        return $item['text'].' ';
    }

    public function getDataArray()
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
                'path'       => $domElement->getNodePath(),
            ];
        }

        return  $this->html;
    }

    public function getDataHtml($array)
    {
        $count_tags = [];

        foreach ($array as $key => $value) {
            $this->html_spin .= '<' . $value['type'] . $this->getAttrs($value['attributes']) . '>';
            if ($value['text']) {
                $count = $this->countTags($value['type'], $count_tags);
                $this->html_spin .= ' ' . $this->clearText($value, true, $count) . ' ';
            }
            $this->getDataHtml($value['childs']);
            $this->html_spin .= '</' . $value['type'] . '>';
        }
    }

    private function getAttrs($attributes)
    {
        $string = ' ';
        foreach ($attributes as $key => $value) {
            $string .= ' '.$value['name'].'="'.$value['value'].'" ';
        }
        return $string.' ';
    }

    private function countTags($tag, &$array)
    {
        if (isset($array[$tag])) {
            $array[$tag] = $array[$tag]+1;
        }else{
            $array[$tag] = 0;
        }

        return $array[$tag];
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
                    'path'       => $domElement->getNodePath(),
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
            // $domElement->nodeName == 'label' ||
            // $domElement->nodeName == 'em' ||
            // $domElement->nodeName == 'mark' ||
            // $domElement->nodeName == 'i' ||
            // $domElement->nodeName == 'b' ||
            // $domElement->nodeName == 'u' ||
            // $domElement->nodeName == 's' ||
            // $domElement->nodeName == 'cite' ||
            // $domElement->nodeName == 'small' ||
            // $domElement->nodeName == 'strong' ||
            $domElement->nodeName == 'a') {
                return $domElement->textContent;
                // $string = str_replace(':code=', '-code-x', $domElement->textContent . ' ');
                // return str_replace('#', '-haxsh-', $string . ' ');
        }

        return '';
    }
}
