<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Product extends ApiController
{
    protected $product;

    public function __construct(ProductModel $product)
    {
        $this->product = $product;
    }
   
    public function index(Request $request)
    {
        $currentPage = $request->query('page', 1);
        $search = $request->query('search');

        $products = $this->product->getAllProducts($currentPage, 10, $search);
        $count = $this->product->countProducts();

        return $this->apiResponseList(true, 200, 'Success', $products, $count, 10, $currentPage);

    }
    public function single(Request $request)
    {
        $idProduct = $request->query('id');

   
        $products = $this->product->getSingleProduct($idProduct);
     
        return $this->apiResponse(true, 200, 'Success', $products);

    }
   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);
        $errorMessage = collect($validator->getMessageBag()->toArray())->flatten()->implode(' ');

        if (!$validator->fails()) {
            $image = $request->input('image');


            $hashedImageName = Str::random(40); 
    
            $imageExtension = $this->getImageExtensionFromBase64($image);
            
            $getBase64 = $this->getBase64($image);

            $binaryImage = base64_decode($getBase64);
            $path = "images/{$hashedImageName}." . $imageExtension;
            Storage::disk('public')->put($path, $binaryImage);

            $res = $this->product->addProduct($request, $path);
            return $this->apiResponse(true, 200, "success add product", $res);
        } else {
            return $this->apiResponse(false, 400, $errorMessage, null);
        }
    }
  
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);
      
        $errorMessage = collect($validator->getMessageBag()->toArray())->flatten()->implode(' ');
       
        if (!$validator->fails()) {
           

            $res = $this->product->editProduct($request);
            if($res != null){
                return $this->apiResponse(true, 200, "success edit product", $res);
            }else{
                return $this->apiResponse(false, 404, "data tidak ditemukan", null);
            }
            
            
        } else {
            return $this->apiResponse(false, 400, $errorMessage, null);
        }
    }

    public function destroy(Request $request)
    {

        $data = $this->product->deleteProduct($request['id']);
       return $this->apiResponse(true, 200,'berhasil hapus product');
    }
}
