<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    //列表
    public function index(Request $request)
    {
    	 // 创建一个查询构造器
        $builder = Product::query()->where('on_sale', true);
        // 判断是否有提交 search 参数，如果有就赋值给 $search 变量
        // search 参数用来模糊搜索商品
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            // 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }
    	if ($request->tt) {
    		dd($builder->toSql());
    	}
    	//是否有提交order参数,如果有赋值给$order
    	if ($order = $request->input('order',' ')) {
    		//如果是以_asc或者_desc
    		if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
    			$builder->orderBy($m[1],$m[2]);
    		}
    	}

    	$products = $builder->paginate(16);
    	
    	return view('products.index',['products'=>$products,'filters'=>['search'=>$search,'order'=>$order]]);
    }
}