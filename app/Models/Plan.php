<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "plans";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'special_price',
        'expiry_date',
        'status',
    ];
    protected $casts = [
        'expiry_date' => 'date',
        'price' => 'decimal:2',
        'special_price' => 'decimal:2',
    ];

}
