<?php

namespace App\Http\Controllers;

use App\models\Products;
use App\models\Brand;
use App\models\Category;
use App\models\ProductsGroup;

use Illuminate\Http\Request;

use DataTables;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Products::latest()->get();
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
                                    <p>' . $text . '</p>
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
                                        <a href="#" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Info
                                        </a>
                                        <a href="' . route('admin.products.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="' . route('admin.products.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'status', 'image'])
                ->make(true);
        }


        return view('admin.pages.products.index');
    }


    public function create()
    {
        $brands = Brand::whereNull('deleted_at')->get();
        $categories = Category::whereNull('deleted_at')->get();
        $productgroups = ProductsGroup::whereNull('deleted_at')->get();
        return view('admin.pages.products.create', compact('brands', 'categories', 'productgroups'));
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amount' => 'required|numeric|min:0',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'product_group' => 'required|string|max:255',
        ]);

        if (!empty($request->id)) {
            $product = Products::firstwhere('id', $request->id);
            $product->name = $request->input('name');
            $product->price = $request->input('amount');
            $product->brand_id = $request->input('brand');
            $product->category_id = $request->input('category');
            $product->product_group_id = $request->input('product_group');
            $product->description = $request->input('desc');

            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time() . '.' . $image->getClientOriginalName();
                $destination = public_path('uploads/products');
                $image->move($destination, $imagename);

                $imagepath = 'uploads/products/' . $imagename;
                $product->image = $imagepath;
            }

            if ($product->save()) {
                return back()->with('success', 'Brand Updated Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {
            $product = new Products();

            $product->name = $request->input('name');
            $product->price = $request->input('amount');
            $product->brand_id = $request->input('brand');
            $product->category_id = $request->input('category');
            $product->product_group_id = $request->input('product_group');
            $product->description = $request->input('desc');

            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time() . '.' . $image->getClientOriginalName();
                $destination = public_path('uploads/products');
                $image->move($destination, $imagename);

                $imagepath = 'uploads/products/' . $imagename;
                $product->image = $imagepath;
            }

            if ($product->save()) {
                return back()->with('success', 'Product added Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        }
    }







    public function remove(Request $request, $id)
    {
        $product = Products::firstwhere('id', $request->id);

        if ($product->delete()) {
            return back()->with('success', 'Product deleted Suuccessfully !!');
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }


    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $product = Products::findOrFail($id);
        $product->status = $request->status;
        $product->save();

        return response()->json(['success' => true]);
    }

    public function deleteSelected(Request $request)
    {
        $selectedProducts = $request->input('selected_products');
        if (!empty($selectedProducts)) {
            Products::whereIn('id', $selectedProducts)->delete();
            return response()->json(['success' => true, 'message' => 'Selected Products deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No Products selected for deletion.']);
    }

}
