<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\ContactPerson;
use App\Models\User;
use App\Models\Dealer;
use App\Models\Role;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

use DataTables;

class ContactPersonController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::query();

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            $data = $query->where('role_id', 3)->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('status', function ($row) {
                //     $checked = $row->status == '1' ? 'checked' : '';
                //     $text = $checked ? 'Active' : 'Inactive';
                //     return '<label class="switch">
                //                     <input type="checkbox" class="status-checkbox status-toggle" data-id="' . $row->id . '" ' . $checked . '>
                //                     <span class="slider round status-text"></span>
                //             </label>';
                // })
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
        $dealers = Dealer::where('status', 1)->get();
        $roles = Role::where('status', 1)->get();
        return view('admin.pages.contact_persons.create', compact('dealers', 'roles'));
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
            'role' => 'required|',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
            'dealer_id' => 'required|string',
        ]);
        if (!empty($request->id)) {
            $person = User::firstwhere('id', $request->id);
            $person->name = $request->input('name');
            $person->email = $request->input('email');
            $person->phone = $request->input('phone');
            $person->role = $request->input('role');
            $person->password = Hash::make($request->input('pass1'));
            $person->real_password = $request->input('pass1');
            $person->dealers_id = $request->input('dealer_id');

            if ($person->save()) {
                return redirect()->route('admin.contactPersons')->with('success', 'Person '.$request->id.' Updated Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {

            $person = new User();

            $person->name = $request->input('name');
            $person->email = $request->input('email');
            $person->phone = $request->input('phone');
            $person->role_id = $request->input('role');
            $person->password =Hash::make($request->input('password'));
            $person->real_password = $request->input('password');
            $person->dealers_id = $request->input('dealer_id');

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
        $contactPersons = $request->input('selected_dealers');
        if (!empty($contactPersons)) {
            ContactPerson::whereIn('id', $contactPersons)->delete();
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
        if (($handle = fopen($path, 'r', "\t")) !== false) {
            $header = fgetcsv($handle, 1000, "\t"); // Adjusted to tab delimiter
        
            while (($data = fgetcsv($handle, 1000, "\t")) !== false) { // Adjusted to tab delimiter
                // Ensure there are enough columns in the CSV row
        
                // Find the dealer by name
                $dealer = Dealer::where('business_name', trim($data[4]))->first();

                // dd($dealer);
                // exit;
        
                if ($dealer) {
                    // Create the ContactPerson record with the correct dealer_id
                    $user = new User();
                    $user->id = trim($data[0]);
                    $user->name = trim($data[1]);
                    $user->email = trim($data[2]);
                    $user->phone = trim($data[3]);
                    $user->dealers_id = $dealer->id;
                    $user->role_id = trim($data[5]);
                    $user->save();
                    
                } else {
                    // Optionally handle the case where the dealer name doesn't exist
                    continue; // Skip this row if dealer not found
                }
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
                $sheet->fromArray(['ID', 'Name', 'Email','Role', 'Phone', 'Real Password','Dealer Name'], null, 'A1');

                // Fetch contacts based on status
                
                $persons = User::where('role_id', 3)->get();
                $personsData = [];
                foreach ($persons as $person) {
                    $dealer = Dealer::where('id', $person->dealers_id)->first();
                    $dealername = $dealer ? $dealer->business_name : 'N/A';
                    $personsData[] = [
                        $person->id,
                        $person->name,
                        $person->email,
                        $person->role_id,
                        $person->phone,
                        $person->real_password,
                        $dealername,
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


    public function sampleFileDownloadContactPerson()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="contactperson_csv_sample.csv"',
        ];

        $columns = ['ID', 'Name', 'Email','Role', 'Phone', 'Real Password','Dealer Name'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
