<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Mail\ResetPasswordEmail;
use App\Models\User;
use App\Models\wishlist;
use App\Models\order;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\orderItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;



class AuthController //implements HasMiddleware
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
    public function login(){


return view('front.account.login');
    }

    public function register(){
        return view('front.account.register');
    }

    public function processRegister(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required|min:3',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:5|confirmed'
        ]);
        if ($validator->passes())  {
            $user =new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();
            session()->flash('success','You have been registered successfuly');
return response()->json([
                'status'=>true,
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
            if ($validator->passes())  {
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password],$request->get('member'))) {
                    if (session()->has('url.intended')) {

                            return redirect(session()->get('url.intended'));
                    }
                     return redirect()->route('account.profile');
                }else{
                    //session()->flash('error','Either Password/Email is incorrect');
                     return redirect()->route('account.login')->WithInput($request->only('email'))->with('error','Either Password/Email is incorrect');
                }

                    return response()->json([
                    'status'=>true,
            ]);
        }else{
           return redirect()
           ->route('account.login')
           ->withInput($request->only('email'))
           ->withErrors($validator)
           ->with('error','Either Password/Email is incorrect');
        }
    }
    public function profile(){
         $userId=Auth::User()->id;
        $countries=Country::OrderBy('name','ASC')->get();
        $user= User::where('id',$userId)->first();
        $address=CustomerAddress::where('user_id',$userId)->first();
        $data['user']= $user;
        $data['countries']= $countries;
         $data['address']= $address;
        return view('front.account.profile',$data);
    }

    public function updateProfile(Request $request){

        $validator =Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.$userId.',id',
            'phone'=>'required'
        ]);
        if ($validator->passes()) {
            $user=User::find($userId);
            $user->name=$request->name;
            $user->email=$request->email;
            $user->phone=$request->phone;
            $user->save();

            session()->flash('success','Your Profile Updated Successfully');
            return response()->json([
                'status'=>true,
                'message'=>'Your Profile Updated Successfully',
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
            ]);
        }
    }

    public function updateAddress(Request $request){
         $userId=Auth::User()->id;
        $validator = Validator::make($request->all(),[
            'first_name' =>'required',
            'last_name' =>'required',
            'email' =>'required',
            'country_id' =>'required',
            'address' =>'required',
            'city' =>'required',
            'state' =>'required',
            'zip' =>'required',
            'mobile' =>'required',
            'apartment'=>'required',
        ]);
        if ($validator->passes()) {
             $userId=Auth::User()->id;
            CustomerAddress::updateOrCreate(
            ['user_id' => $userId],
            [
                'user_id' => $userId,
                'first_name'=>$request->first_name,
                'last_name'=>$request->last_name,
                'email'=>$request->email,
                'mobile'=>$request->mobile,
                'country_id'=>$request->country_id,
                'address'=>$request->address,
                'city'=>$request->city,
                'state'=>$request->state,
                'apartment'=>$request->appartment,
                'zip'=>$request->zip,
            ]
         );
            session()->flash('success','Address Updated Successfully');
            return response()->json([
                'status'=>true,
                'message'=>'Address Updated Successfully',
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
            ]);
        }
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect()->route('account.login')->with('success','You Logout successfuly');

    }

    public function orders(){
        $data =[];
        $user =Auth::user();
        $orders =Order::where('user_id' ,$user->id)->orderBy('created_at','DESC')->get();
        $data['orders'] = $orders;
        return view('front.account.order',$data);
    }
    public function orderDetail($id){

         $data =[];
        $user =Auth::user();
        $order =Order::where('user_id' ,$user->id)->where('id',$id)->first();
        $orderItem= OrderItem::where('order_id',$id)->get();
         $orderItemCount = OrderItem::where('order_id',$id)->get()->count();
        $data['order'] = $order;
         $data['orderItem'] = $orderItem;
          $data['orderItemCount'] = $orderItemCount;

        return view('front.account.orderdetail',$data);
    }
    public function wishlist(){
        $wishlists =wishlist::where('user_id',Auth::user()->id)->with('product')->get();

        $data['wishlists'] = $wishlists;
        return view('front.account.wishlist',$data);
    }
    public function removeProductFromWishlist(Request $request){
         $wishlist =wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->first();
         if ($wishlist == null) {
            session()->flash('error','product Already Removed.');
            return response()->json([
                'status'=>false,
            ]);

         }else{
            $wishlist =wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->delete();
            session()->flash('success','product Removed SuccessFully.');
            return response()->json([
                'status'=>true,
            ]);
         }
    }

    public function showchangePasswordForm(){
        return view('front.account.change-password');
    }
    public function changePassword(Request $request){
        $validator=Validator::make($request->all(),[
            'old_password'=>'required',
            'new_password'=>'required|min:5',
            'confirm_password'=>'required|same:new_password',
        ]);
        if ($validator->passes()) {
            // $userId=Auth::User()->id;
            $user=User::select('id','password')->Where('id',Auth::user()->id)->first();
            if (!Hash::check($request->old_password, $user->password)) {
                session()->flash('error','Your Old Password is incorrect,Please try again');
                return response()->json([
                    'status'=>true,
                ]);
            }

            User::where('id',Auth::user()->id)->first()->update([
                'password'=>Hash::make($request->new_password),
            ]);

            session()->flash('success','You have successfully Chnage Password');
                return response()->json([
                    'status'=>true,
                    'message'=>'You Successfully Updated Password',
                ]);
        }

        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors(),
        ]);
    }
    public function forgotPassword(){
        return view('front.account.forgot-password');
    }

    public function processForgotPassword(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|email|exists:users,email'
        ]);
        if ($validator->fails()) {
            return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
        }
        $token=Str::random(60);
        \DB::table('password_reset_tokens')->where('email',$request->email)->delete();
        \DB::table('password_reset_tokens')->insert([
            'email'=>$request->email,
            'token'=>$token,
            'created_at'=>now(),
        ]);
         //send email here
         $user=User::where('email',$request->email)->first();
         $formData=[
            'token'=>$token,
            'user'=>$user,
            'mailSubject'=>'You have requested to reset Your Password',
         ];
        Mail::to($request->email)->send(new ResetPasswordEmail($formData));
        return redirect()->route('front.forgotPassword')->with('success','Please Check Your Email to reset your password');
    }

    public function resetPassword($token){
        $hasToken=\DB::table('password_reset_tokens')->where('token',$token)->first();
        if($hasToken == Null){
            return redirect()->route('front.forgotPassword')->with('error','Invalid request');
        }
        return view('front.account.reset-password',['token'=>$token]);
    }
    public function processResetPassword(Request $request){
        $token=$request->token;

        $hasObj=\DB::table('password_reset_tokens')->where('token',$token)->first();
        if($hasObj == Null){
            return redirect()->route('front.forgotPassword')->with('error','Invalid request');
        }

        $user=User::where('email',$hasObj->email)->first();
         $validator=Validator::make($request->all(),[
            'new_password'=>'required|min:3',
            'confirm_password'=>'required|same:new_password'
        ]);
        if ($validator->fails()) {
            return redirect()->route('front.resetPassword',$token)->withErrors($validator);
        }
        User::where('id',$request->id)->update([
            'password'=>Hash::make($request->new_password),
                    ]);
                     \DB::table('password_reset_tokens')->where('email',$request->email)->delete();
  return redirect()->route('account.login',$token)->with('success','You successfully updated your password');
    }

}