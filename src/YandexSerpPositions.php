<?php

namespace ParsingBy\YandexSerp;

use ParsingBy\YandexSerp\Models\YandexSerpPositionsModel;


class YandexSerpPositions
{
    private $model;

    public function __construct()
    {
        $this->model = new YandexSerpPositionsModel();
    }

    public function add($keyword_id, $region_id, $device_type)
    {
        $id = $this->model->add($keyword_id, $region_id, $device_type);
        if(!is_numeric($id)) return false;

        return $id;
    }
    
}
