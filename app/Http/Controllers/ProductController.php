<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\support\Facades\File;

class ProductController extends Controller
{
    public function AddProduct(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'category' => 'required',
                'quantity' => 'required|regex:/[0-9]/',
                'price' => 'required|regex:/[0-9]/',
                'image' => 'required',
                'details' => 'required',
                'sellerName' => 'required',
                'sellerPhone' => 'required',
                'sellerId' => 'required',
                'featuredImages' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ]);
        } else {
            if ($request->hasFile("image")) {
                $file = $request->file("image");
                $imageName = Str::random(16) . '_' . $file->getClientOriginalName();
                $file->move(\public_path("Upload/ProductPhotos/"), $imageName);

                $product = new Product([
                    "name" => $request->name,
                    "category" => $request->category,
                    "price" => $request->price,
                    "quantity" => $request->quantity,
                    "details" => $request->details,
                    "sellerName" => $request->sellerName,
                    "sellerId" => $request->sellerId,
                    "sellerPhone" => $request->sellerPhone,
                    "image" => $imageName,
                ]);
                $product->save();
            }

            if ($request->hasFile("featuredImages")) {
                $files = $request->file("featuredImages");
                foreach ($files as $file) {
                    $imageName = Str::random(16) . '_' . $file->getClientOriginalName();
                    $file->move(\public_path("Upload/FeaturedPhotos/"), $imageName);
                    $ProductImage = new ProductImage();
                    $ProductImage->productId = $product->id;
                    $ProductImage->image = $imageName;
                    $ProductImage->save();
                }
            }
            return response()->json([
                'success' => 'Added Successfully!',
            ]);
        }
    }
    public function ProductList()
    {
        return Product::all();
    }
    public function FeaturedImages($id)
    {
        // Request $request
        $var = Product::find($id);
        $productId = Product::where('id', $var->id)->first();
        $FeaturedImages =  $productId->featuredImages; // function
        return $FeaturedImages;
    }

    public function ProductDetails($id)
    {
        // Request $request
        $var = Product::find($id);
        $product = Product::where('id', $var->id)->first();
        return $product;
    }
    function deleteProduct($id)
    {
        $product = Product::find($id);
        $destination = 'Upload/ProductPhotos/' . $product->image;
        if (File::exists($destination)) {
            File::delete($destination);
        }

        $images = ProductImage::where('productId', $product->id)->get();

        foreach ($images as $file) {
            $destination2 = 'Upload/FeaturedPhotos/' . $file->image;
            if (File::exists($destination2)) {
                File::delete($destination2);
            }
            $file->delete();
        }
        $product->delete();
        return response()->json([
            // 'data' => $images,
            'status' => 'success',
            'message' => 'Delete Successfully!',
        ]);
    }
}