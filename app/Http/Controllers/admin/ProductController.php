<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\category;
use App\Models\Product;
use App\Models\productImage;
use App\Models\subcategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Image;

class ProductController implements HasMiddleware
{
     public static function middleware(): array
        {
          return [
            (new Middleware('permission:products view'))->only(['index']),
            (new Middleware('permission:products edit'))->only(['edit']),
            (new Middleware('permission:products create'))->only(['create']),
            (new Middleware('permission:products destroy'))->only(['destroy']),
        ];
        }
    /**
     *
     * Display a listing of the resource.
     */
    public function index()
    {
            $products=Product::latest('id')->with('product_images')->paginate(10);
            // dd($products);
        return view('admin.product.list',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories= category::OrderBy('name','ASC')->get();
        $brands= Brand::OrderBy('name','ASC')->get();
        // $products = Product::find();
        $data['categories']=$categories;
        $data['brands']=$brands;
        // $data['prodcts']=$products;
        return view('admin.product.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $rules=[
            'title'=>'required',
            'slug'=>'required|unique:products',
            'price'=>'required|numeric',
            'sku'=>'required|unique:Products',
            'track_qty'=>'required|in:Yes,No',
            'category'=>'required|numeric',
            'is_featured'=>'required|in:Yes,No',
        ];

            if(!empty($request->track_qty) && $request->track_qty == 'Yes'){
                $rules['qty'] = 'required|numeric';
            }
        $validator = Validator::make($request->all(),$rules);

        if($validator->passes()){
            $products = new Product;
            $products->title = $request->title;
            $products->slug = $request->slug;
            $products->description = $request->description;
            $products->short_description = $request->short_description;
            $products->shipping_returns = $request->shipping_returns;
            $products->price = $request->price;
            $products->compare_price = $request->compare_price;
            $products->sku = $request->sku;
            $products->barcode = $request->barcode;
            $products->track_qty = $request->track_qty;
            $products->qty = $request->qty;
            $products->status = $request->status;
            $products->category_id = $request->category;
            $products->sub_category_id = $request->sub_category;
            $products->brand_id = $request->brand;
            $products->is_featured = $request->is_featured;
             $products->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $products->save();

            //image save

            if(!empty($request->image_array)){
                foreach ($request->image_array as $temp_image_id) {
                    $tempinfo = TempImage::find($temp_image_id);
                    $extArray = explode('.',$tempinfo->name);
                    $ext =last($extArray);


                    $productImage= new productImage();
                    $productImage->product_id = $products->id;
                    $productImage->image ='NULL';
                    $productImage->save();

                    $imageName = $products->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //generate thumb
                    //large
                    $srcPath= public_path().'/temp/'.$tempinfo->name;

                    $destPath= public_path().'/uploads/product/large/'.$tempinfo->name;
                    $image = Image::make($srcPath)->resize(1400,null,function($constraint){
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);

                    //small
                    $destPath= public_path().'/uploads/product/small/'.$imageName;
                    $image = Image::make($srcPath)->fit(300,300);
                    $image->save($destPath);

                }
            }
              $request->session()->flash('success','Product created successfully');
                // ✅ Return success response here
        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
        ]);
        }else{
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors(),
            ]);
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $products= Product::find($id);
        if (empty($products)) {
        return redirect()->route('products.index')->with('error','Records not Found');
        }
        $productImages = productImage::where('product_id',$products->id)->get();
        $categories= category::OrderBy('name','ASC')->get();
        $brands= Brand::OrderBy('name','ASC')->get();
//
        $relatedproducts=[];
        if ($products) {
            if ($products->related_products) {
            $productarray = explode(',',$products->related_products);
                $relatedproducts =Product::WhereIn('id',$productarray)->with('product_images')->get();

            }
        }

        $subcategories = subcategory::where('category_id', $products->category_id)->get();
        $data['categories'] = $categories;
        $data['brands']=$brands;
        $data['products']=$products;
          $data['subcategories']=$subcategories;
          $data['productImages'] =$productImages;
           $data['relatedproducts'] =$relatedproducts;

        return view('admin.product.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $products= Product::find($id);

       $rules=[
            'title'=>'required',
            'slug'=>'required|unique:products,slug','.$prducts->id.','id',
            'price'=>'required|numeric',
            'sku'=>'required|unique:Products,sku','.$prducts->id.','id',
            'track_qty'=>'required|in:Yes,No',
            'category'=>'required|numeric',
            'is_featured'=>'required|in:Yes,No',
        ];

            if(!empty($request->track_qty) && $request->track_qty == 'Yes'){
                $rules['qty'] = 'required|numeric';
            }
        $validator = Validator::make($request->all(),$rules);

        if($validator->passes()){
            $products->title = $request->title;
            $products->slug = $request->slug;
            $products->description = $request->description;
            $products->short_description = $request->short_description;
            $products->shipping_returns = $request->shipping_returns;
            $products->price = $request->price;
            $products->compare_price = $request->compare_price;
            $products->sku = $request->sku;
            $products->barcode = $request->barcode;
            $products->track_qty = $request->track_qty;
            $products->qty = $request->qty;
            $products->status = $request->status;
            $products->category_id = $request->category;
            $products->sub_category_id = $request->sub_category;
            $products->brand_id = $request->brand;
            $products->is_featured = $request->is_featured;
             $products->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $products->save();

            //image save

            $request->session()->flash('success','Product Updated successfully');
                // ✅ Return success response here
        return response()->json([
            'status' => true,
            'message' => 'Product Updated successfully',
        ]);
        }else{
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors(),
            ]);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id,Request $request)
    {
          $products= Product::find($id);
          if(empty($products)){
            $request->session()->flash('error','product not found');
             return response()->json([
                'status' => false,
                'notfound'=>true,
            ]);
          }

          $productImages =productImage::where('product_id',$id)->get();
          if(!empty($productImages)){
            foreach ($productImages as $productImage) {
                File::delete(public_path('uploads/product/large/'.$productImage->image));
                File::delete(public_path('uploads/product/small/'.$productImage->image));
            }
            productImage::where('product_id',$id)->delete();
          }
          $products->delete();

          $request->session()->flash('success','product has been deleted');

             return response()->json([
                'status' => true,
                'message'=>'product has been deleted',
            ]);


    }


public function getProducts(Request $request){
    $tempProduct = [];
if ($request->term != '') {
    $products = Product::where('title','like','%'.$request->term.'%')->get();
    if ($products !=null) {
        foreach ($products as $product) {
            $tempProduct[] =array('id'=>$product->id,'text'=>$product->title);
        }
    }
}
return response()->json([
    'tags' => $tempProduct,
    'status' => true,
]);
}



}