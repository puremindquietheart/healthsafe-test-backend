<?php

namespace App\Services\Traits;
// Cloudinary API
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

trait ProductHelperTraits {

    public function prepareStoreParameters($request)
    {
        return [
            'name'  => $request->name,
            'price' => $request->price,
            'image' => $this->manageProductImage($request),
            'stock' => $request->stock
        ];
    }

    public function prepareUpdateParameters($request)
    {

        return [
            'name'  => $request->name,
            'price' => $request->price,
            'image' => $this->manageProductImage($request),
            'stock' => $request->stock
        ];
    }

    /*
        $products = array of product
        Purpose: Dissect products by data and metadata
    */
    public function manageProducts($products)
    {
        if (count($products['data'])) {

            // assign product data
            $data = $products['data'];
            // unset the product data
            unset($products['data']);
            // then assign the remaining data to $meta
            return [
                'body' => $data,
                'meta' => $products
            ];

        } else {

            return [
                'body' => null,
                'meta' => null
            ];

        }
    }

    /*
        $request = request payload
        Purpose: Handle saving of product image
    */
    public function manageProductImage($request)
    {

        return Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();

    }

}
