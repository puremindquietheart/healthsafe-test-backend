<?php

namespace App\Services\Requests;

use App\Models\Product as ProductModel;
use App\Services\Traits\ProductHelperTraits;

class Product {

    use ProductHelperTraits;

    /*
        $request = request payload
        Purpose: fetch all product
    */
    public function getList($request)
    {

        $products = new ProductModel;

        if ($request->has('search') && $request->search != "") {

            $search = $request->search;

            $products = $products->where('name', 'LIKE', "%{$search}%");

        }

        $products = $products->where('stock', '>', 0)->orderBy($request->sort_by, $request->sort_order)->paginate($request->limit)->toArray();

        return $this->manageProducts($products);
    }

    /*
        $id      = product id
        Purpose: fetch specific product
    */
    public function findById($id)
    {
        return ProductModel::findOrFail($id);
    }

    /*
        $request = request payload
        Purpose: save new product
    */
    public function store($request)
    {
        return ProductModel::firstOrCreate(
            ['name' => $request->name],
            $this->prepareStoreParameters($request)
        );

    }

    /*
        $id      = product id
        $request = request payload
        Purpose: Update existing product
    */
    public function update($id, $request)
    {

        $product = ProductModel::findOrFail($id);

        $request->merge([
            'current_image' => $product->image
        ]);

        if ($product->name != $request->name) {

            // check if name already exist
            if ($this->checkByField('name', $request->name)) return false;

        }

        return ProductModel::where('id', $id)->update($this->prepareUpdateParameters($request));

    }

    /*
        $id = product id
        Purpose: delete product
    */
    public function destroy($id)
    {
        $product = ProductModel::findOrFail($id);

        return $product->delete();

    }

    /*
        $field = table field
        $value = field value
        Purpose: check if value exist in specific field
    */
    private function checkByField($field, $value)
    {
        return ProductModel::where($field, $value)->get()->count();
    }

}
