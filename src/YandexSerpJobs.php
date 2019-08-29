<?php

namespace ParsingBy\YandexSerp;

use Models\YandexSerpJobsModel;
use Models\YandexSerpCurl;

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
        $db = $this->model->yandexserp->new(10)
        dd($db);
        if(empty($db)) return false;

        foreach($db as $db_item)
        {
            $return = $this->curl->getSERP();
        }
    }    

}
