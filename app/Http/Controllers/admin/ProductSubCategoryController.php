<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\subcategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductSubCategoryController //implements HasMiddleware
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
     /**
     * Display a listing of the resource.
     */
    public function index(Request $request)

    {
        if(!empty($request->category_id)){
        $subcategories = subcategory::where('category_id',$request->category_id)
        ->orderBy('name','ASC')
        ->get();
        return response()->json([
            'status' => true,
            'subcategories' => $subcategories,
        ]);
        }else{
             return response()->json([
            'status' => true,
            'subcategories' => [],
             ]);
        };
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
        //
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
}
