<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //创建30条数据
        $products = factory(App\Models\Product::class,30)->create();

        foreach ($products as $key => $product) {
        	$sku = factory(App\Models\ProductSku::class,3)->create(['product_id'=>$product->id]);

        	$product->update(['price'=> $sku->min('price')]);
        }
    }
}
