<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController //implements HasMiddleware
{
     public static function middleware(): array
        {
          return [
            (new Middleware('permission:permissions view'))->only(['index']),
            (new Middleware('permission:permissions edit'))->only(['edit']),
            (new Middleware('permission:permissions create'))->only(['create']),
            (new Middleware('permission:permissions destroy'))->only(['destroy']),
        ];
        }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions=Permission::paginate(50);
        $data['permissions']=$permissions;
        return view('admin.permissions.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required|min:3',
        ]);
        if ($validator->passes()) {
            $permission=new Permission;
            $permission->name=$request->name;
            $permission->save();
            $message='Permission Create successfully';
            session()->flash('success',$message);
             return response()->json([
                'status'=>true,
                'message'=>$message,
            ]);
        }else{

            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
            ]);
        }
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
        $permission=Permission::findOrFail($id);
        $data['permission']=$permission;
        return view('admin.permissions.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission=Permission::findOrFail($id);
         $validator=Validator::make($request->all(),[
            'name'=>'required|min:3',
        ]);
        if ($validator->passes()) {

            $permission->name=$request->name;
            $permission->save();
            $message='Permission Updated successfully';
            session()->flash('success',$message);
             return response()->json([
                'status'=>true,
                'message'=>$message,
            ]);
        }else{

            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $permission=Permission::find($id);
       if ($permission==NUll) {
        $message='Permission Not Found';
            session()->flash('success',$message);
             return response()->json([
                'status'=>false,
                'message'=>$message,
            ]);
       }
       $permission->delete();
       $message='Permission Deleted successfully';
            session()->flash('success',$message);
             return response()->json([
                'status'=>true,
                'message'=>$message,
            ]);
    }
}
