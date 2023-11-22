<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class ProductModel extends BaseModel
{
    use HasFactory;

    public function getAllProducts($page = 1, $perPage = 10, $search = null)
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT *
        FROM products";

        $bindings = [];

        if ($search !== null) {
            $query .= " WHERE name LIKE ?";
            $searchTerm = '%' . $search . '%';
            $bindings[] = $searchTerm;
        }

        $query .= " ORDER BY CASE WHEN status = 'OUT_OF_STOCK' THEN 1 ELSE 0 END, status ASC
        LIMIT ? OFFSET ?";

        $bindings[] = $perPage;
        $bindings[] = $offset;

        $data = DB::select($query, $bindings);

        $res = collect($data)->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'description' => $product->description,
                'image' => $this->urlImage($product->image),
                'status' => $product->stock < 1 ? "OUT_OF_STOCK" : $product->status,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return $res;
    }

    public function getSingleProduct($idProduct)
    {
        $query = "SELECT * FROM products WHERE products.id = ?";

        $product = DB::select($query, [$idProduct]);

        if (!empty($product)) {
            $product = (object) $product[0];
            $res = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'description' => $product->description,
                'image' => $this->urlImage($product->image),
                'status' => $product->stock < 1 ? "OUT_OF_STOCK" : $product->status,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];

            return $res;
        } else {
            return null;
        }
    }

    public function countProducts()
    {
        return  DB::table('products')->count();
    }

    public function addProduct($data, $path)
    {
        $query = "INSERT INTO products (name, price, stock, description, image, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['name'],
            $data['price'],
            $data['stock'],
            $data['description'],
            $path,
            $data['stock'] < 1 ? "OUT_OF_STOCK" : "ACTIVE",
            now(),
            now(),
        ];

        DB::insert($query, $params);
        $productId = DB::getPdo()->lastInsertId();
        $res = DB::select("SELECT * FROM products WHERE id = $productId");
        $res = (object) $res[0];
        return $res;
    }

    public function editProduct($data)
    {
        $query = "UPDATE products 
    SET name = ?, price = ?, stock = ?, description = ?, status = ?
    WHERE id = ?";
        $bindings = [
            $data['name'],
            $data['price'],
            $data['stock'],
            $data['description'],
            $data['stock'] < 1 ? "OUT_OF_STOCK" : "ACTIVE",
            $data['id']
        ];

        DB::update($query, $bindings);

        $res = DB::select("SELECT * FROM products WHERE id = ?", [$data['id']]);
        if (!empty($res)) {
            $res = (object) $res[0];
            return $res;
        } else {
            return null;
        }
    }

    public function deleteProduct($idProduct)
    {
        $query = "DELETE FROM `products` WHERE products.id = ?";
        DB::delete($query, [$idProduct]);
        return true;
    }
}
