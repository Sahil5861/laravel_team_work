<?php

namespace App\Http\Controllers;

use App\Models\AdditionalImage;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductsGroup;
use Illuminate\Support\Facades\Storage;
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
                        return $row->image ? asset('storage/uploads/' . $row->image) : 'No Image';
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
        $additionalImages = AdditionalImage::where('product_id', $id)->get();

        return view('admin.pages.products.edit', compact('product', 'brands', 'productGroups', 'categories', 'additionalImages'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'product_group_id' => 'required|integer',
            'description' => 'nullable|string',
            'offer_price' => 'nullable|numeric',
            'offer_expiry' => 'nullable|date',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = new Products();
        $product->fill($request->only([
            'name',
            'price',
            'category_id',
            'brand_id',
            'product_group_id',
            'description',
            'offer_price'
        ]));
        $product->offer_expiry = $request->input('offer_expiry') ? Carbon::parse($request->input('offer_expiry'))->format('Y-m-d') : null;

        if ($request->hasFile('image')) {
            $imagePaths = $this->handleImageUpload($request->file('image'), 'product');
            $product->image = $imagePaths['original'];
        }

        if ($product->save()) {
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $file) {
                    $imagePaths = $this->handleImageUpload($file, 'additionalimage');
                    AdditionalImage::create([
                        'product_id' => $product->id,
                        'image' => $imagePaths['original'],
                        'image_medium' => $imagePaths['medium'],
                        'image_small' => $imagePaths['small'],
                    ]);
                }
            }

            return redirect()->route('admin.product')->with('success', 'Product created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create product.');
        }
    }

    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_additional_images' => 'nullable|array',
            'remove_additional_images.*' => 'exists:additional_images,id',
        ]);

        // Find the product
        $product = Products::findOrFail($id);

        // Update the main image if a new one is provided
        if ($request->hasFile('main_image')) {
            // Delete the old main image file
            if ($product->main_image) {
                Storage::delete('public/products/' . $product->main_image);
            }

            // Store the new main image
            $product->main_image = $request->file('main_image')->store('public/products');
        }

        // Update or delete additional images
        if ($request->has('remove_additional_images')) {
            foreach ($request->input('remove_additional_images') as $imageId) {
                $image = AdditionalImage::find($imageId);
                if ($image) {
                    // Delete the image file
                    Storage::delete('public/uploads/additional_images/' . $image->image);
                    // Delete the image record from database
                    $image->delete();
                }
            }
        }

        // Handle the new additional images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $file) {
                $imagePaths = $this->handleImageUpload($file, 'additional_images');
                AdditionalImage::create([
                    'product_id' => $product->id,
                    'image' => $imagePaths['original'],
                    'image_medium' => $imagePaths['medium'],
                    'image_small' => $imagePaths['small'],
                ]);
            }
        }

        // Update other product details
        $product->name = $request->input('name');
        // Update other fields as needed

        // Save the product
        $product->save();

        return redirect()->route('admin.product')->with('success', 'Product updated successfully');
    }


    public function deleteImage(Request $request)
    {
        $imageId = $request->input('id');

        // Find the image by its ID
        $image = AdditionalImage::find($imageId);

        if ($image) {
            // Delete the image file from storage
            Storage::delete('public/uploads/additional_images/' . $image->image);

            // Delete the image record from the database
            $image->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }


    protected function handleImageUpload($image, $folder)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $basePath = 'public/uploads/' . $folder . '/';
  

        // Define paths for different image sizes
        $paths = [
            'original' => $basePath . 'original/' . $imageName,
            'medium' => $basePath . 'medium/' . $imageName,
            'small' => $basePath . 'small/' . $imageName,
        ];

        // Create directories if they do not exist
        foreach (array_values($paths) as $path) {
            $directory = dirname(storage_path('app/' . $path));
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
        }

        // Save original image
        $image->move(storage_path('app/' . $basePath . 'original'), $imageName);

        // Resize and save images
        $this->resizeImage(storage_path('app/' . $paths['original']), 800, 600, storage_path('app/' . $paths['medium']));
        $this->resizeImage(storage_path('app/' . $paths['original']), 400, 300, storage_path('app/' . $paths['small']));

        return $paths;
    }


    protected function resizeImage($filePath, $width, $height, $outputPath)
    {
        list($originalWidth, $originalHeight, $type) = getimagesize($filePath);
    
        $imageResized = imagecreatetruecolor($width, $height);
    
        switch ($type) {
            case IMAGETYPE_JPEG:
                $imageSource = imagecreatefromjpeg($filePath);
                break;
    
            case IMAGETYPE_PNG:
                $imageSource = imagecreatefrompng($filePath);
                break;
    
            case IMAGETYPE_GIF:
                $imageSource = imagecreatefromgif($filePath);
                break;
                
            default:
                return; // Return if the image type is unsupported
        }
    
        // Resize and resample the image
        imagecopyresampled($imageResized, $imageSource, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
    
        // Save the resized image to the specified output path
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($imageResized, $outputPath);
                break;
    
            case IMAGETYPE_PNG:
                imagepng($imageResized, $outputPath);
                break;
    
            case IMAGETYPE_GIF:
                imagegif($imageResized, $outputPath);
                break;
        }
    
        // Free up memory
        imagedestroy($imageResized);
        imagedestroy($imageSource);
    }
    



    public function deleteSelected(Request $request)
    {
        $selectedProducts = $request->input('selected_products');
        if (!empty($selectedProducts)) {
            Products::whereIn('id', $selectedProducts)->delete();
            return response()->json(['success' => true, 'message' => 'Selected product deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No product selected for deletion.']);
    }


    public function destroy($id)
    {
        $product = Products::findOrFail($id);

        if ($product) {
            $product->delete();
            return redirect()->route('admin.product')->with('success', 'Product deleted successfully!');
        } else {
            return redirect()->route('admin.product')->with('error', 'Product not found!');
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

                    // Find IDs by names
                    $category = Category::where('category_name', $row[2])->first();
                    $brand = Brand::where('brand_name', $row[3])->first();
                    $productGroup = ProductsGroup::where('products_group_name', $row[4])->first();

                    $product = new Products();
                    $product->name = $row[0];
                    $product->price = $row[1];
                    $product->category_id = $category ? $category->id : null;
                    $product->brand_id = $brand ? $brand->id : null;
                    $product->product_group_id = $productGroup ? $productGroup->id : null;
                    $product->description = $row[5];
                    $product->offer_price = $row[6];
                    $product->offer_expiry = isset($row[7]) ? Carbon::parse($row[7])->format('Y-m-d H:i:s') : null;
                    $product->image = $row[8];

                    // Handle image (if provided)
                    if (!empty($row[8])) {
                        $imagePath = 'product/' . basename($row[8]);
                        
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
        $products = Products::with('category', 'brand', 'productGroup')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Name')
            ->setCellValue('B1', 'Price')
            ->setCellValue('C1', 'Category Name')
            ->setCellValue('D1', 'Brand Name')
            ->setCellValue('E1', 'Product Group Name')
            ->setCellValue('F1', 'Description')
            ->setCellValue('G1', 'Offer Price')
            ->setCellValue('H1', 'Offer Expiry')
            ->setCellValue('I1', 'Image');

        // Fill data
        $rowNum = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $rowNum, $product->name)
                ->setCellValue('B' . $rowNum, $product->price)
                ->setCellValue('C' . $rowNum, $product->category->category_name ?? 'N/A')
                ->setCellValue('D' . $rowNum, $product->brand->brand_name ?? 'N/A')
                ->setCellValue('E' . $rowNum, $product->productGroup->products_group_name ?? 'N/A')
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

    public function sampleFileDownloadProduct()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product_csv_sample.csv"',
        ];

        $columns = ['Name', 'Price', 'Category Name', 'Brand Name', 'Product Group Name', 'Description', 'Offer Price', 'Offer Expiry', 'Image'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
