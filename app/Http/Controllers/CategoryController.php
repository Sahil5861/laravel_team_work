<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::whereNull('deleted_at')->get();
        foreach ($categories as $item) {
            if ($item->parent_category == null) {
                $item->parent_category = 'Not set';
            }
            if ($item->status == 1) {
                $status = 'Active';
            }
            else{
                $status = 'Inactive';
            }
        };
        return view('admin.pages.categories.index', compact('categories'));
    }

    // -----------------------------Data table-------------------------------------------
    public function getData(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start",0);
        $rowperpage = $request->get("length", 10); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name

        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $totalRecords = Category::select('count(*) as allcount')->count();

        // total records with filter
        $totalRecordswithFilter = Category::select('count(*) as allcount')
        ->where('category_name', 'like', '%'.$searchValue.'%')
        ->count();

        $records = Category::orderBy($columnName, $columnSortOrder)
        ->where('category_name', 'like', '%'.$searchValue.'%')
        ->orWhere(function($query) use ($searchValue){
            $query->where('category_name', 'like', '%'.$searchValue.'%');
        })
        ->select('*')
        ->skip($start)
        ->take($rowperpage)
        ->get();

        $dataArr = array();

        foreach ($records as $record) {
            $statusLabel = $record->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>';
            $statusChecked = $record->status == 1 ? 'checked' : '';
            $dataArr[] = array(
                "id" => $record->id,
                "action" => '<button type="button" class="btn text-white" data-bs-toggle="dropdown" data-color-theme="dark" aria-expanded="false" style="border: none;">
                                <i class="ph-list me-2"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="'.route('admin.category.edit', $record->id).'" class="dropdown-item">
                                    Edit
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="'.route('admin.category.delete', $record->id).'" class="dropdown-item">
                                    Delete
                                </a>
                            </div>',
                "image" => '<img src="'.asset($record->image).'" alt="image" width="100px">',
                "category_name" => $record->category_name,

                "status" => '<label class="form-check form-switch form-check-reverse" style="display:flex; justify-content:space-between; width:50%;">
                <input type="checkbox" class="status-checkbox form-check-input" data-id="'.$record->id.'" '.$statusChecked.' id="status'.$record->id.'" style="cursor:pointer;">
                '.$statusLabel.'
            </label>',
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter, // Corrected: 'iTotalDisplayRecords' instead of 'irecordsfiltered'
            "aaData" => $dataArr, // Corrected: 'aaData' instead of 'data'
        );

        return response()->json($response);
    }


    // -----------------------------Data table Ends-------------------------------------------

    public function create(){
        $categories = Category::whereNull('deleted_at')->get();
        return view('admin.pages.categories.create', compact('categories'));
    }

    public function edit($id){
        $categories = Category::whereNull('deleted_at')->get();
        $category = Category::find($id);
        
        return view('admin.pages.categories.edit', compact('category', 'categories'));
    }

    
    public function store(Request $request){
        
        $validate = $request->validate([
            'name' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validate == false) {
            return back()->with('error', 'Required Feild');
        }
        if (!empty($request->id)) {
            $category = Category::firstwhere('id', $request->id);
            $category->category_name = $request->input('name');
            $category->parent_category = $request->input('parent_category');

            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time().'.'.$image->getClientOriginalName();
                $destination = public_path('uploads/category');
                $image->move($destination, $imagename);
    
                $imagepath = 'uploads/category/'.$imagename;
                $category->image = $imagepath;
            }

            if ($category->save()) {
                return back()->with('success', 'Category Updated Suuccessfully !!');
            }
            else{
                return back()->with('error', 'Something went wrong !!');
            }
        }
        else{
            $category = new Category();

            $category->category_name = $request->input('name');
            $category->parent_category = $request->input('parent_category');
    
            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time().'.'.$image->getClientOriginalName();
                $destination = public_path('uploads/category');
                $image->move($destination, $imagename);
    
                $imagepath = 'uploads/category/'.$imagename;
                $category->image = $imagepath;
            }
    
            if ($category->save()) {
                return back()->with('success', 'Category added Suuccessfully !!');
            }
            else{
                return back()->with('error', 'Something went wrong !!');
            }
        }    
    }

    public function remove(Request $request ,$id){
        $category = Category::firstwhere('id', $request->id);

        if ($category->delete()) {
            return back()->with('success', 'Category deleted Suuccessfully !!');
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

        $category = Category::findOrFail($id);
        $category->status = $request->status;
        $category->save();

        return response()->json(['success' => true]);
    }
}
