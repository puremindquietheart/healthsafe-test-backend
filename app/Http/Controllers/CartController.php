<?php

namespace App\Http\Controllers;
// Illuminate Helpers
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
// Requests Helpers
use App\Services\Requests\Cart;

class CartController extends Controller
{
    private $cartRequest;

    public function __construct(Cart $cart)
    {
        $this->cartRequest = $cart;
    }
    /*
        $request = request payload
    */
    public function computeCartItems(Request $request)
    {
        try {

            $request->validate([
                'items'            => 'required|array',
                'items.*.id'       => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric'
            ]);

            $response = $this->cartRequest->computeCartItems($request);

            return response()->json([
                'success'         => true,
                'server_response' => "ok",
                'data'            => $response['cart_items'],
                'other_data'      => $response['other_data']
            ], 200);

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
}
