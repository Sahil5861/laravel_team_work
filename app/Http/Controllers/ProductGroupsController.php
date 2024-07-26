<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductsGroup;

use DataTables;

class ProductGroupsController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = ProductsGroup::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == '1' ? 'checked' : '';
                    $text = $checked ? 'Active' : 'Inactive';
                    return '<label class="switch">
                                    <input type="checkbox" class="status-checkbox status-toggle" data-id="' . $row->id . '" ' . $checked . '>
                                    <span class="slider round status-text"></span>
                                    <p>'.$text.'</p>
                            </label>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                    <a href="#" class="text-body" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="' . route('admin.grouprelation.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="'.route('admin.grouprelation.delete', $row->id).'" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }

        return view('admin.pages.productGroup.index');
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


    public function deleteSelected(Request $request){
        $selectedProductGroups = $request->input('selected_product_groups');
        if (!empty($selectedProductGroups)) {
            ProductsGroup::whereIn('id', $selectedProductGroups)->delete();
            return response()->json(['success' => true, 'message' => 'Selected Product Groups deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No Product Groups selected for deletion.']);
    }
}
