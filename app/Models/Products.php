<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "products";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'price',
        'image',
        'description',
        'category_id',
        'brand_id',
        'product_group_id',
        'status',
        'offer_price',
        'offer_expiry'
    ];

    protected $casts = [
        'offer_expiry' => 'datetime',
    ];

    // In Product model
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function productGroup()
    {
        return $this->belongsTo(ProductsGroup::class);
    }

}
