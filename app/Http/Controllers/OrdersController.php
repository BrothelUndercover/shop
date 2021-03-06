<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
    	
    	$user = $request->user();
    	//开启一个数据库事务
    	$order = \DB::transaction(function() use ($user,$request){
    		$address = UserAddress::find($request->input('address_id'));
    		//更新此地址的最后使用时间
    		$address->update(['last_used_at' => Carbon::now()]);
    		//创建一个订单
    		$order = new Order([
    			'address'	=> [
    				'address'	=>	$address->full_address,
    				'zip'		=>	$address->zip,
    				'contact_name'	=>	$address->contact_name,
    				'contact_phone'	=>	$address->contact_phone, 
    			],
    			'remark'	=>	$request->input('remark'),
    			'total_amount'	=> 0,
    		]);
    		//订单关联到当前用户
    		$order->user()->associate($user);

    		$order->save();

    		$totalAmount = 0;

    		$items = $request->input('items');

    		//遍历用提交的sku
    		foreach ($items as $key => $data) {
    			$sku = ProductSku::find($data['sku_id']);
    			//创建一个OrderItem 并直接与当前订单关联
    			$item = $order->items()->make(['amount' => $data['amount'],'price'=> $sku->price]);
    			$item->product()->associate($sku->product_id);
    			$item->productSku()->associate($sku);
    			$item->save();
    			$totalAmount += $sku->price*$data['amount'];
    			if ($sku->decreaseStock($data['amount']) <= 0) {
    			        throw new InvalidRequestException('该商品库存不足');
    			}
    		}

    		//更新订单金额
    		$order->update(['total_amount' => $totalAmount]);

    		$skuIds =  collect($items)->pluck('sku_id');
    		$user->cartItems()->whereIn('product_sku_id',$skuIds)->delete();

    		//触发延时任务
    		$this->dispatch(new CloseOrder($order,config('app.order_ttl')));
    		return $order;
    	});

    	return $order;
    }
}
