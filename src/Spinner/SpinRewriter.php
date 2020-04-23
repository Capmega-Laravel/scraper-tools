<?php

namespace Sdkconsultoria\BlogScraping\Spinner;

use GuzzleHttp\Client;

/**
 *
 */
class SpinRewriter
{
    public $protected_terms;
    protected $url = 'https://www.spinrewriter.com/action/api';
    protected $timeout = 150;
    protected $client;
    protected $lvl;

    function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout'  => $this->timeout,
        ]);

    }

    public function spin($text)
    {
        $response = $this->client->request('POST', '', [
            'form_params' => [
                'email_address'        => config('scraping.spinner.spinrewriter.email'),
                'api_key'              => config('scraping.spinner.spinrewriter.api_key'),
                'action'               => 'unique_variation',
                'text'                 => $text,
                'protected_terms'      => $this->protected_terms,
                'auto_protected_terms' => 'true',
                'confidence_level'     => 'high',
                'auto_sentences'       => 'false',
                'auto_paragraphs'      => 'false',
                'auto_new_paragraphs'  => 'false',
                'auto_sentence_trees'  => 'false',
                'use_only_synonyms'    => 'false',
                'reorder_paragraphs'   => 'false',
                'nested_spintax'       => 'false',

            ]
        ]);

        return json_decode($response->getBody()->getContents())->response;
    }
}
