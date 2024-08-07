<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\User;
use App\Models\ContactPerson;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

use DataTables;

class DealersController extends Controller
{
    public function index(Request $request)
    {
        // $query = Dealer::query();
        // $data = $query->latest()->with('ContactPerson')->get();
        //     dd($data);
        //     exit;
        if ($request->ajax()) {
            $query = Dealer::query();
            $contactPersons = ContactPerson::where('status', 1);

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            $data = $query->latest()->with('ContactPerson')->get();
            
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
                ->addColumn('view', function ($row){
                    return '<a href="' . route('admin.dealers.viewdata', $row->id) . '" class="text-primary"><i class="ph-eye me-2"></i></a>';
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
                ->rawColumns(['action', 'status', 'view'])
                ->make(true);
            }
            $contactPersons = ContactPerson::where('status', 0)->get();

        return view('admin.pages.dealers.index', compact('contactPersons'));
    }

    public function view($id){
        $dealer = Dealer::find($id);
        $users = User::where('role_id', 3)->where('dealers_id', $id)->get();

        return view('admin.pages.dealers.view', compact('dealer', 'users'));
    }

    public function create()
    {
        $contactPersons = ContactPerson::where('is_primary', 0)->get();
        return view('admin.pages.dealers.create', compact('contactPersons'));
    }


    public function edit($id)
    {
        $dealer = Dealer::find($id);
        $contactPersons = ContactPerson::all();
        return view('admin.pages.dealers.edit', compact('dealer', 'contactPersons'));
    }


    public function store(Request $request)
    {

        // dd($request);
        // exit;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'contact_person_id' => 'nullable|max:6',
            'authenticated' => 'required',
            'gst_no' => 'nullable|string|max:15',
        ]);
        if (!empty($request->id)) {
            $dealer = Dealer::firstwhere('id', $request->id);
            $dealer->business_name = $request->input('name');
            $dealer->business_email = $request->input('email');
            $dealer->phone_number = $request->input('phone');
            $dealer->contact_person_id = $request->input('contact_person_id');
            $dealer->city = $request->input('city');
            $dealer->state = $request->input('state');
            $dealer->country = $request->input('country');
            $dealer->authenticated = $request->input('authenticated');
            $dealer->GST_number = $request->input('gst_no');

            if ($dealer->save()) {
                return redirect()->route('admin.dealers')->with('success', 'Dealer ' . $request->id . ' Updated Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {

            $dealer = new Dealer();

            $dealer->business_name = $request->input('name');
            $dealer->business_email = $request->input('email');
            $dealer->phone_number = $request->input('phone');
            $dealer->contact_person_id = $request->input('contact_person_id');
            $dealer->city = $request->input('city');
            $dealer->state = $request->input('state');
            $dealer->country = $request->input('country');
            $dealer->authenticated = $request->input('authenticated');
            $dealer->GST_number = $request->input('gst_no');

            if ($dealer->save()) {
                return redirect()->route('admin.dealers')->with('success', 'Dealer added Suuccessfully !!');
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

        $dealer = Dealer::findOrFail($id);
        if ($dealer) {
            $dealer->status = $request->status;
            $dealer->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }

    }

    public function remove(Request $request, $id)
    {
        $dealer = Dealer::firstwhere('id', $request->id);

        if ($dealer->delete()) {
            return back()->with('success', 'Dealer deleted Suuccessfully !!');
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }

    public function deleteSelected(Request $request)
    {
        $selectedDealers = $request->input('selected_dealers');
        if (!empty($selectedDealers)) {
            Dealer::whereIn('id', $selectedDealers)->delete();
            return response()->json(['success' => true, 'message' => 'Selected Dealers deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No Dealers selected for deletion.']);
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
                Brand::create([
                    'id' => $data[0],
                    'business' => $data[1],
                    'business_email' => $data[2],
                    'city' => $data[3],
                    'state' => $data[4],
                    'country' => $data[5],
                    'conatact_person_id' => $data[6],
                    'authenticated' => $data[7],
                    'GST_number' => $data[8],
                ]);
            }

            fclose($handle);
        }

        return redirect()->route('admin.brand')->with('success', 'Dealers imported successfully.');

    }


    public function export(Request $request)
    {
        try {
            $status = $request->query('status', null); // Get status from query parameters

            $response = new StreamedResponse(function () use ($status) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Add headers for CSV
                $sheet->fromArray(['ID', 'Dealer Name', 'Dealer Email', 'Dealer Phone', 'City', 'State', 'Conuntry', 'Conatact Person Id', 'Is Authenticated','GST number'], null, 'A1');

                // Fetch brands based on status
                $query = Dealer::query();
                if ($status !== null) {
                    $query->where('status', $status);
                }
                $dealers = $query->get();
                $dealersData = [];
                foreach ($dealers as $dealer) {
                    $contactPerson = ContactPerson::where('id', $dealer->contact_person_id)->first();

                    $dealersData[] = [
                        $dealer->id,
                        $dealer->business_name,
                        $dealer->business_email,
                        $dealer->phone_number,
                        $dealer->city,
                        $dealer->state,
                        $dealer->country,
                        $contactPerson ? $contactPerson->name : 'Not assigned', // Safely handle null case
                        $dealer->authenticated ? 'Yes' : 'No',
                        $dealer->GST_number ? $dealer->GST_number : 'Not Provided',
                    ];
                }
                $sheet->fromArray($dealersData, null, 'A2');

                // Write CSV to output
                $writer = new Csv($spreadsheet);
                $writer->setUseBOM(true);
                $writer->save('php://output');
            });

            // Set headers for response
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="dealers.csv"');

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

   // app/Http/Controllers/DealerController.php

    public function updatePrimaryContact(Request $request, $dealerId)
    {
        $dealer = Dealer::findOrFail($dealerId);
        $contactPersonId = $request->input('contact_person_id');

        // Set all contact persons of this dealer to 'no'
        $dealer->contactPersons()->update(['is_primary' => false]);

        // Set the selected contact person to 'yes'
        $dealer->contactPersons()->where('id', $contactPersonId)->update(['is_primary' => true]);

        return response()->json(['success' => 'Primary contact updated successfully.']);
}




}
