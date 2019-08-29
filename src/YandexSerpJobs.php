<?php

namespace ParsingBy\YandexSerp;

use ParsingBy\YandexSerp\Models\YandexSerpJobsModel;
use ParsingBy\YandexSerp\Models\YandexSerpCurl;

class YandexSerpJobs
{
    private $model;

    public function __construct()
    {
        $this->model = new YandexSerpJobsModel();
        $this->curl = new YandexSerpCurl();
    }

    public function add($data_id, $page = 1)
    {
        return $this->model->add(array(
            'data_id' => $data_id,
            'page' => $page
        ));
    }

    public function doParsePages()
    {
        $db = $this->model->with('yandexserp')->new(1)->get();
        if(empty($db)) return false;

        foreach($db as $db_item)
        {
            $try = 1;
            while($try < 70)
            {
                $return = $this->curl->getSERP($db_item->yandexserp->reqion_id, $db_item->yandexserp->phrase, $db_item->page);
                
                dump('try='.$try."\n");
                if(!$return)
                {
                    $try++;
                    continue;
                }

                $try = 1000;
                dump($return);
            }
        }
    }    

}
