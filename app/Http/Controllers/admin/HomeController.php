<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class HomeController //implements HasMiddleware
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
        return view('admin.dashboard');
//         $admin=Auth::guard('admin')->user();
//    echo 'welcome'.$admin->name.' <a href="'.route('admin.logout').'">Logout</a> ';
    }

    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}