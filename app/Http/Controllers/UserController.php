<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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
                                    <a href="' . route('admin.user.edit', $row->id) . '" class="dropdown-item">
                                        <i class="ph-pencil me-2"></i>Edit
                                    </a>
                                    <form action="' . route('admin.user.delete', $row->id) . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this user?\')">
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
                    return $row->created_at ? $row->created_at->format('d M Y') : 'N/A';
                })
                ->make(true);
        }

        return view('admin.users.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:10|min:10',
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

        return redirect()->route('admin.user')
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:10|min:10',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
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

        return redirect()->route('admin.user')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user')->with('success', 'User deleted successfully.');
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'dealer')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function remove(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->delete()) {
            return back()->with('success', 'User deleted successfully.');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $user = User::findOrFail($id);
        if ($user) {
            $user->status = $request->status;
            $user->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function deleteSelected(Request $request)
    {
        $selectedUsers = $request->input('selected_users');
        if (!empty($selectedUsers)) {
            User::whereIn('id', $selectedUsers)->delete();
            return response()->json(['success' => true, 'message' => 'Selected users deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No users selected for deletion.']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ','); // Skip the header row

            // Fetch all roles and map role names to IDs
            $roles = Role::pluck('id', 'name')->toArray();

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Validate data
                if (count($data) < 6) {
                    \Log::warning('Skipping row with insufficient columns:', $data);
                    continue;
                }

                // Map role name to role ID
                $roleName = $data[4];
                $roleId = $roles[$roleName] ?? null;

                // Check if role_id is valid
                if (!$roleId) {
                    \Log::warning('Invalid role name:', $roleName);
                    continue;
                }

                // Handle date conversion with error handling
                try {
                    $createdAt = \Carbon\Carbon::createFromFormat('d-M-y', $data[5])->format('Y-m-d');
                } catch (\Exception $e) {
                    \Log::error('Date format error:', ['data' => $data, 'exception' => $e->getMessage()]);
                    $createdAt = null; // or use a default date
                }

                // Create or update the user
                User::updateOrCreate(
                    ['email' => $data[2]], // Assuming email is unique
                    [
                        'name' => $data[1],
                        'phone' => $data[3],
                        'role_id' => $roleId,
                        'created_at' => $createdAt,
                        'password' => Hash::make('defaultpassword'), // Provide a default password
                    ]
                );
            }

            fclose($handle);
        }

        return redirect()->route('admin.user')->with('success', 'Users imported successfully.');
    }




    public function export(Request $request)
    {
        try {
            $status = $request->query('status', null); // Get status from query parameters

            $response = new StreamedResponse(function () use ($status) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Add headers for CSV
                $sheet->fromArray(["ID", "Name", "Email", "Phone", "Role", "Created At"], null, 'A1');

                // Fetch users based on status
                $query = User::query();
                if ($status !== null) {
                    $query->where('status', $status);
                }
                $users = $query->get();
                $usersData = [];
                foreach ($users as $user) {
                    $role = $user->role->name ?? 'N/A'; // Adjust as per your role relationship

                    // Check if created_at is null before formatting
                    $createdAt = $user->created_at ? $user->created_at->format('d-M-y') : 'N/A';
                    $usersData[] = [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->phone,
                        $role,
                        $createdAt,
                    ];
                }
                $sheet->fromArray($usersData, null, 'A2');

                // Write CSV to output
                $writer = new Csv($spreadsheet);
                $writer->setUseBOM(true);
                $writer->save('php://output');
            });

            // Set headers for response
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="users.csv"');

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sampleFileDownloadUser()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_csv_sample.csv"',
        ];

        $columns = ['Id','Name', 'Email', 'Phone', 'Role', 'Created At'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
