<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::latest()->get();
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
                ->addColumn('updated_at', function ($row) {
                    return formatDate($row->updated_at);
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                <a href="#" class="text-body" data-bs-toggle="dropdown">
                                    <i class="ph-list"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="' . route('role.edit', $row->id) . '" class="dropdown-item">
                                        <i class="ph-pencil me-2"></i>Edit
                                    </a>
                                    <form action="' . route('role.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this role?\')">
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

        return view('admin.role.index');
    }

    public function create()
    {
        return view('admin.role.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        Role::create([
            'name' => $request->name,
            'status' => $request->status ?? 'inactive',
        ]);

        return redirect()->route('role.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('admin.role.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $role->update($request->only('name', 'status'));

        return redirect()->route('role.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('role.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->status = $request->input('status');
            $role->save();

            return response()->json(['success' => 'Status updated successfully.']);
        }

        return response()->json(['error' => 'Role not found.'], 404);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        Role::whereIn('id', $ids)->delete();

        return response()->json(['success' => 'Selected roles have been deleted.']);
    }

    public function bulkStatusUpdate(Request $request)
    {
        $ids = $request->ids;
        $status = $request->status;

        Role::whereIn('id', $ids)->update(['status' => $status]);

        return response()->json(['success' => 'Selected roles have been updated.']);
    }
}
