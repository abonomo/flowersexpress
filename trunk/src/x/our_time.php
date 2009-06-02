<?php

//custom time related functions
class OurTime
{
	public function js_to_datetime($js_date, $time)
	{
		//convert mm/dd/yyyy to YYYY-MM-DD HH:MM:SS
		$first_slash_pos = strpos($js_date, '/');
		$second_slash_pos = strpos(substr($js_date, $first_slash_pos+1), '/') + $first_slash_pos+1;
		
		$month = substr($js_date, 0, $first_slash_pos);
		$day = substr($js_date, $first_slash_pos+1, $second_slash_pos-$first_slash_pos-1);
		$year = substr($js_date, $second_slash_pos+1);
		
		if ($time == 0)
			return "$year-$month-$day 00:00:00";
		else 
			return "$year-$month-$day 23:59:59"; 
	}
	
	public function datetime_to_js($datetime)
	{
		//convert YYYY-MM-DD to mm/dd/YYYY
		$first_dash_pos = strpos($datetime, '-');
		$second_dash_pos = strpos(substr($datetime, $first_dash_pos+1), '-') + $first_dash_pos + 1;
				
		$year = substr($datetime, 0, $first_dash_pos);
		$month = substr($datetime, $first_dash_pos+1, 2);
		$day = substr($datetime, $second_dash_pos+1, 2);
		
		return "$month/$day/$year";
	}
}
?>