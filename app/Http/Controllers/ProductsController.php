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
                        $checked = $row->status ? 'checked' : '';
                        return '<label class="switch">
                                    <input type="checkbox" class="status-checkbox" data-id="' . $row->id . '" ' . $checked . '>
                                    <span class="slider round"></span>
                                </label>';
                    })
                    ->addColumn('offer_expiry', function ($row) {
                        return $row->offer_expiry->format('d M Y');
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
        $products = Products::with(['category', 'brand', 'productGroup'])->select([
            'id', 'name', 'image', 'price', 'category_id', 'brand_id', 'product_group_id', 'description', 'offer_price', 'offer_expiry', 'status', 'created_at', 'updated_at'
        ]);

        // Filtering based on status if provided
        if ($request->has('status')) {
            $products->where('status', $request->input('status'));
        }

        return DataTables::of($products)
            ->editColumn('image', function ($product) {
                return $product->image ? '<img src="' . asset($product->image) . '" alt="Product Image" width="70">' : 'No Image';
            })
            ->editColumn('offer_expiry', function ($product) {
                return $product->offer_expiry ? \Carbon\Carbon::parse($product->offer_expiry)->format('d M Y H:i') : 'No Expiry';
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
        $product = Products::findOrFail($id);

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
        $product->offer_expiry = $request->input('offer_expiry') ? \Carbon\Carbon::parse($request->input('offer_expiry'))->format('Y-m-d H:i:s') : null;

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
        $product->offer_expiry = $validatedData['offer_expiry'] ? \Carbon\Carbon::parse($validatedData['offer_expiry'])->format('Y-m-d H:i:s') : null;

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
        $product = Products::findOrFail($id);

        if ($product->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Product deleted successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong!']);
        }
    }

    public function exportCsv()
    {
        $products = Products::with(['category', 'brand', 'productGroup'])->get();

        $csvData = [];
        foreach ($products as $product) {
            $csvData[] = [
                'Name' => $product->name,
                'Category' => $product->category ? $product->category->name : 'N/A',
                'Brand' => $product->brand ? $product->brand->name : 'N/A',
                'Product Group' => $product->productGroup ? $product->productGroup->name : 'N/A',
                'Price' => $product->price,
                'Offer Price' => $product->offer_price,
                'Offer Expiry' => $product->offer_expiry ? \Carbon\Carbon::parse($product->offer_expiry)->format('d M Y H:i') : 'No Expiry',
                'Created At' => $product->created_at->format('d M Y'),
                'Updated At' => $product->updated_at->format('d M Y'),
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(array_keys($csvData[0]), NULL, 'A1');
        $sheet->fromArray($csvData, NULL, 'A2');

        $writer = new Csv($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="products.csv"');

        return $response;
    }
}
