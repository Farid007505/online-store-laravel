<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\productImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Image;

class ProductImageController //implements HasMiddleware
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
    public function update(Request $request){

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $srcPath =$image->getPathName();

        $productImage= new productImage();
        $productImage->product_id = $request->product_id;
        $productImage->image ='NULL';
        $productImage->save();

        $imageName = $request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
        $productImage->image =$imageName;
        $productImage->save();
                            //large
        //$srcPath= $tempImagelocation;

        $destPath= public_path().'/uploads/product/large/'.$imageName;
        $image = Image::make($srcPath)->resize(1400,null,function($constraint){
            $constraint->aspectRatio();
        });
        $image->save($destPath);

        //small
        $destPath= public_path().'/uploads/product/small/'.$imageName;
        $image = Image::make($srcPath)->fit(300,300);
        $image->save($destPath);

        return response()->json([
            'status' => true,
             'image_id' => $productImage->id,
            'image_path'=>asset('uploads/product/small/'.$productImage->image),
            'message'=>"Image has been updated",
        ]);
    }

    public function destroy(Request $request){
        $productImage = productImage::find($request->id);
        if(empty($productImage)){
            return response()->json([
            'status' => false,
            'message'=>"Image not found",
        ]);
        }
        //delete image
        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));
        $productImage->delete();

        return response()->json([
            'status' => true,
            'message'=>"Image has been deleted",
        ]);
    }
}
