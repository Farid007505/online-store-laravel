<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
// use Intervention\Image\Facades\Image;
class CategoryController implements HasMiddleware
{
      public static function middleware(): array
        {
           return [
            (new Middleware('permission:categories view'))->only(['index']),
            (new Middleware('permission:categories edit'))->only(['edit']),
            (new Middleware('permission:categories create'))->only(['create']),
            (new Middleware('permission:categories destroy'))->only(['destroy']),
        ];
        }
    public function index(Request $request){
        $category = category::latest();
        if (!empty($request->get('keyword'))) {
            $category = $category->where('name','like','%'.$request->get('keyword').'%');
        }
        $category = $category->paginate(10);
        return view('admin.category.list',compact('category'));
    }

    public function create(){
       return view('admin.category.create');
    }

    public function store(Request $request){

        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:categories'
        ]);
        if ($validator->passes()) {
            $category = new category();
            $category->name =$request->name;
            $category->slug =$request->slug;
            $category->status =$request->status;
            $category->showHome =$request->showHome;
            $category->save();

            //save image
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                 $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);
                // generate thumnail
                //  $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                // $img=Image::make($sPath);
                // $img->resize(450,600);
                // $img->save($dPath);

                // dd(extension_loaded('gd'), Image::make($sPath)->mime());
                 $category->image =$newImageName;
            $category->save();
            }

            $request->session()->flash('success','Category add successfuly');

            return response()->json([
            'status'=> true,
            'message'=>'Category add successfuly'
            ]);

        }else{
            return response()->json([
            'status'=> false,
            'errors'=>$validator->errors()
            ]);

        }

    }

    public function edit($categoryId,Request $request){
        $category = category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit',compact('category'));
    }

    public function update($categoryId,Request $request){

        $category = category::find($categoryId);
        if (empty($category)) {
            return response()->json([
                'status'  => false,
                'notfound'=> true,
                'message' => 'Category not found',
            ]);
        }

        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:categories,slug,'.$category->id.',id',
        ]);
        $oldImage = $category->image;

        if ($validator->passes()) {
            $category->name =$request->name;
            $category->slug =$request->slug;
            $category->status =$request->status;
            $category->showHome =$request->showHome;
            $category->save();


            //save image
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id.'-'.time().'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                 $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);
                // generate thumnail
                //  $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                // $img=Image::make($sPath);
                // $img->resize(450,600);
                // $img->save($dPath);

                // dd(extension_loaded('gd'), Image::make($sPath)->mime());
                 $category->image =$newImageName;
            $category->save();



                // Delete Old Image

                File::delete(public_path().'/uploads/category/'.$oldImage);

            }

            $request->session()->flash('success','Category Update successfuly');

            return response()->json([
            'status'=> true,
            'message'=>'Category Update  successfuly'
            ]);

        }else{
            return response()->json([
            'status'=> false,
            'errors'=>$validator->errors()
            ]);

        }
    }

    public function delete(){

    }

     public function destroy($categoryId, Request $request){

        $category = category::find($categoryId);
            if (empty($category)) {
                $request->session()->flash('error','Category not found');
                return response()->json([
            'status'=> true,
            'message'=>'Category not found',
            ]);

                //return redirect()->route('categories.index');
            }

        File::delete(public_path().'/uploads/category/'.$category->image);
        $category->delete();
            $request->session()->flash('success','Category Delete Successfully');

        return response()->json([
            'status'=> true,
            'message'=>'Category Delete Successfully',
            ]);
    }

}
