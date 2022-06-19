<?php

namespace App\Http\Controllers;
// Illuminate Helpers
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
// Requests Helpers
use App\Services\Requests\Product;

class ProductController extends Controller
{

    private $productRequest;

    public function __construct(Product $product)
    {
        $this->productRequest = $product;
    }

    /*
        $sort_by and $sort_order values are default as created_at and desc respectively
        $limit and $page values are default as 10 and 1 respectively
        $search value is blank by default
    */
    public function index(Request $request)
    {
        try {

            if (!$request->has('sort_by')) {

                $request->merge([
                    'sort_by'    => 'created_at',
                    'sort_order' => 'desc'
                ]);

            }

            if (!$request->has('limit')) {

                $request->merge([
                    'limit' => 10,
                    'page'  => 1
                ]);

            }

            $products = $this->productRequest->getList($request);

            return response()->json([
                'success'         => true,
                'server_response' => "ok",
                'data'            => $products['body'],
                'meta'            => $products['meta']
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success'         => false,
                'server_response' => $e->getMessage(),
                'line_error'      => $e->getLine()
            ], 500);

        }
    }

    /*
        $request = request payload
    */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'name'  => 'required|string|unique:products',
                'price' => 'required|numeric',
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'stock' => 'required|integer'
            ]);

            $product = $this->productRequest->store($request);

            return response()->json([
                'success'         => true,
                'server_response' => "ok",
                'data'            => $product
            ], 201);

        } catch (ValidationException $v) {

            return response()->json([
                'success'         => false,
                'server_response' => $v->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success'         => false,
                'server_response' => $e->getMessage(),
                'line_error'      => $e->getLine()
            ], 500);

        }
    }

    /*
        $id = product id
    */
    public function show($id)
    {
        try {

            $products = $this->productRequest->findById($id);

            return response()->json([
                'success'         => true,
                'server_response' => "ok",
                'data'            => $products
            ], 200);

        } catch (ModelNotFoundException $m) {

            return response()->json([
                'success'         => false,
                'server_response' => $m->getMessage(),
            ], 401);

        } catch (\Exception $e) {

            return response()->json([
                'success'         => false,
                'server_response' => $e->getMessage(),
                'line_error'      => $e->getLine()
            ], 500);

        }
    }

    /*
        $id      = product id
        $request = request payload
    */
    public function update($id, Request $request)
    {
        try {
            // for some reason, boolean value from frontend is converting the value from boolean type to string. so, this filter function is necessary
            $request->merge([
                'is_new' => filter_var($request->is_new,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)
            ]);

            $validationRule = [
                'name'   => 'required|string',
                'price'  => 'required|numeric',
                'stock'  => 'required|integer',
                'is_new' => 'required|boolean'
            ];

            if ($request->is_new) {

                $validationRule['image'] = 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048';

            }

            $request->validate($validationRule);

            $product = $this->productRequest->update($id, $request);

            if (!$product) {

                throw ValidationException::withMessages(['name' => 'The name has already been taken.']);

            }

            return response()->json([
                'success'         => true,
                'server_response' => "ok",
                'data'            => $product
            ], 200);

        } catch (ModelNotFoundException $m) {

            return response()->json([
                'success'         => false,
                'server_response' => $m->getMessage(),
            ], 401);

        } catch (ValidationException $v) {

            return response()->json([
                'success'         => false,
                'server_response' => $v->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success'         => false,
                'server_response' => $e->getMessage(),
                'line_error'      => $e->getLine()
            ], 500);

        }
    }

    /*
        $id = product id
    */
    public function destroy($id)
    {
        try {

            $product = $this->productRequest->destroy($id);

            return response()->json([
                'success'         => true,
                'server_response' => "ok",
                'data'            => $product
            ], 200);

        } catch (ModelNotFoundException $m) {

            return response()->json([
                'success'         => false,
                'server_response' => $m->getMessage(),
            ], 401);

        } catch (\Exception $e) {

            return response()->json([
                'success'         => false,
                'server_response' => $e->getMessage(),
                'line_error'      => $e->getLine()
            ], 500);

        }
    }

}
