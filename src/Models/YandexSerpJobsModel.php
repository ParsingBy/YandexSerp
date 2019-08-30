<?php

namespace ParsingBy\YandexSerp\Models;
use Illuminate\Database\Eloquent\Model;

class YandexSerpJobsModel extends Model
{
	protected $table = 'parsing_by_yandex_serp_jobs';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        'result' => 'array'
    ];
	
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

	public function scopeNew($query, $take)
	{
		return $query
			->where($this->table . '.status', '=' , 'new')
			->inRandomOrder()
			->take($take);
	}	

	public function yandexserp()
    {
        return $this->belongsTo('ParsingBy\YandexSerp\Models\YandexSerpModel', 'data_id', 'id');
    }	
}
