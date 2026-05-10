<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function productColors() 
    {
        return $this->belongsToMany(Color::class, 'product_colors', 'product_id', 'color_id');
    }

    public function productImages()
    {
        return $this->belongsToMany(Image::class, 'product_images', 'product_id', 'image_id');
    }

    public function stores(){
        return $this->hasOne(Store::class,'id','store_id');
    }
}
