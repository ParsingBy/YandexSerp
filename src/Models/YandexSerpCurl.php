<?php

namespace ParsingBy\YandexSerp\Models;

use GuzzleHttp\Client;

class YandexSerpCurl
{
    private $headers = [
        'Cache-Control' =>  'max-age=0',
        'Accept'        =>  'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*//*;q=0.5',
        'Connection'    =>  'keep-alive',
        'Keep-Alive'    =>  '500',
        'Accept-Charset'    =>  'Windows-1251,utf-8;q=0.7,*;q=0.7',
        'Accept-Encoding'   =>  'gzip',
        'Accept-Language'   =>  'ru-ru,en;q=0.5'
    ];

    private $region_id = 213;

    public function getSERP($region_id, $phrase, $page = 1)
    {
        $phrase = $this->cleanPhrase($phrase);
        $this->setRegionId($region_id);
        $this->setUserAgent();

        $proxy = $this->getProxy();

        $client = \Curl::to('https://yandex.ru/yandsearch')
                ->withData(
                    ['text' => $phrase],
                    ['lr' => $this->region_id],
                    ['p' => ($page - 1)]
                )
                ->withHeaders($this->headers)
                ->returnResponseObject()
                ->withResponseHeaders();


        $client->withOption('PROXYTYPE', $proxy['proxytype']);
        $client->withOption('PROXY', $proxy['ip']);
        $client->withOption('BINARYTRANSFER', true);
        $client->withOption('FOLLOWLOCATION', true);
        $client->withOption('VERBOSE', true);
        $client->withOption('TIMEOUT', 10);
        

        try
        {
            $request = $client->get();
        }
        catch(\Exception $e)
        {
            dump($e);
        }

        if($request->status !== 200) return false;
        if(strpos($request->content, "serp-list") < 0) return false;

        //Обрабатываем HTML
        $data = $this->parseSERPHtml($request->content);

        return $data;
    }

    private function setUserAgent()
    {
        $this->headers['User-Agent'] = \Campo\UserAgent::random();
    }

    private function getProxy()
    {
        $proxy = \ProxyManager::getRandom();

        if($proxy['type'] == 'HTTP') $proxy['proxytype'] = CURLPROXY_HTTP;
        if($proxy['type'] == 'SOCKS4') $proxy['proxytype'] = CURLPROXY_SOCKS4;

        return $proxy;
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