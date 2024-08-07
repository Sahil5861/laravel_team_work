<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductsGroup extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "product_groups";
    protected $dates = ['deleted_at'];

    protected $fillable = ['products_group_name'];

    public function products()
    {
        return $this->hasMany(Products::class);
    }
    
}
