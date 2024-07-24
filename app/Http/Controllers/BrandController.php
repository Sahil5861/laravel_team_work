<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index(){
        $brands = Brand::whereNull('deleted_at')->get();
        foreach ($brands as $item) {
            if ($item->status == 1) {
                $status = 'Active';
            }
            else{
                $status = 'Inactive';
            }
        };
        return view('admin.pages.brands.index', compact('brands'));
    }


    public function create(){
        $brands = Brand::whereNull('deleted_at')->get();
        return view('admin.pages.brands.create', compact('brands'));
    }

    public function edit($id){
        $brands = Brand::whereNull('deleted_at')->get();
        $brand = Brand::find($id);
        
        return view('admin.pages.brands.edit', compact('brand', 'brands'));
    }


    public function store(Request $request){
        
        $validate = $request->validate([
            'name' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
        ]);
        if (!empty($request->id)) {
            $brand = Brand::firstwhere('id', $request->id);
            $brand->brand_name = $request->input('name');

            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time().'.'.$image->getClientOriginalName();
                $destination = public_path('uploads/brand');
                $image->move($destination, $imagename);
    
                $imagepath = 'uploads/brand/'.$imagename;
                $brand->image = $imagepath;
            }

            if ($brand->save()) {
                return back()->with('success', 'Brand Updated Suuccessfully !!');
            }
            else{
                return back()->with('error', 'Something went wrong !!');
            }
        }
        else{
            $brand = new Brand();

            $brand->brand_name = $request->input('name');
    
            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time().'.'.$image->getClientOriginalName();
                $destination = public_path('uploads/brand');
                $image->move($destination, $imagename);
    
                $imagepath = 'uploads/brand/'.$imagename;
                $brand->image = $imagepath;
            }
    
            if ($brand->save()) {
                return back()->with('success', 'Brand added Suuccessfully !!');
            }
            else{
                return back()->with('error', 'Something went wrong !!');
            }
        }    
    }

    public function remove(Request $request ,$id){
        $brand = Brand::firstwhere('id', $request->id);

        if ($brand->delete()) {
            return back()->with('success', 'Brand deleted Suuccessfully !!');
        }
        else{
            return back()->with('error', 'Something went wrong !!');
        }
    }


    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->status = $request->status;
        $brand->save();

        return response()->json(['success' => true]);
    }



}
