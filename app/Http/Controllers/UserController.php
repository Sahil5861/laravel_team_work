<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('role')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                <a href="#" class="text-body" data-bs-toggle="dropdown">
                                    <i class="ph-list"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="' . route('users.edit', $row->id) . '" class="dropdown-item">
                                        <i class="ph-pencil me-2"></i>Edit
                                    </a>
                                    <form action="' . route('users.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this users?\')">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="submit" class="dropdown-item">
                                            <i class="ph-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>';
                })
                ->addColumn('role', function ($row) {
                    return $row->role->name ?? 'N/A';
                })
                ->addColumn('created_at', function ($row) {
                    return formatDate($row->created_at);
                })
                ->make(true);
        }

        return view('admin.users.index');
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);
    
        $hashedPassword = Hash::make($request->password);
    
        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $hashedPassword,
            'role_id' => $request->role_id,
        ]);
    
        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }
    

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',  // Ensure this is defined
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);
    
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
    
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->role_id = $request->role_id;
        $user->save();
    
        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }
    

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    // Method to delete multiple users
    public function deleteSelected(Request $request)
    {
        $ids = $request->input('ids');
        User::whereIn('id', $ids)->delete();
        return response()->json(['success' => 'Users deleted successfully.']);
    }

    // Method to activate multiple users
    public function activateSelected(Request $request)
    {
        $ids = $request->input('ids');
        User::whereIn('id', $ids)->update(['status' => 'active']);
        return response()->json(['success' => 'Users activated successfully.']);
    }

    // Method to deactivate multiple users
    public function deactivateSelected(Request $request)
    {
        $ids = $request->input('ids');
        User::whereIn('id', $ids)->update(['status' => 'inactive']);
        return response()->json(['success' => 'Users deactivated successfully.']);
    }
}
