<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;
//延迟任务（Delayed Job）
class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order,$delay)
    {
        $this->order = $order;
        //设置延迟时间,delay()方法的参数代表多少秒后执行
        $this->delay($delay);
    }

    //当队列处理器从队列中取出任务时,会调用handle()方法
    public function handle()
    {
        //判断对应订单是否被支付
        //如果已经支付则不需要关闭订单,直接他退出
        if ($this->order->paid_at) {
            return;
        }
        //通过事务执行sql
        \DB::transaction(function(){
            //将订单closed字段改为true,即关闭订单
            $this->order->update(['closed' => true]);

            //还原库存
            foreach ($this->order->items as $key => $item) {
                $item->productSku->addStock($item->amount);
            }
        });
    }
}
