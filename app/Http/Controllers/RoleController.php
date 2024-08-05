<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Role;

use DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Role::query();

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
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at->format('d M Y');
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                    <a href="#" class="text-body" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="' . route('admin.role.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="' . route('admin.role.delete', $row->id) . '" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.pages.roles.index');
    }


    public function create()
    {
        $roles = Role::whereNull('deleted_at')->get();
        return view('admin.pages.roles.create', compact('roles'));
    }

    public function edit($id)
    {
        $roles = Role::whereNull('deleted_at')->get();
        $role = Role::find($id);

        return view('admin.pages.roles.edit', compact('role', 'roles'));
    }


    public function store(Request $request)
    {

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);
        if (!empty($request->id)) {
            $role = Role::firstwhere('id', $request->id);
            $role->name = $request->input('name');

            if ($role->save()) {
                return redirect()->route('admin.role')->with('success', 'Role ' . $request->id . ' Updated Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        } else {
            $role = new Role();

            $role->name = $request->input('name');

            if ($role->save()) {
                return redirect()->route('admin.role')->with('success', 'Role added Suuccessfully !!');
            } else {
                return back()->with('error', 'Something went wrong !!');
            }
        }
    }

    public function remove(Request $request, $id)
    {
        $role = Role::firstwhere('id', $request->id);

        if ($role->delete()) {
            return back()->with('success', 'Role deleted Suuccessfully !!');
        } else {
            return back()->with('error', 'Something went wrong !!');
        }
    }


    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $role = Role::findOrFail($id);
        if ($role) {
            $role->status = $request->status;
            $role->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }

    }



    public function deleteSelected(Request $request)
    {
        $selectedRoles = $request->input('selected_roles');
        if (!empty($selectedRoles)) {
            Role::whereIn('id', $selectedRoles)->delete();
            return response()->json(['success' => true, 'message' => 'Selected roles deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No roles selected for deletion.']);
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
                Role::create([
                    'id' => $data[0],
                    'name' => $data[1],
                ]);
            }

            fclose($handle);
        }

        return redirect()->route('admin.role')->with('success', 'roles imported successfully.');

    }

    public function export(Request $request)
    {
        try {
            $status = $request->query('status', null); // Get status from query parameters

            $response = new StreamedResponse(function () use ($status) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Add headers for CSV
                $sheet->fromArray(['ID', 'Name', 'Image', 'Created At', 'Status'], null, 'A1');

                // Fetch roles based on status
                $query = Role::query();
                if ($status !== null) {
                    $query->where('status', $status);
                }
                $roles = $query->get();
                $rolesData = [];
                foreach ($roles as $role) {
                    $rolesData[] = [
                        $role->id,
                        $role->name,
                        $role->created_at->format('d M Y'),
                        $role->status == 1 ? 'Active' : 'Inactive',
                    ];
                }
                $sheet->fromArray($rolesData, null, 'A2');

                // Write CSV to output
                $writer = new Csv($spreadsheet);
                $writer->setUseBOM(true);
                $writer->save('php://output');
            });

            // Set headers for response
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="roles.csv"');

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




}
