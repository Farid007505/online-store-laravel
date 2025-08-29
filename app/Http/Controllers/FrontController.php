<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\wishlist;
use App\Models\Page;
use App\Models\User;
use App\Models\ContactEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FrontController //implements HasMiddleware
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
        $products= Product::where('is_featured','Yes')->where('status',1)->get();

         $latestproducts= Product::orderBy('id','ASC')->where('status',1)->take(8)->get();

        $data['featureproducts'] = $products;
        $data['latestproducts'] = $latestproducts;
        return view('front.home',$data);
    }

    public function addToWishList(Request $request){
        if (Auth::check()== false) {
            session(['url.intended' => url()->previous()]);
            return response()->json([
                'status' => false,
            ]);
        }
        $product = Product::where('id',$request->id)->first();
        if ($product == Null) {
            return response()->json([
            'status'=>true,
            'message'=>'<div class="alert alert-danger">Product not Found.</div>',
        ]);
        }

        wishlist::updateOrCreate(
            [
                'user_id'=>Auth::user()->id,
                'product_id'=>$request->id,
            ],
            [
                'user_id'=>Auth::user()->id,
                'product_id'=>$request->id,
            ],
        );

        // $wishlist = new wishlist();
        // $wishlist->user_id=Auth::user()->id;
        // $wishlist->product_id=$request->id;
        // $wishlist->save();
        return response()->json([
            'status'=>true,
           'message'=>'<div class="alert alert-success"><strong>'.$product->title.'</strong> added in your Wishlist.</div>',
        ]);

    }

    public function page($slug){
        $page=Page::where('slug',$slug)->first();
        if ($page==Null) {
            abort(404);
        }
        $data['page']=$page;
      return view('front.layouts.page',$data);
    }

    public function sendContactEmail(Request $request){
        $validator=Validator::make($request->all(),[

            'name'=>'required',
            'email'=>'required|email',
            'subject'=>'required|min:10',
        ]);
        if ($validator->passes()) {
            $mailData=[
                'name'=>$request->name,
                'email'=>$request->email,
                'subject'=>$request->subject,
                'message'=>$request->message,
                'mail_subject'=>'You have received email',
            ];

            $admin=User::where('id',1)->first();
            Mail::to($admin->email)->send(new ContactEmail($mailData));

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
            ]);
        }
    }
}