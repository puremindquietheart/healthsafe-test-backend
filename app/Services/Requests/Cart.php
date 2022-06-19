<?php

namespace App\Services\Requests;

use App\Models\Product;

class Cart {

    /*
        $request = request payload
        Purpose: compute cart items
    */
    public function computeCartItems($request)
    {
        // // $productIds = array_map(function($item){
        // //     return $item['id'];
        // // }, $request->items);

        // // $products = Product::whereIn('id', $productIds)->get()->toArray();

        // will use foreach instead to retain the order of cart product
        $deletedProducts = $cartItems = [];
        $cartTotalPrice  = $totalQty  = 0;

        foreach ($request->items as $key => $item) {
            // get product info then convert it to array for easier modification
            $product = Product::select('name', 'image', 'price')->where('id', $item['id'])->first();
            // if product exist checking to consider deleted product in cart
            if ($product) {

                $product = $product->toArray();
                // assign quantity
                $product['quantity']    = $item['quantity'];
                // compute total price per product
                $product['total_price'] = $product['price'] * $item['quantity'];
                // manage total price and qty here
                $totalQty       = $totalQty + $item['quantity'];
                $cartTotalPrice = $cartTotalPrice + $product['total_price'];

                $cartItems[] = $product;
            } else {
                $deletedProducts[] = $key;
            }

        }

        return [
            'cart_items'    => $cartItems,
            'deleted_items' => $deletedProducts,
            'other_data'    => [
                'total_qty'   => $totalQty,
                'total_price' => number_format($cartTotalPrice, 2)
            ]
        ];

    }

}
