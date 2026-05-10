<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use Illuminate\Cache\TaggableStore;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreCategory;
use App\Models\Category;
use App\Models\Tag;
use App\Models\storeTag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }
    public function addStore(Request $request)
    {

        DB::beginTransaction();
        try {
            $data = $request->validate([
                'store_name' => 'required',
                'store_description' => 'required',
                'store_cover_image' => 'required',
                'store_logo' => 'required',
                'category_id' => 'required',
                'tag_id' => 'required',
                'user_id' => 'required',
            ]);
             $storeData = $request->except(['category_id','tag_id'],$request->all());

            if ($request->hasFile('store_logo')) {
                Log::info("working");
                $file = $request->file('store_logo');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('documents/store'), $filename);
                $storeData['store_logo'] =  $filename;
            }


            if ($request->hasFile('store_cover_image')) {
                $file = $request->file('store_cover_image');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('documents/store'), $filename);

                $storeData['store_cover_image'] =  $filename;
            }


            $addStore = Store::create($storeData);

            if($request->has('category_id'))
            {

                foreach ($request->category_id as $category)
                {
                    $categoryDatas = [
                        'store_id' => $addStore->id,
                        'category_id' => $category
                    ];
                    StoreCategory::create($categoryDatas);
                }

            }
            if($request->has('tag_id'))
            {
                foreach ($request->tag_id as $tag_id) {
                    $tagData = [
                        'store_id' => $addStore->id,
                        'tag_id' => $tag_id
                    ];
                    storeTag::create($tagData);
                }

            }
          DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Store created successfully',
                'store' => $addStore,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("Error");
            Log::info($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while creating the store',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getStoreTags(Request $request){
        $tags = Tag::where('status',1)->get();
        if($tags){
            return response()->json([
                'success' => true,
                'message' => 'Tags get successfully',
                'tags' => $tags,
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while fatching the Tags',
                'error' => "",
            ], 500);
        }
    }

   public function getStoresBackend()
    {
        $user = auth()->user();

        $query = Store::with([
            'categories' => function ($query) {
                // $query->where('status', 1);
            }
        ])->orderBy('id', 'DESC');

        if ($user->hasRole('vendor')) {
            $query->where('user_id', $user->id);
        }

        $stores = $query->get();

        return response()->json([
            'success' => true,
            'stores'  => $stores,
            'message' => $stores->count() > 0
                ? 'Stores fetched successfully'
                : 'No store found'
        ]);
    }

    public function getStoreCategoryBackend($id)
    {
       $category_id = StoreCategory::with([])->where('store_id',$id)->pluck('category_id')->toArray();
       $categories = Category::with([])->whereIn('id',$category_id)->get();
       if(count($categories)>0){
           return response()->json(['success'=>true,'categories'=>$categories,'message'=>'Stores fetched successfully']);
       }else{
           return response()->json(['success'=>false,'message'=>'No store found','categories'=>[]]);
       }
    }


    public function updateStore(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $store = Store::findOrFail($id);

            $data = $request->validate([
                'store_name' => 'required',
                'store_description' => 'required',
                'category_id' => 'required|array',
                // 'tag_id' => 'required|array',
                'user_id' => 'required',
            ]);

            $storeData = $request->except([
                'category_id',
                'tag_id',
            ]);

            /*
            |--------------------------------------------------------------------------
            | STORE LOGO
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('store_logo')) {

                // OLD IMAGE DELETE
                if ($store->store_logo && file_exists(public_path('documents/store/' . $store->store_logo))) {
                    unlink(public_path('documents/store/' . $store->store_logo));
                }

                $file = $request->file('store_logo');

                $filename = time() . '_logo_' . $file->getClientOriginalName();

                $file->move(public_path('documents/store'), $filename);

                $storeData['store_logo'] = $filename;
            }

            /*
            |--------------------------------------------------------------------------
            | STORE COVER IMAGE
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('store_cover_image')) {

                // OLD IMAGE DELETE
                if ($store->store_cover_image && file_exists(public_path('documents/store/' . $store->store_cover_image))) {
                    unlink(public_path('documents/store/' . $store->store_cover_image));
                }

                $file = $request->file('store_cover_image');

                $filename = time() . '_cover_' . $file->getClientOriginalName();

                $file->move(public_path('documents/store'), $filename);

                $storeData['store_cover_image'] = $filename;
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE STORE
            |--------------------------------------------------------------------------
            */
            $store->update($storeData);

            /*
            |--------------------------------------------------------------------------
            | UPDATE CATEGORY
            |--------------------------------------------------------------------------
            */
            StoreCategory::where('store_id', $store->id)->delete();

            if ($request->has('category_id')) {

                foreach ($request->category_id as $category) {

                    StoreCategory::create([
                        'store_id' => $store->id,
                        'category_id' => $category
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE TAGS
            |--------------------------------------------------------------------------
            */
            StoreTag::where('store_id', $store->id)->delete();

            if ($request->has('tag_id')) {

                foreach ($request->tag_id as $tag) {

                    StoreTag::create([
                        'store_id' => $store->id,
                        'tag_id' => $tag
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Store updated successfully',
                'store' => $store,
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Store Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occurred while updating store',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getStoreProducts($storeId)
    {
        $store = Store::find($storeId);

        if (!$store) {
            return response()->json([
                'message' => 'Store not found'
            ], 404);
        }

          $products = Product::with([
            'stores',
            'productImages'
        ])->where('store_id', $storeId)
        ->get();

        return response()->json([
            'message' => 'Store products fetched successfully',
            'products' => $products
        ]);
    }
}
