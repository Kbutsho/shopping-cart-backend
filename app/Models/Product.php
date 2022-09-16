<?php

namespace App\Models;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable=[
        'name',
        'quantity',
        'category',
        'price',
        'details',
        'sellerName',
        'sellerPhone',
        'sellerId',
        'image',
    ];
    public function featuredImages(){
        return $this->hasMany(ProductImage::class,'productId');
    }
}
