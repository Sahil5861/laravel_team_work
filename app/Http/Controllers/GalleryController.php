<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Gallery;


class GalleryController extends Controller
{
    public function viewfolders(){
        $folders = Folder::where('deleted_at', null)->get();
        return view('admin.pages.gallery.index', compact('folders'));
    }

    public function viewfolderImages(Request $request ,$id){
        $folder = Folder::find($id);
        $limit = $request->input('limit', 12);
        // $images = Gallery::where('folder_id', $id)->get();
        $offset = $request->get('offset', 0);
        if ($limit) {
            $images = Gallery::where('folder_id', $id)->skip($offset)->take($limit)->get();
            if ($request->ajax()) {
                return response()->json($images);
            }
        }  
        
        // $images = Gallery::paginate($limit);
        // if ($request->ajax()) {
        //     return response()->json([
        //         'images' => $images->items(),
        //         'pagination' => (string) $images->links()->render()
        //     ]);
        // }
        return view('admin.pages.gallery.images', compact('folder', 'images'));
    }

    public function uploadImages(Request $request){
        $validate = $request->validate([
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if (!$validate) {
            return back()->withErrors(['error' => 'Something went wrong with one of the images!']);
        }
        // dd($request);
        // exit;
        $folder = Folder::where('id', $request->folder_id)->first();
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Generate a unique name for each image
                $imagename = time() . '.' . $image->getClientOriginalName();
                $destination = public_path('uploads/'.$folder->name);
                $image->move($destination, $imagename);

                $imagepath = 'uploads/'.$folder->name.'/' . $imagename;

                $gallery_image = new Gallery();

                $gallery_image->folder_id = $folder->id;
                $gallery_image->image_path = $imagepath;

                if (!$gallery_image->save()) {
                    return back()->withErrors(['error' => 'Something went wrong with one of the images!']);
                }
            }
            return redirect()->route('admin.gallery.image', $folder->id)->with('success', 'Images Uploaded Successfully!');
        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'No images were uploaded.'
            ], 400);
            
        }
         
    }
}
