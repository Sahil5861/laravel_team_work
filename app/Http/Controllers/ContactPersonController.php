<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ContactPerson;

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

        ]);
        if (!empty($request->id)) {
            $person = ContactPerson::firstwhere('id', $request->id);
            $person->name = $request->input('name');
            $person->email = $request->input('email');
            $person->phone = $request->input('phone');
            $person->designatin = $request->input('designatin');

            if ($person->save()) {
                return redirect()->route('admin.dealers')->with('success', 'Dealer '.$request->id.' Updated Suuccessfully !!');
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
}
