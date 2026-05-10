<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class storeTag extends Model
{
    use HasFactory; 

     protected $table = 'store_tags';
    protected $guarded = [];
}
