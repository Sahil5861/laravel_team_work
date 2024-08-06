<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ContactPerson;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

use DataTables;

class ContactPersonController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ContactPerson::query();

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
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                    <a href="#" class="text-body" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="' . route('admin.dealers.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="' . route('admin.dealers.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.pages.contact_persons.index');
    }

    public function create()
    {
        return view('admin.pages.contact_persons.create');
    }


    public function edit($id)
    {
        $contactPersons = ContactPerson::all();
        return view('admin.pages.contact_persons.edit', compact('contactPersons'));
    }

    public function store(Request $request)
    {
        
        // dd($request);
        // exit;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'designation' => 'required|string',

        ]);
        if (!empty($request->id)) {
            $person = ContactPerson::firstwhere('id', $request->id);
            $person->name = $request->input('name');
            $person->email = $request->input('email');
            $person->phone = $request->input('phone');
            $person->designatin = $request->input('designatin');

            if ($person->save()) {
                return redirect()->route('admin.contactPersons')->with('success', 'Person '.$request->id.' Updated Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {

            $person = new ContactPerson();

            $person->name = $request->input('name');
            $person->email = $request->input('email');
            $person->phone = $request->input('phone');
            $person->designation = $request->input('designation');

            if ($person->save()) {
                return redirect()->route('admin.contactPersons')->with('success', 'Person added Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        }
    }


    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $contactPersons = ContactPerson::findOrFail($id);
        if ($contactPersons) {
            $contactPersons->status = $request->status;
            $contactPersons->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }


    public function deleteSelected(Request $request)
    {
        $selectedDealers = $request->input('selected_dealers');
        if (!empty($selectedDealers)) {
            ContactPerson::whereIn('id', $selected_persons)->delete();
            return response()->json(['success' => true, 'message' => 'Selected Records deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No records selected for deletion.']);
    }

    // Import And Exports--------------------------------------------------------------------
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
                ContactPerson::create([
                    'id' => $data[0],
                    'name' => $data[1],
                    'email' => $data[2],
                    'phone' => $data[3],
                    'designation' => $data[4],
                    'is_primary' => $data[5],
                ]);
            }

            fclose($handle);
        }

        return redirect()->route('admin.contactPersons')->with('success', 'Contact Persons imported successfully.');

    }


    public function export(Request $request)
    {
        try {
            $status = $request->query('status', null); // Get status from query parameters

            $response = new StreamedResponse(function () use ($status) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Add headers for CSV
                $sheet->fromArray(['ID', 'Name', 'Email', 'Phone', 'Designation', 'Is Primary'], null, 'A1');

                // Fetch contacts based on status
                $query = ContactPerson::query();
                if ($status !== null) {
                    $query->where('status', $status);
                }
                $persons = $query->get();
                $personsData = [];
                foreach ($persons as $person) {
                    $personsData[] = [
                        $person->id,
                        $person->name,
                        $person->email,
                        $person->phone,
                        $person->designation,
                        $person->is_primary ? 'YES' : 'NO',
                    ];
                }
                $sheet->fromArray($personsData, null, 'A2');

                // Write CSV to output
                $writer = new Csv($spreadsheet);
                $writer->setUseBOM(true);
                $writer->save('php://output');
            });

            // Set headers for response
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="contactPersons.csv"');

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
