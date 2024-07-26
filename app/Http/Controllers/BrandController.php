<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Brand;

use DataTables;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Brand::query();

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
                                        <a href="' . route('admin.brand.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="' . route('admin.brand.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'status', 'image'])
                ->make(true);
        }

        return view('admin.pages.brands.index');
    }


    public function create()
    {
        $brands = Brand::whereNull('deleted_at')->get();
        return view('admin.pages.brands.create', compact('brands'));
    }

    public function edit($id)
    {
        $brands = Brand::whereNull('deleted_at')->get();
        $brand = Brand::find($id);

        return view('admin.pages.brands.edit', compact('brand', 'brands'));
    }


    public function store(Request $request)
    {

        $validate = $request->validate([
            'name' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
        ]);
        if (!empty($request->id)) {
            $brand = Brand::firstwhere('id', $request->id);
            $brand->brand_name = $request->input('name');

            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time() . '.' . $image->getClientOriginalName();
                $destination = public_path('uploads/brand');
                $image->move($destination, $imagename);

                $imagepath = 'uploads/brand/' . $imagename;
                $brand->image = $imagepath;
            }

            if ($brand->save()) {
                return back()->with('success', 'Brand Updated Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {
            $brand = new Brand();

            $brand->brand_name = $request->input('name');

            if ($request->file('image')) {
                $image = $request->file('image');
                $imagename = time() . '.' . $image->getClientOriginalName();
                $destination = public_path('uploads/brand');
                $image->move($destination, $imagename);

                $imagepath = 'uploads/brand/' . $imagename;
                $brand->image = $imagepath;
            }

            if ($brand->save()) {
                return back()->with('success', 'Brand added Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        }
    }

    public function remove(Request $request, $id)
    {
        $brand = Brand::firstwhere('id', $request->id);

        if ($brand->delete()) {
            return back()->with('success', 'Brand deleted Suuccessfully !!');
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }


    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $brand = Brand::findOrFail($id);
        if ($brand) {
            $brand->status = $request->status;
            $brand->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }

    }



    public function deleteSelected(Request $request)
    {
        $selectedBrands = $request->input('selected_brands');
        if (!empty($selectedBrands)) {
            Brand::whereIn('id', $selectedBrands)->delete();
            return response()->json(['success' => true, 'message' => 'Selected brands deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No brands selected for deletion.']);
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
                Brand::create([
                    'id' => $data[0],
                    'brand_name' => $data[1],
                    'image' => $data[2],
                ]);
            }

            fclose($handle);
        }

        return redirect()->route('admin.brand')->with('success', 'Brands imported successfully.');

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

                // Fetch brands based on status
                $query = Brand::query();
                if ($status !== null) {
                    $query->where('status', $status);
                }
                $brands = $query->get();
                $brandsData = [];
                foreach ($brands as $brand) {
                    $brandsData[] = [
                        $brand->id,
                        $brand->brand_name,
                        $brand->image,
                        $brand->created_at->format('d M Y'),
                        $brand->status == 1 ? 'Active' : 'Inactive',
                    ];
                }
                $sheet->fromArray($brandsData, null, 'A2');

                // Write CSV to output
                $writer = new Csv($spreadsheet);
                $writer->setUseBOM(true);
                $writer->save('php://output');
            });

            // Set headers for response
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="brands.csv"');

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




}
