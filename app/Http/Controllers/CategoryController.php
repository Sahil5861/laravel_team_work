<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Category;

use DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Category::query();

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            $data = $query->latest()->get();

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
                                        <a href="' . route('admin.category.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="' . route('admin.category.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'status', 'image'])
                ->make(true);
        }

        return view('admin.pages.categories.index');
    }


    public function create()
    {
        $categories = Category::whereNull('deleted_at')->get();
        return view('admin.pages.categories.create', compact('categories'));
    }

    public function edit($id)
    {
        $categories = Category::whereNull('deleted_at')->get();
        $category = Category::find($id);

        return view('admin.pages.categories.edit', compact('category', 'categories'));
    }


    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
        ]);
    
        if (!empty($request->id)) {
            $category = Category::find($request->id);
            if (!$category) {
                return back()->with('error', 'Category not found!');
            }
    
            $category->category_name = $request->input('name');
    
            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time() . '.' . $image->getClientOriginalName();
                $destination = public_path('uploads/category');
                $image->move($destination, $imagename);
    
                $category->image = 'uploads/category/' . $imagename;
            }
    
            if ($category->save()) {
                return redirect()->route('admin.category')->with('success', 'Category ' . $request->id . ' Updated Successfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {
            $category = new Category();
            $category->category_name = $request->input('name');
    
            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time() . '.' . $image->getClientOriginalName();
                $destination = public_path('uploads/category');
                $image->move($destination, $imagename);
    
                $category->image = 'uploads/category/' . $imagename;
            }
    
            if ($category->save()) {
                return redirect()->route('admin.category')->with('success', 'Category added Successfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        }
    }
    

    public function remove(Request $request, $id)
    {
        $category = Category::firstwhere('id', $request->id);

        if ($category->delete()) {
            return back()->with('success', 'Category deleted Suuccessfully !!');
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }


    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $category = Category::findOrFail($id);
        if ($category) {
            $category->status = $request->status;
            $category->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }

    }



    public function deleteSelected(Request $request)
    {
        $selectedCategories = $request->input('selected_categories');
        if (!empty($selectedCategories)) {
            Category::whereIn('id', $selectedCategories)->delete();
            return response()->json(['success' => true, 'message' => 'Selected category deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No category selected for deletion.']);
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
            $header = fgetcsv($handle, 1000, ','); // Skip the header row

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                Category::create([
                    'id' => $data[0],
                    'category_name' => $data[1],
                    'image' => $data[2],
                ]);
            }

            fclose($handle);
        }

        return redirect()->route('admin.category')->with('success', 'Categories imported successfully.');

    }

    public function export(Request $request)
    {
        try {
            $status = $request->query('status', null); // Get status from query parameters

            $response = new StreamedResponse(function () use ($status) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Add headers for CSV
                $sheet->fromArray(['ID', 'Name', 'Image', 'Created At', 'Status'], null, 'A1');

                // Fetch category based on status
                $query = Category::query();
                if ($status !== null) {
                    $query->where('status', $status);
                }
                $categories = $query->get();
                $categoriesData = [];
                foreach ($categories as $category) {
                    $categoriesData[] = [
                        $category->id,
                        $category->category_name,
                        $category->image,
                        $category->created_at->format('d M Y'),
                        $category->status == 1 ? 'Active' : 'Inactive',
                    ];
                }
                $sheet->fromArray($categoriesData, null, 'A2');

                // Write CSV to output
                $writer = new Csv($spreadsheet);
                $writer->setUseBOM(true);
                $writer->save('php://output');
            });

            // Set headers for response
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="categories.csv"');

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function sampleFileDownloadCategory()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="category_csv_sample.csv"',
        ];

        $columns = ['ID', 'Name', 'Image', 'Created At', 'Status'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
