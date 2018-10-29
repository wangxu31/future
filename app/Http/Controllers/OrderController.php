<?php

namespace App\Http\Controllers;

use App\Enums\OrderType;
use App\Jobs\ProcessOrder;
use App\Tools\Tools;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    //
	public function index(Request $request)
	{
		$merchantNo = $request->input("merchant_no");
		return $this->getOrders($merchantNo);
	}

	private function getOrders($merchantNo)
	{
		$orderList = [];
		$orders = Order::where('merchant_no', $merchantNo)
			->orderBy("id", "DESC")
			->limit(5)
			->get();

		foreach ($orders as $order) {
			$orderList[$order->sales_no] = [
				'id' => $order->id,
				'source' => OrderType::ORDER_TYPE_DICT[$order->source],
				'amount' => Tools::CentToYuan($order->amount),
				'created_at' => Tools::CarbonToDateTimeString($order->created_at),
				'updated_at' => Tools::CarbonToDateTimeString($order->updated_at)
			];
		}
		return $orderList;
	}

	public function getOrder(Request $request)
	{
		$merchantNo = $request->input("merchant_no");
		$salesNo = $request->input("sales_no");

		if ($merchantNo && $salesNo) {
			return Order::where("merchant_no", $merchantNo)
				->where("sales_no", $salesNo)
				->firstOrFail();
		} else {
			return Order::findOrFail(3);
		}
	}

	public function store(Request $request)
	{
		// 表单验证

		$order = new Order();

		$res = false;
		if ($request->merchant_no) {
			$order->merchant_no = $request->merchant_no;
			$order->amount = rand(1, 2500);
			$order->status = 1;
			$order->source = rand(1, 2);
			$order->sales_no = $this->generateSalesNo($request->merchant_no);
			$res = $order->save();
			if ($res) {
				$result = ProcessOrder::dispatch($order)->onConnection("redis")->onQueue("order_process");
//					->delay(now()->addSeconds(3));
				print_r($result);
			}
		}
		return [
			"data" => $res
		];
	}

	private function generateSalesNo($merchantNo)
	{
		$time = date("YmdHis", Carbon::now()->getTimestamp());
		$rand = substr(md5($time . $merchantNo . rand(0, 10000)), 0, 5);
		return sprintf("E%s%s%s", $time, $merchantNo, $rand);
	}
}
