<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminLoginController //implements HasMiddleware
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
    public function index(){
    return view('admin.login');
}

    public function authenticate(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'

        ]);

        if($validator->passes()){
            if (Auth::guard('admin')->attempt(['email'=>$request->email,'password'=>$request->password],
                $request->get('remember'))) {
                    $admin=Auth::guard('admin')->user();
                    if ($admin->role == 2) {
                        return redirect()->route('admin.dashboard');
                    }else{
                         Auth::guard('admin')->logout();
                         return redirect()->route('admin.login')->with('error','You are not Authorized to access the Admin Panel');
                    }


            }else{
                return redirect()->route('admin.login')->with('error','Either Email or Password is in-valid');
            }

        }else{
            return redirect()->route('admin.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));
        }
    }



}
