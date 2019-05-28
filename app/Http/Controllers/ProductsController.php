<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    //列表
    public function index(Request $request)
    {
    	$products = Product::query()->where('on_sale',true)->paginate(16);

    	return view('products.index',['products'=>$products]);
    }
}
