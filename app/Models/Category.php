<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "categories";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'category_name',
    ];

    public function products()
    {
        return $this->hasMany(Products::class);
    }

}
