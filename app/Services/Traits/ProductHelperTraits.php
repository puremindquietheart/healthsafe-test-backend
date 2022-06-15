<?php

namespace App\Services\Traits;

trait ProductHelperTraits {

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

}
