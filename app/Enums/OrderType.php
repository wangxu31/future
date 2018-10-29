<?php

namespace App\Enums;

class OrderType
{
	const ONLINE_SALE = 1;
	const THIRD_PARTY_SALE = 2;

	const ORDER_TYPE_DICT = [
		self::ONLINE_SALE => '线上商城',
		self::THIRD_PARTY_SALE => '外部',
	];
}
