<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\category;
use App\Models\subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubcategoryController implements HasMiddleware
{
    public static function middleware(): array
        {
           return [
            (new Middleware('permission:subcategories view'))->only(['index']),
            (new Middleware('permission:subcategories edit'))->only(['edit']),
            (new Middleware('permission:subcategories create'))->only(['create']),
            (new Middleware('permission:subcategories destroy'))->only(['destroy']),
        ];
        }

     public function index(Request $request){
        $subcategory = subcategory::select('sub_categories.*','categories.name as categoryName')
        ->latest('sub_categories.id')
        ->leftjoin('categories','categories.id','sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $subcategory = $subcategory->where('sub_categories.name','like','%'.$request->get('keyword').'%');

             $subcategory = $subcategory->orWhere('categories.name','like','%'.$request->get('keyword').'%');
        }
        $subcategory = $subcategory->paginate(10);
        return view('admin.sub_category.list',compact('subcategory'));
    }

    public function create(){

        $categories = category::orderBy('name','ASC')->get();
        return view('admin.sub_category.create',compact('categories'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:sub_categories',
            'category'=>'required',
            'status'=>'required',
        ]);

        if ($validator->passes()) {
            $subcategory = new subcategory();
            $subcategory ->name = $request->name;
            $subcategory ->slug = $request->slug;
            $subcategory ->status = $request->status;
            $subcategory ->category_id = $request->category;
            $subcategory->showHome =$request->showHome;
            $subcategory->save();
            $request->session()->flash('success','Record has been Inserted');
            return response()->json([
                'status' =>true,
                'message' => 'sub catefory has been Inserted',
            ]);
        }else{
            return response()->json([
                'status' =>false,
                'errors'=> $validator->errors(),
            ]);
        }
    }

    public function edit($id,Request $request){
        $subcategories = subcategory::find($id);
        if (empty($subcategories)) {

            $request->session()->flash('error','Record not found');

            return redirect()->route('sub-categories.index');
        }

        $categories = category::orderBy('name','ASC')->get();

        return view('admin.sub_category.edit',compact('subcategories','categories'));
    }

    public function update($id,Request $request ){
        $subcategories = subcategory::find($id);
        if (empty($subcategories)) {

            return response()->json([
                'status'=>false,
                'notfound'=> true,

            ]);
            //return redirect()->route('sub-categories.index');
        }

             $validator = Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:sub_categories,slug,'.$subcategories->id.',id',
            'category'=>'required',
            'status'=>'required',
        ]);

        if ($validator->passes()) {
            $subcategories ->name = $request->name;
            $subcategories ->slug = $request->slug;
            $subcategories ->status = $request->status;
            $subcategories ->category_id = $request->category;
            $subcategories->showHome =$request->showHome;
            $subcategories->save();
            $request->session()->flash('success','Record has been Updated');
            return response()->json([
                'status' =>true,
                'message' => 'sub catefory has been Updated',
            ]);
        }else{
            return response()->json([
                'status' =>false,
                'errors'=> $validator->errors(),
            ]);
        }
    }

    public function destroy($id, Request $request){
        $subcategories = subcategory::find($id);
        if (empty($subcategories)) {

            $request->session()->flash('error','Record not found');
            return response()->json([
                'status'=>false,
                'notfound'=> true,

            ]);
            //return redirect()->route('sub-categories.index');
        }
        $subcategories->delete();
            $request->session()->flash('success','Record has been deleted');
            return response()->json([
                'status'=>true,
                'message'=> 'Record has been deleted',

            ]);
    }
}
