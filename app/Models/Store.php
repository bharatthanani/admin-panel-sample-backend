<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $guarded = []; 


    public function categories(){
        return $this->belongsToMany(Category::class, 'store_categories', 'store_id', 'category_id');
    }

    public function tags(){
        return $this->belongsToMany(Tag::class, 'store_tags', 'store_id', 'tag_id');
    }
}
