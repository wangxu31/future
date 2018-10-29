<?php

namespace App\Tools;


use Carbon\Carbon;

class Tools
{
    public static function CarbonToDateTimeString(Carbon $time)
	{
		return Carbon::parse($time)->toDateTimeString();
	}

	public static function CentToYuan(int $amount)
	{
		return $amount/100;
	}

	public static function YuanToCent(float $amount)
	{
		return $amount*100;
	}
}
