<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalImage extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'image', 'image_medium', 'image_small'];

    // AdditionalImage.php
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

}
