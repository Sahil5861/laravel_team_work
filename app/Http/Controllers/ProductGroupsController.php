<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\ProductsGroup;

use DataTables;

class ProductGroupsController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = ProductsGroup::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
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
                    return '<div class="dropdown">
                                    <a href="#" class="text-body" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="' . route('admin.grouprelation.edit', $row->id) . '" class="dropdown-item">
                                            <i class="ph-pencil me-2"></i>Edit
                                        </a>
                                        <a href="'.route('admin.grouprelation.delete', $row->id).'" data-id="' . $row->id . '" class="dropdown-item delete-button">
                                            <i class="ph-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>';
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }

        return view('admin.pages.productGroup.index');
    }
    public function create(){
        return view('admin.pages.productGroup.create');
    }

    public function edit($id){
        $productsgroup = ProductsGroup::find($id);
        
        return view('admin.pages.productGroup.edit', compact('productsgroup'));
    }

    
    public function store(Request $request){
        
        $validate = $request->validate([
            'name' => 'required',
        ]);

        if ($validate == false) {
            return back()->with('error', 'Required Feild');
        }
        if (!empty($request->id)) {
            $productsgroup = ProductsGroup::firstwhere('id', $request->id);
            $productsgroup->productsgroup_name = $request->input('name');

            if ($productsgroup->save()) {
                return back()->with('success', 'Productsgroup Updated Suuccessfully !!');
            }
            else{
                return back()->with('error', 'Something went wrong !!');
            }
        }
        else{
            $productsgroup = new ProductsGroup();

            $productsgroup->products_group_name = $request->input('name');

            if ($productsgroup->save()) {
                return back()->with('success', 'Productsgroup added Suuccessfully !!');
            }
            else{
                return back()->with('error', 'Something went wrong !!');
            }
        }    
    }

    public function remove(Request $request ,$id){
        $productsgroup = ProductsGroup::firstwhere('id', $request->id);

        if ($productsgroup->delete()) {
            return back()->with('success', 'Productsgroup deleted Suuccessfully !!');
        }
        else{
            return back()->with('error', 'Something went wrong !!');
        }
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $productsgroup = ProductsGroup::findOrFail($id);
        if ($productsgroup) {
            # code...
            $productsgroup->status = $request->status;
            $productsgroup->save();
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false]);
        }

    }


    public function deleteSelected(Request $request){
        $selectedProductGroups = $request->input('selected_product_groups');
        if (!empty($selectedProductGroups)) {
            ProductsGroup::whereIn('id', $selectedProductGroups)->delete();
            return response()->json(['success' => true, 'message' => 'Selected Product Groups deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No Product Groups selected for deletion.']);
    }

    public function import(Request $request){
        $validate = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
        if ($validate == false) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ','); // Skip the header row
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                ProductsGroup::create([
                    'products_group_name' => $data[0],
                ]);
            }
    
            fclose($handle);
        }

        return redirect()->route('admin.brand')->with('success', 'Products Group imported successfully.');
    
    }


    public function export()
    {
        $response = new StreamedResponse(function () {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Add headers for CSV
            $sheet->fromArray(['Name'], null, 'A1');

            // Fetch products and add to sheet
            $productsgroups = ProductsGroup::all();
            $productsgroupsData = [];
            foreach ($productsgroups as $productsgroup) {
                $productsgroupsData[] = [
                    $productsgroup->id,
                    $productsgroup->products_group_name,
                ];
            }
            $sheet->fromArray($productsgroupsData, null, 'A2');

            // Write CSV to output
            $writer = new Csv($spreadsheet);
            $writer->setUseBOM(true);
            $writer->save('php://output');
        });

        // Set headers for response
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="productgroups.csv"');

        return $response;
    }


}
