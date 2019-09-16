<?php

namespace ParsingBy\YandexSerp\Models;
use Illuminate\Database\Eloquent\Model;

class YandexSerpPositionsModel extends Model
{
	protected $table = 'yandex_serp_positions';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
	
	public function add($keyword_id, $region_id, $device_type)
	{
		try
		{
			$id = \DB::table($this->table)->insertGetId(array(
				'keyword_id' => $keyword_id,
				'region_id' => $region_id,
				'device_type' => $device_type
			));
			return $id;
		}
		catch(\Exception $e)
		{
			return array('error' => $e->getMessage());
		}	
	}	
}
