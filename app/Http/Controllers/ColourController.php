<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use App\Models\Colour;
use DataTables;

class ColourController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = Colour::query();

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
                                        <a href="' . route('admin.colour.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="' . route('admin.colour.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.colour.index');
    }
    public function create()
    {
        return view('admin.colour.create');
    }

    public function edit($id)
    {
        $colour = Colour::findOrFail($id);
        return view('admin.colour.edit', compact('colour'));
    }



    public function store(Request $request)
    {

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
        ]);
        if (!empty($request->id)) {
            $colour = Colour::firstwhere('id', $request->id);
            $colour->name = $request->input('name');
            $colour->short_name = $request->input('short_name');

            if ($colour->save()) {
                return redirect()->route('admin.colour')->with('success', 'Colour ' . $request->id . ' Updated Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {
            $colour = new Colour();

            $colour->name = $request->input('name');
            $colour->short_name = $request->input('short_name');

            if ($colour->save()) {
                return redirect()->route('admin.colour')->with('success', 'Colour added Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        }
    }


    public function deleteSelected(Request $request)
    {
        $selectedColours = $request->input('selected_colours');
        if (!empty($selectedColours)) {
            Colour::whereIn('id', $selectedColours)->delete();
            return response()->json(['success' => true, 'message' => 'Selected Colours deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No Colour selected for deletion.']);
    }


    public function remove(Request $request, $id)
    {
        $colour = Colour::firstwhere('id', $request->id);

        if ($colour->delete()) {
            return back()->with('success', 'Colour deleted Suuccessfully !!');
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }
    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $colour = Colour::findOrFail($id);
        if ($colour) {
            $colour->status = $request->status;
            $colour->save();
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
                Colour::create([
                    'id' => $data[0],
                    'name' => $data[1],
                    'short_name' => $data[2],
                ]);
            }

            fclose($handle);
        }

        return redirect()->route('admin.colour')->with('success', 'Colour imported successfully.');

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
                // Fetch Colors based on status
                $query = Colour::query();
                if ($status !== null) {
                    $query->where('status', $status);
                }

                $colors = $query->get();
                $colorsData = [];
                foreach ($colors as $color) {
                    $colorsData[] = [
                        $color->id,
                        $color->name,
                        $color->short_name,
                        $color->status,
                    ];
                }
                $sheet->fromArray($colorsData, null, 'A2');

                // Write CSV to output
                $writer = new Csv($spreadsheet);
                $writer->setUseBOM(true);
                $writer->save('php://output');
            });

            // Set headers for response
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="colors.csv"');

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sampleFileDownloadColour()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="colour_csv_sample.csv"',
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
