<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Models\Category;

use Yajra\DataTables\Facades\DataTables;

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

    // -----------------------------Data table-------------------------------------------
    public function getData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start", 0);
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
            ->where('category_name', 'like', '%' . $searchValue . '%')
            ->count();

        $records = Category::orderBy($columnName, $columnSortOrder)
            ->where('category_name', 'like', '%' . $searchValue . '%')
            ->orWhere(function ($query) use ($searchValue) {
                $query->where('category_name', 'like', '%' . $searchValue . '%');
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
                                <a href="' . route('admin.category.edit', $record->id) . '" class="dropdown-item">
                                    Edit
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="' . route('admin.category.delete', $record->id) . '" class="dropdown-item">
                                    Delete
                                </a>
                            </div>',
                "image" => '<img src="' . asset($record->image) . '" alt="image" width="100px">',
                "category_name" => $record->category_name,

                "status" => '<label class="form-check form-switch form-check-reverse" style="display:flex; justify-content:space-between; width:50%;">
                <input type="checkbox" class="status-checkbox form-check-input" data-id="' . $record->id . '" ' . $statusChecked . ' id="status' . $record->id . '" style="cursor:pointer;">
                ' . $statusLabel . '
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

        if ($validate == false) {
            return back()->with('error', 'Required Feild');
        }
        if (!empty($request->id)) {
            $category = Category::firstwhere('id', $request->id);
            $category->category_name = $request->input('name');
            $category->parent_category = $request->input('parent_category');

            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time() . '.' . $image->getClientOriginalName();
                $destination = public_path('uploads/category');
                $image->move($destination, $imagename);

                $imagepath = 'uploads/category/' . $imagename;
                $category->image = $imagepath;
            }

            if ($category->save()) {
                return back()->with('success', 'Category Updated Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {
            $category = new Category();

            $category->category_name = $request->input('name');
            $category->parent_category = $request->input('parent_category');

            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time() . '.' . $image->getClientOriginalName();
                $destination = public_path('uploads/category');
                $image->move($destination, $imagename);

                $imagepath = 'uploads/category/' . $imagename;
                $category->image = $imagepath;
            }

            if ($category->save()) {
                return back()->with('success', 'Category added Suuccessfully !!');
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
        $category->status = $request->status;
        $category->save();

        return response()->json(['success' => true]);
    }

    public function deleteSelected(Request $request)
    {
        $selectedCategories = $request->input('selected_categories');
        if (!empty($selectedCategories)) {
            Category::whereIn('id', $selectedCategories)->delete();
            return response()->json(['success' => true, 'message' => 'Selected categories deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No categories selected for deletion.']);
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

        return redirect()->route('admin.category')->with('success', 'Category imported successfully.');

    }


    public function export(Request $request)
    {
        $status = $request->query('status', '');

        $query = Category::query();

        if ($status !== '') {
            $query->where('status', $status);
        }

        $categories = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add header row
        $sheet->setCellValue('A1', 'Id')
            ->setCellValue('B1', 'Category Name')
            ->setCellValue('C1', 'Image')
            ->setCellValue('D1', 'Created At')
            ->setCellValue('E1', 'Status');

        // Add data rows
        $row = 2;
        foreach ($categories as $category) {
            $sheet->setCellValue('A' . $row, $category->id)
                ->setCellValue('B' . $row, $category->category_name)
                ->setCellValue('C' . $row, $category->image)
                ->setCellValue('D' . $row, $category->created_at->format('d M Y'))
                ->setCellValue('E' . $row, $category->status == 1 ? 'Active' : 'Inactive');
            $row++;
        }

        $writer = new Csv($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="categories.csv"');

        return $response;
    }
}

