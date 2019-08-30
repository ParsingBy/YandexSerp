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

    public function delete($id)
    {   
        return $this->model->find($id)->delete();
    }


    public function doParsePages()
    {
        $db = $this->model->with('yandexserp')->new(10)->get();
        if(empty($db)) return false;

        foreach($db as $db_item)
        {
            $try = 1;
            while($try < 100)
            {
                $return = $this->curl->getSERP($db_item->yandexserp->region_id, $db_item->yandexserp->phrase, $db_item->page);
                
                if(!$return)
                {
                    $try++;
                    continue;
                }

                $try = 1000;

                $this->model->find($db_item->id)->update(['result' => $return]);
                $this->model->find($db_item->id)->update(['status' => 'done']);
            }
        }
    } 
}
