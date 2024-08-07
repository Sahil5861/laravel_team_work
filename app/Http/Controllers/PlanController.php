<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


use DataTables;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Plan::query();

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            $data = $query->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('description', function ($row) {
                    return \Illuminate\Support\Str::words($row->description, 10, '...');
                })
                ->addColumn('image', function ($row) {
                    return $row->image ? asset($row->image) : '';
                })
                ->addColumn('price', function ($row) {
                    return $row->price;
                })
                ->addColumn('special_price', function ($row) {
                    return $row->special_price;
                })
                ->addColumn('expiry_date', function ($row) {
                    return $row->expiry_date ? $row->expiry_date->format('d M Y') : 'N/A';
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
                                        <a href="' . route('admin.plan.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="' . route('admin.plan.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'description', 'image', 'status'])
                ->make(true);
        }

        return view('admin.pages.plans.index');
    }


    public function create()
    {
        $plans = Plan::whereNull('deleted_at')->get();
        $oldImage = session('old_image'); // This should fetch the old image from the session
        return view('admin.pages.plans.create', compact('plans', 'oldImage'));
    }


    public function edit($id)
    {
        $plans = Plan::whereNull('deleted_at')->get();
        $plan = Plan::find($id);

        return view('admin.pages.plans.edit', compact('plan', 'plans'));
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
            'description' => 'required',
            'price' => 'required|numeric|regex:/^\d+(\.\d{1,6})?$/',
            'special_price' => 'required|numeric|regex:/^\d+(\.\d{1,6})?$/',
            'expiry_date' => 'sometimes|date|after_or_equal:today',
        ]);

        $plan = $request->id ? Plan::find($request->id) : new Plan();
        $plan->name = $request->input('name');
        $plan->description = $request->input('description');
        $plan->price = $request->input('price');
        $plan->special_price = $request->input('special_price');
        $plan->expiry_date = $request->input('expiry_date') ? \Carbon\Carbon::parse($request->input('expiry_date')) : null;

        if ($request->file('image')) {
            // Remove old image if it exists
            if ($plan->image && file_exists(public_path($plan->image))) {
                unlink(public_path($plan->image));
            }

            $image = $request->file('image');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/plan');
            $image->move($destination, $imagename);

            $plan->image = 'uploads/plan/' . $imagename;
        }

        if ($plan->save()) {
            return redirect()->route('admin.plan')->with('success', 'Plan saved successfully!');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }





    public function remove(Request $request, $id)
    {
        $plan = Plan::firstwhere('id', $request->id);

        if ($plan->delete()) {
            return back()->with('success', 'Plan deleted Suuccessfully !!');
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }


    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $plan = Plan::findOrFail($id);
        if ($plan) {
            $plan->status = $request->status;
            $plan->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }

    }



    public function deleteSelected(Request $request)
    {
        $selectedPlans = $request->input('selected_plans');
        if (!empty($selectedPlans)) {
            Plan::whereIn('id', $selectedPlans)->delete();
            return response()->json(['success' => true, 'message' => 'Selected plans deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No plans selected for deletion.']);
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
                Plan::create([
                    'id' => $data[0],
                    'name' => $data[1],
                    'description' => $data[2],
                    'image' => $data[3],
                    'price' => (float) $data[4], // Ensure this is a float
                    'special_price' => (float) $data[5], // Ensure this is a float
                    'expiry_date' => $data[6],
                ]);
            }

            fclose($handle);
        }

        return redirect()->route('admin.plan')->with('success', 'Plans imported successfully.');
    }



    public function export(Request $request)
    {
        try {
            $status = $request->query('status', null); // Get status from query parameters

            $response = new StreamedResponse(function () use ($status) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Add headers for CSV
                $sheet->fromArray(['ID', 'Name', 'Description', 'Image', 'Price', 'Special Price', 'Expiry Date', 'Created At', 'Status'], null, 'A1');

                // Fetch plans based on status
                $query = Plan::query();
                if ($status !== null) {
                    $query->where('status', $status);
                }
                $plans = $query->get();
                $plansData = [];
                foreach ($plans as $plan) {
                    $plansData[] = [
                        $plan->id,
                        $plan->name,
                        $plan->description,
                        $plan->image,
                        $plan->price,
                        $plan->special_price,
                        $plan->expiry_date,
                        $plan->created_at->format('d M Y'),
                        $plan->status == 1 ? 'Active' : 'Inactive',
                    ];
                }
                $sheet->fromArray($plansData, null, 'A2');

                // Write CSV to output
                $writer = new Csv($spreadsheet);
                $writer->setUseBOM(true);
                $writer->save('php://output');
            });

            // Set headers for response
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="plans.csv"');

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sampleFileDownloadPlan()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plan_csv_sample.csv"',
        ];

        $columns = ['ID', 'Name', 'Description', 'Image', 'Price', 'Special Price', 'Expiry Date', 'Created At', 'Status'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }



}
