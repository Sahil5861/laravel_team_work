<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;
use DataTables;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Size::latest()->get();
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
                                    <a href="' . route('size.edit', $row->id) . '" class="dropdown-item">
                                        <i class="ph-pencil me-2"></i>Edit
                                    </a>
                                    <form action="' . route('size.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this size?\')">
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

        return view('admin.size.index');
    }

    public function create()
    {
        return view('admin.size.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        Size::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'status' => $request->status ?? 'inactive',
        ]);

        return redirect()->route('size.index')
            ->with('success', 'Size created successfully.');
    }

    public function edit(Size $size)
    {
        return view('admin.size.edit', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $size->update($request->only('name', 'short_name', 'status'));

        return redirect()->route('size.index')
            ->with('success', 'Size updated successfully.');
    }

    public function destroy(Size $size)
    {
        $size->delete();

        return redirect()->route('size.index')
            ->with('success', 'Size deleted successfully.');
    }


    public function toggleStatus(Request $request, $id)
    {
        $size = Size::find($id);
        if ($size) {
            $size->status = $request->input('status');
            $size->save();

            return response()->json(['success' => 'Status updated successfully.']);
        }

        return response()->json(['error' => 'Size not found.'], 404);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        Size::whereIn('id', $ids)->delete();

        return response()->json(['success' => 'Selected sizes have been deleted.']);
    }

    public function bulkStatusUpdate(Request $request)
    {
        $ids = $request->ids;
        $status = $request->status;

        Size::whereIn('id', $ids)->update(['status' => $status]);

        return response()->json(['success' => 'Selected sizes have been updated.']);
    }

}
