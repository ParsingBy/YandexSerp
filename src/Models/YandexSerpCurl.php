<?php

namespace ParsingBy\YandexSerp\Models;

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
    private $per_page = 10;

    public function getSERP($region_id, $phrase, $page = 1)
    {
        $phrase = $this->cleanPhrase($phrase);
        $this->setRegionId($region_id);
        $this->setUserAgent();

        $proxy = $this->getProxy();

        $client = \Curl::to('https://yandex.ru/yandsearch')
                ->withData(array(
                    'text' => $phrase,
                    'lr' => $this->region_id,
                    'p' => ($page - 1)
                ))
                ->withHeaders($this->headers)
                ->returnResponseObject();


        $client->withOption('PROXYTYPE', $proxy['proxytype']);
        $client->withOption('PROXY', $proxy['ip']);
        $client->withOption('BINARYTRANSFER', true);
        $client->withOption('FOLLOWLOCATION', true);
        $client->withOption('SSL_VERIFYHOST', false);
        $client->withOption('SSL_VERIFYPEER', false);
        $client->withOption('ENCODING', 'gzip');
        //$client->withOption('VERBOSE', true);
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
        if(strpos($request->content, "serp-list") < -1) return false;

        //Обрабатываем HTML
        $data = $this->parseSERPHtml($request->content);

        $data = $this->setResultsPositions($data, $page);

        return $data;
    }

    public function parseSERPHtml($html)
    {
        $dom = \HTMLDomParser::str_get_html($html);

        $list = $dom->find('ul.serp-list')[0]->find('li.serp-item');

        $return = array();

        foreach($list as $item)
        {
            if(strpos($item->innertext, 'label_border-radius_20">реклама') !== false) continue;
            if(!$item->find('.organic__url-text')) continue;

            $url = $item
                ->find('a')[0]
                ->attr['href'];

            $title = $item
                ->find('.organic__url-text')[0]
                ->innertext;
            $title_text = $item
                ->find('.organic__url-text')[0]
                ->text();

            $description = $item
                ->find('.extended-text__full')[0]
                ->innertext();
            $description_text = $item
                ->find('.extended-text__full')[0]
                ->text();
            $description_text = str_replace("Скрыть","", $description_text);
            $description_text = trim($description_text);
            
            $return['organic'][] = array(
                'url' => $url,
                'title' => array(
                    'raw' => $title,
                    'clean' => $title_text
                ),
                'description' => array(
                    'raw' => $description,
                    'clean' => $description_text
                )
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

    private function setResultsPositions($data, $page)
    {
        $new = array();

        $position = 1 + ($page - 1) * $this->per_page;

        foreach($data['organic'] as $organic)
        {
            $new['organic'][$position] = $organic;
            $position++;
        }

        return $new;
    }

}