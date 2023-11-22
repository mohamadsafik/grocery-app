<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartModel;
use Illuminate\Support\Facades\Validator;

class Cart extends ApiController
{
    protected $cart;

    public function __construct(CartModel $cart)
    {
        $this->cart = $cart;
    }

    public function getCart(Request $request)
    {
        $idUser = $request->query('id_user');

        $cart = $this->cart->getCart($idUser);

        if ($cart == null) {
            return $this->apiResponseList(false, 200, "Cart Empty");
        }
        return $this->apiResponseList(true, 200, "success get cart", $cart);
    }

    public function addToCart(Request $request)
    {
        $idUser = $request->input('id_user');
        $idProduct = $request->input('id_product');
        $quantity = $request->input('quantity');
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'id_product' => 'required',
            'quantity' => 'required|integer',
        ]);

        $errorMessage = collect($validator->getMessageBag()->toArray())->flatten()->implode(' ');

        if (!$validator->fails()) {
            $res =   $this->cart->addToCart($idUser, $idProduct, $quantity);
            if ($res == false) {
                return $this->apiResponse(false, 400, "Quantity exceeds available stock, check your cart");
            }
            return $this->apiResponse(true, 200, "Success add to cart");
        } else {
            return $this->apiResponse(true, 400, $errorMessage);
        }
    }

    public function removeFromCart(Request $request)
    {
        $data = $this->cart->removeFromCart($request['id']);
        return $this->apiResponse(true, 200, 'berhasil hapus product');
    }

    public function paymentSimulation(Request $request)
    {
        $cartIds = $request->input('cart_ids', []);


        $this->cart->payment($cartIds);


        return $this->apiResponse(true, 200, "success payment",);
    }
}
