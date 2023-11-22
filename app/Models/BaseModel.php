<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    use HasFactory;

   protected function urlImage($path){
    $pathImage = $path ==null ? "images/error.png" : $path;
    $base_url = config('app.url');// belum digunakan, pake base url dari frontend
    return $pathImage;
   }
}
