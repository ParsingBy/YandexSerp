<?php

namespace ParsingBy\YandexSerp\Models;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

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
                ->returnResponseObject();


        $client->withOption('PROXYTYPE', $proxy['proxytype']);
        $client->withOption('PROXY', $proxy['ip']);
        $client->withOption('BINARYTRANSFER', true);
        $client->withOption('FOLLOWLOCATION', true);
        $client->withOption('ENCODING', 'gzip');
       // $client->withOption('VERBOSE', true);
        $client->withOption('TIMEOUT', 7);
        

        try
        {
            $request = $client->get();
        }
        catch(\Exception $e)
        {
            dump($e);
        }

        if($request->status !== 200) return false;
        if(strpos($request->content, "serp-list") < -1) return false;

        \Log::error($request->content);

        //Обрабатываем HTML
        $data = $this->parseSERPHtml($request->content);

        return $data;
    }

    public function parseSERPHtml($html)
    {
        $dom = (new Dom)->load($html);

        $list = $dom->find('ul.serp-list')[0]->find('li.serp-item');

        dump('count=' . count($list));

        $return = array();

        foreach($list as $item)
        {
            if(strpos($item->innerHtml, 'label_border-radius_20">реклама') !== false) continue;
            $url = $item
                ->find('a')[0]
                ->getAttribute('href');
            $title = $item
                ->find('.organic__url-text')[0]
                ->innerHtml;
            $description = $item
                ->find('.extended-text__full')[0]
                ->innerHtml;
            
            $return['organic'][] = array(
                'url' => $url,
                'title' => $title,
                'description' => $description
            );
        }

        return $return;
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