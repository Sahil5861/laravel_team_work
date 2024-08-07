<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dealer;
use Illuminate\Support\Facades\Hash;


use DataTables;
class ViewsController extends Controller
{

    public function getData(Request $request, $id){
        $dealer = Dealer::find($id);
        $users = User::where('role_id', 3)->where('dealers_id', $id)->get();

        if ($users->count() == 1) {
            $user = $users->first();
            if ($user->is_primary != 1) {
                $user->is_primary = 1;
                $user->save();
            }
        }
        if ($request->ajax()) {
            $query = User::query();

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            $data = $query->where('role_id', 3)->where('dealers_id',$id)->latest()->get();

            return DataTables::of($data)

                ->addColumn('name', function ($row){
                    $name = $row->name; 
                    if ($row->is_primary == 1) {
                        $name .= '<div class="status-indicator-container">
						            <span class="status-indicator bg-success"></span>
					            </div>';
                    }
                    return $name;
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == '1' ? 'checked' : '';
                    $text = $checked ? 'Active' : 'Inactive';
                    return '<label class="switch">
                                    <input type="checkbox" class="status-checkbox status-toggle" data-id="' . $row->id . '" ' . $checked . '>
                                    <span class="slider round status-text"></span>
                            </label>';
                })
                ->addColumn('action', function ($row) {

                    $setPrimary = '';
                    if ($row->is_primary == 0) {
                        $setPrimary = '<a href="#" " data-id="' . $row->id . '" class="dropdown-item set-primary-button">
                                            <i class="ph-check me-2 text-"></i>Set as Primary
                                        </a>';
                    }
                    return '<div class="dropdown">
                                    <a href="#"  class="text-body" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        '.$setPrimary.'
                                        <a href="' . route('admin.dealers.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'name', 'status'])
                ->make(true);
        }

        return view('admin.pages.dealers.view', compact('dealer', 'users'));
    }
    public function store(Request $request, $id)
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
        $person = new User();
        $person->name = $request->input('name');
        $person->email = $request->input('email');
        $person->phone = $request->input('phone');
        $person->role_id = $request->input('role');
        $person->password =Hash::make($request->input('password'));
        $person->real_password = $request->input('password');
        $person->dealers_id = $id;

        if ($person->save()) {
            return redirect()->route('admin.dealers.viewdata',$id)->with('success', 'New Person added Suuccessfully  for Dealer!!'. $id);
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }


    public function setPrimary(Request $request)
{

    // dd($request);
    // exit;
    $userId = $request->input('user_id');
    $dealerId = $request->input('dealer_id');

    // Set all users for the dealer to is_primary = 0
    User::where('dealers_id', $dealerId)->update(['is_primary' => 0]);

    // Set the selected user to is_primary = 1
    $user = User::find($userId);
    $user->is_primary = 1;

    if ($user->save()) {
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false]);
    }
}

}
