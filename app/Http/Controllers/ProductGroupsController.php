<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductsGroup;

class ProductGroupsController extends Controller
{
    public function index(){
        $productsgroups = ProductsGroup::whereNull('deleted_at')->get();
        foreach ($productsgroups as $item) {
            if ($item->status == 1) {
                $status = 'Active';
            }
            else{
                $status = 'Inactive';
            }
        };
        return view('admin.pages.productGroup.index', compact('productsgroups'));
    }

    public function create(){
        return view('admin.pages.productGroup.create');
    }

    public function edit($id){
        $productsgroup = ProductsGroup::find($id);
        
        return view('admin.pages.productGroup.edit', compact('productsgroup'));
    }

    
    public function store(Request $request){
        
        $validate = $request->validate([
            'name' => 'required',
        ]);

        if ($validate == false) {
            return back()->with('error', 'Required Feild');
        }
        if (!empty($request->id)) {
            $productsgroup = ProductsGroup::firstwhere('id', $request->id);
            $productsgroup->productsgroup_name = $request->input('name');

            if ($productsgroup->save()) {
                return back()->with('success', 'Productsgroup Updated Suuccessfully !!');
            }
            else{
                return back()->with('error', 'Something went wrong !!');
            }
        }
        else{
            $productsgroup = new ProductsGroup();

            $productsgroup->products_group_name = $request->input('name');

            if ($productsgroup->save()) {
                return back()->with('success', 'Productsgroup added Suuccessfully !!');
            }
            else{
                return back()->with('error', 'Something went wrong !!');
            }
        }    
    }

    public function remove(Request $request ,$id){
        $productsgroup = ProductsGroup::firstwhere('id', $request->id);

        if ($productsgroup->delete()) {
            return back()->with('success', 'Productsgroup deleted Suuccessfully !!');
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

        $productsgroup = ProductsGroup::findOrFail($id);
        $productsgroup->status = $request->status;
        $productsgroup->save();

        return response()->json(['success' => true]);
    }
}
