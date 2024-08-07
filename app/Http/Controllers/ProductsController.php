<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductsGroup;
use DataTables;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = Products::with('category', 'brand', 'productGroup')->latest();
                if ($request->has('status') && $request->status != '') {
                    $query->where('status', $request->status);
                }

                $data = $query->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function ($row) {
                        $checked = $row->status == '1' ? 'checked' : '';
                        $text = $checked ? 'Active' : 'Inactive';
                        return '<label class="switch">
                                        <input type="checkbox" class="status-checkbox status-toggle" data-id="' . $row->id . '" ' . $checked . '>
                                        <span class="slider round status-text"></span>
                                </label>';
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="dropdown">
                                        <a href="#" class="text-body" data-bs-toggle="dropdown">
                                            <i class="ph-list"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="' . route('admin.product.edit', $row->id) . '" class="dropdown-item">
                                                <i class="ph-pencil me-2"></i>Edit
                                            </a>
                                            <a href="' . route('admin.product.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                                <i class="ph-trash me-2"></i>Delete
                                            </a>
                                        </div>
                                    </div>';
                    })
                    ->addColumn('category', function ($row) {
                        return $row->category->category_name ?? 'N/A';
                    })
                    ->addColumn('brand', function ($row) {
                        return $row->brand->brand_name ?? 'N/A';
                    })
                    ->addColumn('product_groups', function ($row) {
                        return $row->productGroup->products_group_name ?? 'N/A';
                    })
                 
                    ->addColumn('offer_expiry', function ($row) {
                        return $row->offer_expiry ? $row->offer_expiry->format('d M Y') : 'N/A';
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y');
                    })
                    ->addColumn('description', function ($row) {
                        return \Illuminate\Support\Str::words($row->description, 10, '...');
                    })
                    ->addColumn('updated_at', function ($row) {
                        return $row->updated_at->format('d M Y');
                    })
                    ->addColumn('image', function ($row) {
                        return $row->image ? asset($row->image) : 'No Image';
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
        $products = Products::query();

        return DataTables::of($products)
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('admin.product.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>';
            })
            ->addColumn('category', function ($row) {
                return $row->category ? $row->category->name : 'N/A';
            })
            ->addColumn('brand', function ($row) {
                return $row->brand ? $row->brand->name : 'N/A';
            })
            ->addColumn('product_groups', function ($row) {
                return $row->productGroups->pluck('name')->implode(', ');
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d F Y');
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d F Y');
            })
            ->make(true);
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
            'description' => 'required',
            'offer_price' => 'nullable|numeric',
            'offer_expiry' => 'nullable|date',
        ]);

        $product = new Products();
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->brand_id = $request->input('brand_id');
        $product->product_group_id = $request->input('product_group_id');
        $product->description = $request->input('description');
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
    public function deleteSelected(Request $request)
    {
        $selectedPlans = $request->input('selected_products');
        if (!empty($selectedPlans)) {
            Products::whereIn('id', $selectedPlans)->delete();
            return response()->json(['success' => true, 'message' => 'Selected product deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No product selected for deletion.']);
    }



    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_group_id' => 'nullable|exists:product_groups,id',
            'offer_price' => 'nullable|numeric',
            'offer_expiry' => 'nullable|date',
        ]);

        // Update the product fields
        $product->name = $validatedData['name'];
        $product->price = $validatedData['price'];
        $product->description = $validatedData('description');
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
        $product = Products::firstwhere('id', $request->id);

        if ($product->delete()) {
            return back()->with('success', 'Product deleted Suuccessfully !!');
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }

    public function import(Request $request)
    {
        if ($request->hasFile('csv_file')) {
            try {
                $file = $request->file('csv_file');
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                foreach ($data as $index => $row) {
                    if ($index === 0)
                        continue; // Skip header row

                    $product = new Products();
                    $product->name = $row[0];
                    $product->price = $row[1];
                    $product->category_id = $row[2];
                    $product->brand_id = $row[3];
                    $product->product_group_id = $row[4];
                    $product->description = $row[5];
                    $product->offer_price = $row[6];
                    $product->offer_expiry = isset($row[7]) ? Carbon::parse($row[7])->format('Y-m-d H:i:s') : null;
                    $product->image = $row[8];

                    // Handle image (if provided)
                    if (!empty($row[8])) {
                        $imagePath = 'uploads/product/' . basename($row[8]);
                        $product->image = $imagePath;
                    }

                    $product->save();
                }

                return redirect()->route('admin.product')->with('success', 'Products imported successfully!');
            } catch (\Exception $e) {
                return back()->with('error', 'Error importing CSV: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'No file uploaded.');
    }




    public function export(Request $request)
    {
        $products = Products::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Name')
            ->setCellValue('B1', 'Price')
            ->setCellValue('C1', 'Category ID')
            ->setCellValue('D1', 'Brand ID')
            ->setCellValue('E1', 'Product Group ID')
            ->setCellValue('F1', 'Description')
            ->setCellValue('G1', 'Offer Price')
            ->setCellValue('H1', 'Offer Expiry')
            ->setCellValue('I1', 'Image');

        // Fill data
        $rowNum = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $rowNum, $product->name)
                ->setCellValue('B' . $rowNum, $product->price)
                ->setCellValue('C' . $rowNum, $product->category_id)
                ->setCellValue('D' . $rowNum, $product->brand_id)
                ->setCellValue('E' . $rowNum, $product->product_group_id)
                ->setCellValue('F' . $rowNum, $product->description)
                ->setCellValue('G' . $rowNum, $product->offer_price)
                ->setCellValue('H' . $rowNum, $product->offer_expiry ? $product->offer_expiry->format('Y-m-d') : '')
                ->setCellValue('I' . $rowNum, $product->image);
            $rowNum++;
        }

        $writer = new Csv($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="products.csv"');

        return $response;
    }
}
