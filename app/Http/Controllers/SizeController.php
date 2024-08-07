<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use App\Models\Size;
use DataTables;

class SizeController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = Size::query();

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            $data = $query->latest()->get();
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
                ->addColumn('created_at', function ($row) {
                    return formatDate($row->created_at);
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                    <a href="#" class="text-body" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="' . route('admin.size.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="' . route('admin.size.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.size.index');
    }
    public function create()
    {
        return view('admin.size.create');
    }

    public function edit($id)
    {
        $size = Size::findOrFail($id);
        return view('admin.size.edit', compact('size'));
    }



    public function store(Request $request)
    {

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
        ]);
        if (!empty($request->id)) {
            $size = Size::firstwhere('id', $request->id);
            $size->name = $request->input('name');
            $size->short_name = $request->input('short_name');

            if ($size->save()) {
                return redirect()->route('admin.size')->with('success', 'Size ' . $request->id . ' Updated Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {
            $size = new Size();

            $size->name = $request->input('name');
            $size->short_name = $request->input('short_name');

            if ($size->save()) {
                return redirect()->route('admin.size')->with('success', 'Size added Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        }
    }


    public function deleteSelected(Request $request)
    {
        $selectedSizes = $request->input('selected_sizes');
        if (!empty($selectedSizes)) {
            Size::whereIn('id', $selectedSizes)->delete();
            return response()->json(['success' => true, 'message' => 'Selected Sizes deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No Size selected for deletion.']);
    }


    public function remove(Request $request, $id)
    {
        $size = Size::firstwhere('id', $request->id);

        if ($size->delete()) {
            return back()->with('success', 'Size deleted Suuccessfully !!');
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }
    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $size = Size::findOrFail($id);
        if ($size) {
            $size->status = $request->status;
            $size->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }

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
                Size::create([
                    'id' => $data[0],
                    'name' => $data[1],
                    'short_name' => $data[2],
                ]);
            }

            fclose($handle);
        }

        return redirect()->route('admin.size')->with('success', 'Size imported successfully.');

    }



    public function export(Request $request)
    {
        try {
            $status = $request->query('status', null); // Get status from query parameters

            $response = new StreamedResponse(function () use ($status) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Add headers for CSV
                $sheet->fromArray(['ID', 'Name', 'Short Name', 'status'], null, 'A1');
                // Fetch Size based on status
                $query = Size::query();
                if ($status !== null) {
                    $query->where('status', $status);
                }

                $sizes = $query->get();
                $sizesData = [];
                foreach ($sizes as $size) {
                    $sizesData[] = [
                        $size->id,
                        $size->name,
                        $size->short_name,
                        $size->status,
                    ];
                }
                $sheet->fromArray($sizesData, null, 'A2');

                // Write CSV to output
                $writer = new Csv($spreadsheet);
                $writer->setUseBOM(true);
                $writer->save('php://output');
            });

            // Set headers for response
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="sizes.csv"');

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sampleFileDownloadSize()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="size_csv_sample.csv"',
        ];

        $columns = ['ID', 'Name', 'Short Name', 'status'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }



}
