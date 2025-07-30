<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\product;

class wishlist extends Model
{
    use HasFactory;
    public $fillable = ['user_id','product_id'];
    public function product(){
        return $this->belongsTo(product::class);
    }
}