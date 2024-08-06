<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "brands";
    protected $dates = ['deleted_at'];

    protected $fillable = ['brand_name', 'image'];

    public function products()
    {
        return $this->hasMany(Products::class);
    }
}
