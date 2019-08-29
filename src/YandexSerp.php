<?php

namespace ParsingBy\YandexSerp;

use ParsingBy\YandexSerp\Models\YandexSerpModel;
use ParsingBy\YandexSerp\YandexSerpJobs;

class YandexSerp
{
    private $model;

    public function __construct()
    {
        $this->model = new YandexSerpModel();
        $this->jobs = new YandexSerpJobs();
    }

    public function add($phrase, $region_id, $pages = 1)
    {
        return $this->model->add(array(
            'region_id' => $region_id,
            'phrase' => $phrase,
            'fetch_result_pages_count' => $pages
        ));
    }

    public function getResult($id)
    {
        $db = $this->model->whereId($id)->first();
        if(empty($db)) return false;

        if($status !== 'done')
        {
            return array(
                'status' => $db->status
            );
        }

        return array(
            'data' => $db->result,
            'status' => $db->status
        );
    }

    public function doCreatePagesToParse()
    {
        $db = $this->model->new(10)->get();
        if(empty($db)) return false;

        foreach($db as $db_item)
        {
            for($i = 1; $i <= $db_item->fetch_result_pages_count; $i++)
            {
                $this->jobs->add($db_item->id, $i);
            }   

            $this->model->updateStatus($db_item->id, 'in_progress');
        }
    }
}
