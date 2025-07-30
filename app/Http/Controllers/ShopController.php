<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\subcategory;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;

class ShopController // implements HasMiddleware
{
    //  public static function middleware(): array
    //     {
    //        return [
    //         (new Middleware('permission:view brands'))->only(['index']),
    //         (new Middleware('permission:edit brands'))->only(['edit']),
    //         (new Middleware('permission:create brands'))->only(['create']),
    //         (new Middleware('permission:destroy brands'))->only(['destroy']),
    //     ];
    //     }
        public function index(Request $request,$categorySlug = Null,$subcategorySlug = Null){

        $categorySelected ='';
        $subcategorySelected ='';
        $brandsArray= [];


        $categories = Category::orderBy('name','ASC')->with('sub_categories')->where('status',1)->get();

         $brands = Brand::orderBy('name','ASC')->where('status',1)->get();

        $products = Product::where('status',1);

        if (!empty($categorySlug)) {
             $category= Category::where('slug',$categorySlug)->first();
            $products=$products->where('category_id',$category->id);
            $categorySelected=$category->id;

        }

        if (!empty($subcategorySlug)) {
            $subcategory = subcategory::where('slug',$subcategorySlug)->first();
            $products=$products->where('sub_category_id',$subcategory->id);
              $subcategorySelected=$subcategory->id;
        }


         if (!empty($request->get('brand'))) {
            $brandsArray = explode(',',$request->get('brand'));
            $products = $products->whereIn('brand_id',$brandsArray);
        }

            if ($request->get('price_max' ) != '' && $request->get('price_min') != '') {
                if ($request->get('price_max') == 1000) {
                    $products=$products->whereBetween('price',[intval($request->get('price_min')),100000]);
            }else{
                $products=$products->whereBetween('price',[intval($request->get('price_min')),intval($request->get('price_max'))]);
            }
            }

            if (!empty($request->get('search'))) {
                   $products = $products->where('title','like','%'.$request->get('search').'%');
            }





          if ($request->get('sort') != '') {
            if ($request->get('sort') == 'latest') {
                 $products = $products->orderBy('id','DESC');
            }else if ($request->get('sort') == 'price_asc') {
                $products = $products->orderBy('price','ASC');
            }else{
                $products = $products->orderBy('price','DESC');
            }
          }else{
            $products = $products->orderBy('id','DESC');
          }
          $products = $products->paginate(6);


        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
         $data['categorySelected'] = $categorySelected;
        $data['subcategorySelected'] = $subcategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = intval($request->get('price_max')==0) ? 1000 : $request->get('price_max');
        $data['priceMin'] = intval($request->get('price_min'));
        $data['sort'] = $request->get('sort');

        return view('front.shop',$data);


    }



    public function product($slug){
       $product=Product::where('slug',$slug)->with('product_images')->first();
        if ($product == Null) {
            abort(404);

        }

        $relatedproducts=[];
        if ($product) {
            if ($product->related_products) {
            $productarray = explode(',',$product->related_products);
                $relatedproducts =Product::WhereIn('id',$productarray)->get();
            }
        }

        $data['product'] = $product;
        $data['relatedproducts'] = $relatedproducts;
        return view('front.product',$data);
            }
}
