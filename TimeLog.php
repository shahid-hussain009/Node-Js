<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
class TimeLog extends Model
{
    protected $table = 'tms_time_logs';
    public $primaryKey = 'id';

    protected $fillable = ['user_id', 'type', 'status'];

    public static function onlineDuration($socket_id,$date)
    {
		$times = DB::table('tms_time_logs')
				->select('created_at')
				->where('socket_id',$socket_id)
				->whereDate("created_at",$date)
				->get();
		if(count($times) > 0)
		{
			$start = Carbon::parse($times[0]->created_at);
	    	$end = Carbon::parse($times[count($times)-1]->created_at);
	        $dif = $start->diff($end);
	        $hours = round(($dif->d *24*60) +($dif->h)+($dif->m*60),2); 
			//return  $dif->d." Day ".$dif->h." Hour".$dif->m." Minute ".$dif->s." Sec";
			return $hours;
		}
		return 0;
    	
    }
     /*public static function onlineDuration($socket_id)
    {
    	$dates = DB::table('tms_time_logs')
    				->select(DB::raw('Date(created_at) as date'))
    				->where('socket_id',$socket_id)
    				->groupBy(DB::raw('Date(created_at)'))
    				->get();
    	$durations = [];
    	foreach ($dates as $key => $value) 
    	{
    		$times = DB::table('tms_time_logs')
    				->select('created_at')
    				->where('socket_id',$socket_id)
    				->whereDate("created_at",$value->date)
    				->get();
	    	$start = Carbon::parse($times[0]->created_at);
	    	$end = Carbon::parse($times[count($times)-1]->created_at);
	        $dif = $start->diff($end);
	        $hours = round(($dif->d *24*60) +($dif->h)+($dif->m*60),2); 
	        $dates[$key]->hours = $hours;
    	}
    	
    	return $dates;
    }*/
}
