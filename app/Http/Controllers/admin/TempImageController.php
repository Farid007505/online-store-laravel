<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Image;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class TempImageController //implements HasMiddleware
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
    public function create(Request $request){

        if ($request->image) {
             $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;
            $tempImage = new TempImage();
            $tempImage->name=$newName;
            $tempImage->save();
            $image->move(public_path().'/temp',$newName);

            //generate thumnail
            // $origionalPath=public_path('temp');
            // $thumbPath=public_path('temp/thumb');

            $srcPath=public_path().'/temp/'.$newName;
            $destPath =public_path().'/temp/thumb/'.$newName;
            $image = Image::make($srcPath);
            $image->fit(300,270);
            $image->save($destPath);
            return response()->json([
                'status'=>true,
                'image_id'=>$tempImage->id,
                'image_path'=>asset('/temp/thumb/'.$newName),
                'message'=>'image upload succcessfuly',
            ]);
        }
    }
}
