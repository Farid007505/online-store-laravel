<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class UserController //implements HasMiddleware
{
     public static function middleware(): array
        {
          return [
            (new Middleware('permission:users view'))->only(['index']),
            (new Middleware('permission:users edit'))->only(['edit']),
            (new Middleware('permission:users create'))->only(['create']),
            (new Middleware('permission:users destroy'))->only(['destroy']),
        ];
        }

    public function index(Request $request){
        $users =User::latest();
        if (!empty($request->get('keyword'))) {
            $users=$users->where('name','like','%'.$request->get('keyword').'%');
             $users=$users->orWhere('email','like','%'.$request->get('keyword').'%');
        }
        $users=$users->paginate(10);
        $data['users']=$users;
        return view('admin.user.list',$data);
    }

    public function create(){
        return view('admin.user.create',[

        ]);
    }

    public function store(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|unique:users',
            'password'=>'required|min:5',
            'phone'=>'required',
        ]);
        if ($validator->passes()) {
            $user=new User;
            $user->name=$request->name;
            $user->email=$request->email;
            $user->phone=$request->phone;
             $user->status=$request->status;
            $user->password=Hash::make($request->password);
            $user->save();

            $message='User Create Successfully';
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

    public function edit(Request $request,$id){
        $user=User::find($id);
        $roles=Role::orderBy('name','ASC')->get();
        $hasRole=$user->roles->pluck('name');
        if ($user== Null) {
            $message= 'User Not Found';
            session()->flash('error',$message);
            return redirect()->route('users.index');
        }
        $data['user']=$user;
        $data['roles']=$roles;
        $data['hasRole']=$hasRole;
        return view('admin.user.edit',$data);
    }

    public function update(Request $request , $id){
        $user=User::find($id);
        if ($user== Null) {
            $message= 'User Not Found';

            session()->flash('error',$message);
            return response()->json([
                'status'=>true,
                'message'=>$message,
            ]);
        }
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|unique:users,email,'.$id.',id',
            'phone'=>'required',
        ]);
        if ($validator->passes()) {
            $user->name=$request->name;
            $user->email=$request->email;
            $user->phone=$request->phone;
             $user->status=$request->status;
           if ($request->password != '') {
                $user->password=Hash::make($request->password);
           }
            $user->save();
            $user->syncRoles($request->role);

            $message='User Updated Successfully';
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

    public function destroy(Request $request,$id){
        $user=User::find($id);
        if ($user== Null) {
            $message= 'User Not Found';

            session()->flash('error',$message);
            return redirect()->route('pages.index');
        }

        $user->delete();
        $message='User Delete Successfully';
            session()->flash('success',$message);
            return response()->json([
                'status'=>true,
                'message'=>$message,
            ]);
    }
}