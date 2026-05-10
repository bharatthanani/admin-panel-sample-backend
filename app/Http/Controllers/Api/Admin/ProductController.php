<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Image;
use App\Models\ProductColor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function getColorsForBackend()
    {
        $colors = Color::orderBy('id','DESC')->get();

        return response()->json([
            'message' => 'User fatech',
            'colors' => $colors
        ]);
    }

    public function getCategoryBackend()
    {
        $categories = Category::orderBy('id','DESC')->get();

        return response()->json([
            'message' => 'Category fatech',
            'category' => $categories
        ]);
    }

   public function getProductBackend()
    {
        $user = auth()->user();

        $query = Product::with([
            'stores',
            'productColors',
            'productImages'
        ])->orderBy('id', 'DESC');

        // If vendor, show only own products
        if ($user->hasRole('vendor')) {

            // $query->where('user_id', $user->id);
            $query->whereHas('stores', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $products = $query->get();

        return response()->json([
            'message'  => 'Product fetch',
            'products' => $products
        ]);
    }
    public function addProductBackend(Request $request)
    {

        DB::beginTransaction();
        try {
            $data = $request->validate([
                'product_name' => 'required',
                'product_price' => 'required',
                'product_qty' => 'required',
                'product_desc' => 'required',
                'category_id' => 'required',
                'color_id' => 'required',
                'store_id' => 'required',
            ]);

            $productData = $request->except(['color_id','product_image','image_name','id'],$request->all());
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');

                // Create unique filename
                $filename = time().'_'.$file->getClientOriginalName();

                // Save the file to public/documents/profile
                $file->move(public_path('documents/product'), $filename);

                // Save filename in database
                $productData['product_image'] =  $filename;
            }


//            $addProduct  = Product::Create($productData);

            $product = $request->id
                ? Product::with([])->find($request->id)->update($productData)
                : Product::create($productData);

            $productId = $request->id ?? $product->id;
            $product = Product::with([])->find($productId);


            if ($request->id) {
                ProductColor::with([])->where('product_id', $productId)->delete();
            }
            $imageData = [];

            if ($request->hasFile('image_name')) {
                $images = [];

                foreach ($request->file('image_name') as $file) {
                    $filename = time().'_'.$file->getClientOriginalName();

                    $file->move(public_path('documents/product'), $filename);

                    $images['image_name'] = $filename;
                    $addImage =  Image::create([
                        'image_name' => $filename,
                        'image_path' => 'documents/product/'.$filename,

                    ]);
                    ProductImage::Create([
                        'image_id' => $addImage->id,
                        'product_id' => $productId
                    ]);
                }
            }

            if($request->has('color_id'))
            {
                foreach ($request->color_id as $color) {
                    $colorData = [
                        'product_id' => $productId, // Use the product ID
                        'color_id' => $color // Insert the color ID
                    ];
                    ProductColor::create($colorData);
                }

            }
            if ($request->id){
                $message = 'Product updated successfully';
            }else{
                $message = 'Product created successfully';
            }
            DB::commit();

            return response()->json([
                'message' => $message,
                'product' => $product,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            // Handle the error, and optionally log it or return it to the client
            return response()->json([
                'message' => 'Error occurred while creating the product',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function getProductDetail(Request $request,$id)
    {
        $products = Product::with(['productColors','productImages'])->where('id',$id)->first();

        return response()->json([
            'message' => 'Producr fatech',
            'products' => $products
        ]);
    }

    public function addUpdateCategoryBackend(Request $request)
    {
        $data = $request->validate([
            'category_name' => 'required',
        ]);

        $data = [
            'category_name' => $request->category_name
        ];
        if ($request->hasFile('category_image')) {
            $file = $request->file('category_image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('documents/category'), $filename);

            $data['category_image'] =  $filename;
        }

        $category = Category::updateOrCreate(['id'=>$request->id],$data);
        if($category){
            return response()->json(['message' => 'Category updated successfully','category' => $category,'status'=>true]);
        }else{
            return response()->json(['message' => 'Error occurred while updating category','status'=>false]);
        }
    }

    public function deleteCategoryBackend(Request $request,$id)
    {
        $category = Category::where(['id'=>$id])->delete();
        if($category){
            return response()->json(['message' => 'Category deleted successfully','category' => $category,'status'=>true]);
        }else{
            return response()->json(['message' => 'Error occurred while updating category','status'=>false]);
        }
    }

    public function deleteProductBackend(Request $request,$id)
    {
        $product = Product::where(['id'=>$id])->delete();
        if($product){
            return response()->json(['message' => 'Product deleted successfully','product' => $product,'status'=>true]);
        }else{
            return response()->json(['message' => 'Error occurred while updating product','status'=>false]);
        }
    }
}
