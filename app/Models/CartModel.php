<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class CartModel extends BaseModel
{
    use HasFactory;

    public function getCart($idUser)
    {
        $query =  "SELECT pd.*, ct.quantity, ct.id AS id_cart
        FROM carts ct
        LEFT JOIN users ON users.id = ct.id_user
        LEFT JOIN products pd ON pd.id = ct.id_product WHERE users.id = ?";

        $data = DB::select($query, [$idUser]);
        if ($data != null) {
            return
                collect($data)->map(function ($cart) {
                    return [
                        'id' => $cart->id,
                        'id_cart' => $cart->id_cart,
                        'name' => $cart->name,
                        'price' => $cart->price,
                        'stock' => $cart->stock,
                        'description' => $cart->description,
                        'image' => $this->urlImage($cart->image),
                        'status' => $cart->stock < 1 ? "OUT_OF_STOCK" : $cart->status,
                        'quantity' => $cart->quantity,
                        'created_at' => $cart->created_at,
                        'updated_at' => $cart->updated_at,
                    ];
                });
        } else {
            return null;
        }
    }

    public function addToCart($idUser, $idProduct, $quantity)
    {
        $query = "SELECT carts.*, products.stock FROM carts LEFT JOIN products ON carts.id_product = products.id WHERE id_user = ? AND id_product = ?";
        $existingItem = DB::select($query, [$idUser, $idProduct]);

        if ($existingItem) {
            $newQuantity = $existingItem[0]->quantity + $quantity;
            if ($newQuantity > $existingItem[0]->stock) {
                return false;
            }
            if ($newQuantity < 1) {
                $this->removeFromCart($existingItem[0]->id);
                return true;
            }

            $query = "UPDATE carts SET quantity = ?, updated_at = ? WHERE id = ?";
            DB::update($query, [$newQuantity, now(), $existingItem[0]->id]);
            return true;
        } else {

            $query = "INSERT INTO carts (id_user, id_product, quantity, created_at, updated_at) VALUES (?, ?, ?, ?, ?)";
            DB::insert($query, [$idUser, $idProduct, $quantity, now(), now()]);
            return true;
        }
    }
    public function removeFromCart($idCart)
    {

        $query = "DELETE FROM `carts` WHERE carts.id = ?";
        DB::delete($query, [$idCart]);
        return true;
    }

    public function payment(array $cartIds)
    {
        $placeholders = implode(',', array_fill(0, count($cartIds), '?'));

        $query = "DELETE FROM `carts` WHERE carts.id IN ($placeholders)";

        DB::delete($query, $cartIds);

        return true;
    }
}
