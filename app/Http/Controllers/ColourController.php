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
                    $checked = $row->status == 'active' ? 'checked' : '';
                    return '<label class="switch">
                                <input type="checkbox" class="status-toggle" data-id="' . $row->id . '" ' . $checked . '>
                                <span class="slider round"></span>
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
                                    <a href="' . route('colour.edit', $row->id) . '" class="dropdown-item">
                                        <i class="ph-pencil me-2"></i>Edit
                                    </a>
                                    <form action="' . route('colour.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this colour?\')">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="submit" class="dropdown-item">
                                            <i class="ph-trash me-2"></i>Delete
                                        </button>
                                    </form>
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        Colour::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'status' => $request->status ?? 'inactive',
        ]);

        return redirect()->route('colour.index')
            ->with('success', 'Colour created successfully.');
    }

    public function edit(Colour $colour)
    {
        return view('admin.colour.edit', compact('colour'));
    }

    public function update(Request $request, Colour $colour)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $colour->update($request->only('name', 'short_name', 'status'));

        return redirect()->route('colour.index')
            ->with('success', 'Colour updated successfully.');
    }

    public function destroy(Colour $colour)
    {
        $colour->delete();

        return redirect()->route('colour.index')
            ->with('success', 'Colour deleted successfully.');
    }


    public function toggleStatus(Request $request, $id)
    {
        $colour = Colour::find($id);
        if ($colour) {
            $colour->status = $request->input('status');
            $colour->save();

            return response()->json(['success' => 'Status updated successfully.']);
        }

        return response()->json(['error' => 'Colour not found.'], 404);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        Colour::whereIn('id', $ids)->delete();

        return response()->json(['success' => 'Selected Colours have been deleted.']);
    }



    public function updateStatus($id, Request $request){
        $request->validate([
            'status' => 'required|boolean',
        ]);
        $color = Colour::findOrFail($id);
        if ($color) {
            $color->status = $request->status;
            $color->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }

    }

    public function bulkStatusUpdate(Request $request)
    {
        $ids = $request->ids;
        $status = $request->status;

        Colour::whereIn('id', $ids)->update(['status' => $status]);

        return response()->json(['success' => 'Selected Colours have been updated.']);
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


}
