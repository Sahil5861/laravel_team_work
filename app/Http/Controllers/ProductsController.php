<?php

namespace App\Http\Controllers;
use App\models\Products;
use App\models\Brand; 
use App\models\Category;
use App\models\ProductsGroup;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(){
        $products = Products::whereNull('deleted_at')->get();

        return view('admin.pages.products.index', compact('products'));
    }


    public function create(){
        $brands = Brand::whereNull('deleted_at')->get();
        $categories = Category::whereNull('deleted_at')->get();
        $productgroups = ProductsGroup::whereNull('deleted_at')->get();
        return view('admin.pages.products.create', compact('brands', 'categories', 'productgroups'));
    }

}
