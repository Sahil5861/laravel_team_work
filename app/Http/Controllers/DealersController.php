<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\ContactPerson;

use DataTables;

class DealersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Dealer::query();

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
                // ->addcolumn('address', function ($row){
                //     return  $row->city.", ". $row->state.", ". $row->country;
                // })
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
                ->rawColumns(['action', 'status', 'address'])
                ->make(true);
        }

        return view('admin.pages.dealers.index');
    }


    public function create()
    {
        $contactPersons = ContactPerson::all();
        return view('admin.pages.dealers.create', compact('contactPersons'));
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
            'GST_number' => 'nullable|string|max:15',
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
            $dealer->GST_number = $request->input('GST_number');

            if ($dealer->save()) {
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
            $dealer->GST_number = $request->input('GST_number');

            if ($dealer->save()) {
                return redirect()->route('admin.dealers')->with('success', 'Dealer added Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        }
    }

}
