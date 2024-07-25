<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;

use DataTables;

class BrandController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = Brand::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    return $row->image ? asset($row->image) : '';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == '1' ? 'checked' : '';
                    $text = $checked ? 'Active' : 'Inactive';
                    return '<label class="switch">
                                    <input type="checkbox" class="status-checkbox status-toggle" data-id="' . $row->id . '" ' . $checked . '>
                                    <span class="slider round status-text"></span>
                            </label>';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                    <a href="#" class="text-body" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="' . route('admin.brand.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="'.route('admin.brand.delete', $row->id).'" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'status', 'image'])
                ->make(true);
        }

        return view('admin.pages.brands.index');
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
        if ($brand) {
            $brand->status = $request->status;
            $brand->save();
            return response()->json(['success' => true])->with('success', 'Status Updated');
        }
        else{
            return response()->json(['success' => false])->with('error', 'Somethin Went Wrong');
        }

    }



}
