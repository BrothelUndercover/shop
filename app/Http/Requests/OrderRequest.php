<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class OrderRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 判断用户提交的地址 ID 是否存在于数据库并且属于当前用户
            // 后面这个条件非常重要，否则恶意用户可以用不同的地址 ID 不断提交订单来遍历出平台所有用户的收货地址
            'address_id'    =>  ['required',Rule::exists('user_addresses','id')->where('user_id',$this->user()->id)],
            'items'         =>  ['required','array'],
            'items.*.sku_id'=>  [   //检查items数组下每一个字数组的 sku_id 参数
                    'required',
                    function($attribute,$value,$fail){
                        if (!$sku = ProductSku::find($value)) {
                            $fail('该商品不存在');
                            return;
                        }
                        if (!$sku->product->on_sale) {
                            $fail('该商品未上架');
                            return;
                        }
                        if ($sku->stock === 0) {
                            $fail('该商品已售空');
                            return;
                        }
                        preg_match('/items\.(\d+)\.sku_id/', $attribute,$m);
                    }
            ], 
        ];
    }
}
