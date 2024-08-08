<?php

namespace App\Http\Controllers;

use App\Models\AdditionalImage;
use Illuminate\Http\Request;

class AdditionalImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $additionalImage = new AdditionalImage();
        $additionalImage->product_id = $request->input('product_id');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images', $imageName);
            $additionalImage->image = 'storage/images/' . $imageName;
        }

        $additionalImage->save();

        return redirect()->back()->with('success', 'Additional image added successfully!');
    }

    public function destroy($id)
    {
        $additionalImage = AdditionalImage::findOrFail($id);

        // Remove the image file
        if (file_exists(public_path($additionalImage->image))) {
            unlink(public_path($additionalImage->image));
        }

        $additionalImage->delete();

        return redirect()->back()->with('success', 'Additional image deleted successfully!');
    }
}
