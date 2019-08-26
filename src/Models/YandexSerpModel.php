<?php

namespace ParsingBy\YandexSerp\Models;
use Illuminate\Database\Eloquent\Model;

class YandexSerpModel extends Model
{
	protected $table = 'parsing_by_yandex_serp';
    protected $primaryKey = 'id';
    public $timestamps = false;
	
	public function add($data = array())
	{
		try
		{
			$id = \DB::table($this->table)->insertGetId($data);
			return $id;
		}
		catch(\Exception $e)
		{
			return array('error' => $e->getMessage());
		}	
	}	
	
	public function edit($id, $data = array())
	{
		try
		{
			\DB::table($this->table)
				->where('id','=', $id)
				->update($data);
			return true;
		}
		catch(\Exception $e)
		{
			return array('error' => $e->getMessage());
		}	
	}

}
