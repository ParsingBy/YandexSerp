<?php

namespace ParsingBy\YandexSerp\Models;

use GuzzleHttp\Client;

class YandexSerpCurl
{
	private $options = [
		'headers' => [
	        'Cache-Control'	=>	'max-age=0',
	        'Accept'		=> 	'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*//*;q=0.5',
	        'Connection'	=>	'keep-alive',
	        'Keep-Alive'	=>	'500',
	        'Accept-Charset'	=>	'Windows-1251,utf-8;q=0.7,*;q=0.7',
	        'Accept-Encoding'	=>	'gzip',
	        'Accept-Language'	=>	'ru-ru,en;q=0.5'
	    ]
	];

	private $region_id = 213;

	public function getSERP($region_id, $phrase, $page = 1)
    {
    	$phrase = $this->cleanPhrase($phrase);
    	$this->setRegionId($region_id);
    	$this->setUserAgent();

    	$client = $this->client();
    	
    	return $client->get('yandsearch', 
    		['query' => 
    			['text' => $phrase],
    			['lr' => $this->region_id],
    			['p' => ($page - 1)]
    		]);
    }

    private function client()
    {
 		return new Client([
		    'base_uri' => 'https://yandex.ru/',
		    'timeout'  => 15.0,
		    'proxy' => $this->getProxy()
		]);   	
    }

    private function setUserAgent()
    {
    	$this->options['headers']['User-Agent'] = \Campo\UserAgent::random();
    }

    private function getProxy()
    {
    	$proxy = \ProxyManager::getRandom();
    	return $proxy['type'] . "://" . $proxy['ip'];
    }

    private function setRegionId($region_id)
    {
    	$this->region_id = $region_id;
    }

    private function cleanPhrase($phrase)
    {
    	$phrase = str_replace(" ","+",$phrase);
    	return $phrase;
    }

}