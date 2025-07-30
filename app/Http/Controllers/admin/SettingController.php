<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\User;


class SettingController //implements HasMiddleware
{
    // public static function middleware(): array
    //     {
    //        return [
    //         (new Middleware('permission:view brands'))->only(['index']),
    //         (new Middleware('permission:edit brands'))->only(['edit']),
    //         (new Middleware('permission:create brands'))->only(['create']),
    //         (new Middleware('permission:destroy brands'))->only(['destroy']),
    //     ];
    //     }
    public function showChangePassword(){
        return view('admin.user.change-password');
    }

    public function processShowChangePassword(Request $request){
        // $userId=Auth::User()->id;

        $validator=Validator::make($request->all(),[
            'old_password'=>'required',
            'new_password'=>'required|min:5',
            'confirm_password'=>'required|same:new_password',
        ]);
        $admin=User::where('id',Auth::guard('admin')->id())->first();
        if ($validator->passes()) {
            if (!Hash::check($request->old_password,$admin->password)) {
                session()->flash('error','Your Old password is incorrect.Please try again');
               return response()->json([
                'status'=>true,
               ]);
            }
            User::where('id',Auth::guard('admin')->id())->first()->update([
                'password'=>Hash::make($request->new_password),
            ]);
            session()->flash('success','You Successfully change password');
               return response()->json([
                'status'=>true,
               ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
            ]);
        }
    }
}