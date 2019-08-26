<?php

namespace ParsingBy\YandexSerp;

use Models\YandexSerpModel;

class YandexSerp
{
	private $model;

	public function __construct()
	{
		$this->model = new YandexSerpModel();
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
    		'result' => $db->result,
    		'status' => $db->status
    	);
    }
}
