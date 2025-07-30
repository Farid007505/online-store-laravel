<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BrandController implements HasMiddleware
{
     public static function middleware(): array
        {
           return [
            (new Middleware('permission:brands view'))->only(['index']),
            (new Middleware('permission:brands edit'))->only(['edit']),
            (new Middleware('permission:brands create'))->only(['create']),
            (new Middleware('permission:brands destroy'))->only(['destroy']),
        ];
        }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands=Brand::latest();
        $brands=$brands->paginate();
        return view('admin.brand.list',compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('admin.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:brands',
        ]);

        if($validator->passes()){
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug=$request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success','Brand has been created');
        return response()->json([
            'status'=>true,
            'message'=>'Brand has been created',
        ]);
        }else{
        return response()->json([
            'status'=>false,
            'message'=>'something worng',
        ]);

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
        $brands = Brand::find($id);
        if (empty($brands)) {
            return redirect()->route('brands.index');
        }
        return view('admin.brand.edit',compact('brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brands = Brand::find($id);
        if (empty($brands)) {
            return response()->json([
                'staus' => false,
                'NotFound'=>true,
            ]);
        }
         $validator = Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:brands,slug,'.$brands->id.',id',
        ]);

        if($validator->passes()){

            $brands->name = $request->name;
            $brands->slug=$request->slug;
            $brands->status = $request->status;
            $brands->save();

            $request->session()->flash('success','Brand has been created');
        return response()->json([
            'status'=>true,
            'message'=>'Brand has been created',
        ]);
        }else{
        return response()->json([
            'status'=>false,
            'message'=>'something worng',
        ]);

    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id,Request $request)
    {
        $brands = Brand::find($id); $brands = Brand::find($id);
        if (empty($brands)) {
            return response()->json([
                'staus' => false,
                'NotFound'=>true,
            ]);
        }
        $brands->delete();
        $request->session()->flash('success','data has been updated');
        return response()->json([
                'staus' => true,
                'message'=>'data has been updated',
            ]);

    }
}
