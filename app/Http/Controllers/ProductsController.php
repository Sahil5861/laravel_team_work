<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductsGroup;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductsController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = Products::with(['category', 'brand', 'productGroup']);

                if ($request->has('status') && $request->status != '') {
                    $query->where('status', $request->status);
                }

                $data = $query->latest()->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('category_name', function ($row) {
                        return $row->category ? $row->category->name : 'N/A';
                    })
                    ->addColumn('brand_name', function ($row) {
                        return $row->brand ? $row->brand->name : 'N/A';
                    })
                    ->addColumn('product_group_name', function ($row) {
                        return $row->productGroup ? $row->productGroup->name : 'N/A';
                    })
                    ->addColumn('status', function ($row) {
                        $checked = $row->status == '1' ? 'checked' : '';
                        return '<label class="switch">
                                    <input type="checkbox" class="status-checkbox" data-id="' . $row->id . '" ' . $checked . '>
                                    <span class="slider round"></span>
                                </label>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y');
                    })
                    ->addColumn('updated_at', function ($row) {
                        return $row->updated_at->format('d M Y');
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="dropdown">
                                    <a href="#" class="text-body" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="' . route('admin.product.edit', $row->id) . '" class="dropdown-item">Edit</a>
                                        <a href="' . route('admin.product.delete', $row->id) . '" class="dropdown-item delete-button">Delete</a>
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        }

        return view('admin.pages.products.index');
    }

    public function getProducts(Request $request)
    {
        $products = Products::select(['id', 'name', 'image', 'price', 'category', 'brand', 'product_group', 'description', 'offer_price', 'offer_expiry', 'status', 'created_at', 'updated_at']);

        // Filtering based on status if provided
        if ($request->has('status')) {
            $products->where('status', $request->input('status'));
        }

        return DataTables::of($products)
            ->editColumn('image', function ($product) {
                return '<img src="' . $product->image . '" alt="Product Image" width="70">';
            })
            ->addIndexColumn()
            ->make(true);
    }


    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $productGroups = ProductsGroup::all();
        return view('admin.pages.products.create', compact('categories', 'brands', 'productGroups'));
    }

    public function edit($id)
    {
        $brands = Brand::all();
        $productGroups = ProductsGroup::all();
        $categories = Category::all();
        $product = Products::find($id);

        return view('admin.pages.products.edit', compact('product', 'brands', 'productGroups', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'product_group_id' => 'required|exists:product_groups,id',
            'description' => 'nullable|string',
            'offer_price' => 'nullable|numeric',
            'offer_expiry' => 'nullable|date',
        ]);

        $product = new Products();
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->brand_id = $request->input('brand_id');
        $product->product_group_id = $request->input('product_group_id');
        $product->description = $request->input('description', ''); // Provide default value if not present
        $product->offer_price = $request->input('offer_price');
        $product->offer_expiry = $request->input('offer_expiry');

        if ($request->hasFile('image')) {
            // Remove old image if exists
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            // Upload new image
            $image = $request->file('image');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/product');
            $image->move($destination, $imagename);

            $product->image = 'uploads/product/' . $imagename;
        }

        if ($product->save()) {
            return redirect()->route('admin.product')->with('success', 'Product saved successfully!');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_group_id' => 'nullable|exists:product_groups,id',
            'offer_price' => 'nullable|numeric',
            'offer_expiry' => 'nullable|date',
        ]);

        // Update the product fields
        $product->name = $validatedData['name'];
        $product->price = $validatedData['price'];
        $product->description = $validatedData['description'] ?? ''; // Provide default value if not present
        $product->category_id = $validatedData['category_id'];
        $product->brand_id = $validatedData['brand_id'];
        $product->product_group_id = $validatedData['product_group_id'];
        $product->offer_price = $validatedData['offer_price'];
        $product->offer_expiry = $validatedData['offer_expiry'];

        // Handle the image file
        if ($request->hasFile('image')) {
            // Remove old image if exists
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            // Upload new image
            $image = $request->file('image');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/product');
            $image->move($destination, $imagename);

            $product->image = 'uploads/product/' . $imagename;
        }

        // Save the updated product
        $product->save();

        return redirect()->route('admin.product')->with('success', 'Product updated successfully.');
    }

    public function remove(Request $request, $id)
    {
        $product = Products::find($id);

        if ($product->delete()) {
            return back()->with('success', 'Product deleted successfully!');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $product = Products::findOrFail($id);
        if ($product) {
            $product->status = $request->status;
            $product->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function deleteSelected(Request $request)
    {
        $selectedProducts = $request->input('selected_products');
        if (!empty($selectedProducts)) {
            Products::whereIn('id', $selectedProducts)->delete();
            return response()->json(['success' => true, 'message' => 'Selected products deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No products selected for deletion.']);
    }

    public function import(Request $request)
    {
        $validate = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validate == false) {
            return redirect()->back();
        }

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        if (($handle = fopen($path, 'r')) !== false) {
            fgetcsv($handle, 1000, ','); // Skip the header row

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                Products::create([
                    'name' => $data[0],
                    'brand_id' => $data[1],
                    'product_group_id' => $data[2],
                    'status' => $data[3],
                    'offer_price' => $data[4],
                    'offer_expiry' => $data[5],
                ]);
            }

            fclose($handle);
            return redirect()->back()->with('success', 'Products imported successfully!');
        }

        return redirect()->back()->with('error', 'Failed to import products.');
    }

    public function export()
    {
        $products = Products::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['ID', 'Name', 'Brand ID', 'Product Group ID', 'Status', 'Offer Price', 'Offer Expiry'],
        ]);

        foreach ($products as $product) {
            $sheet->fromArray([
                $product->id,
                $product->name,
                $product->brand_id,
                $product->product_group_id,
                $product->status,
                $product->offer_price,
                $product->offer_expiry ? $product->offer_expiry->format('d M Y') : '',
            ], null, 'A' . ($products->search($product) + 2));
        }

        $writer = new Csv($spreadsheet);
        $filename = 'products_export_' . date('Y-m-d') . '.csv';
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
