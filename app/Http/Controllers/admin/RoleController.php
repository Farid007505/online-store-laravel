<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class RoleController implements HasMiddleware
{
     public static function middleware(): array
        {
           return [
            (new Middleware('permission:roles view'))->only(['index']),
            (new Middleware('permission:roles edit'))->only(['edit']),
            (new Middleware('permission:roles create'))->only(['create']),
            (new Middleware('permission:roles destroy'))->only(['destroy']),
        ];
        }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles=Role::orderBy('name','ASC')->paginate(10);
        $data['roles']=$roles;
        return view('admin.roles.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions=Permission::orderBy('name','ASC')->get();
        $data['permissions']=$permissions;
        return view('admin.roles.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required|min:3|unique:roles,name',
        ]);
        if ($validator->passes()) {
                $role=new Role;
                $role->name=$request->name;
                $role->save();

                if (!empty($request->permission)) {
                  foreach ($request->permission as $name) {
                    $role->givePermissionTo($name);
                  }

                }session()->flash('success','Roles created successfully');
                   return response()->json([
            'status'=>true,
        ]);

        }else{
                    return redirect()->route('roles.create')->withInput()->withErrors($validator);
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
        $role=Role::find($id);
        $permissions=Permission::orderBy('name','ASC')->get();
        $hasPermission=$role->permissions->pluck('name');
        $data['role']=$role;
        $data['permissions']=$permissions;
        $data['hasPermission']=$hasPermission;
        return view('admin.roles.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role=Role::find($id);
        $validator=Validator::make($request->all(),[
            'name'=>'required|min:3|unique:roles,name,'.$id.',id',
        ]);
        if ($validator->passes()) {
                $role->name=$request->name;
                $role->save();

                if (!empty($request->permission)) {
                  $role->syncPermissions($request->permission);
                  }else{
                    $role->syncPermissions([]);
                  }
                  session()->flash('success','Roles updated successfully');
                   return response()->json([
            'status'=>true,
        ]);
                }else{
                    return redirect()->route('roles.edit',$id)->withInput()->withErrors($validator);
                }

        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id,Request $request)
    {
        $role=Role::find($id);
        if ($role == null) {
            session()->flash('error','Role Not Found');
            return response()->json([
                'status'=>false,
            ]);
        }
        $role->delete();
         session()->flash('success','Role deleted successfully');
            return response()->json([
                'status'=>true,
            ]);
    }
}