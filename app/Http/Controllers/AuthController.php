<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordEmail;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Order;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\OrderItem;

class AuthController
{
    // Show login page
    public function login()
    {
        return view('front.account.login');
    }

    // Show register page
    public function register()
    {
        return view('front.account.register');
    }

    // Process registration
    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'
        ]);

        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'You have been registered successfully');
            return response()->json(['status' => true]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    // Authenticate user
    public function authenticate(Request $request)
    {
        // Clear old flash messages and intended URL
        session()->forget(['success', 'error', 'url.intended']);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.login')
                ->withInput($request->only('email'))
                ->withErrors($validator)
                ->with('error', 'Either Password/Email is incorrect');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('member'))) {
            // Regenerate session for security
            $request->session()->regenerate();

            // Redirect to intended URL if exists, else profile
            $intended = session()->pull('url.intended', route('account.profile'));
            return redirect()->to($intended);
        } else {
            return redirect()->route('account.login')
                ->withInput($request->only('email'))
                ->with('error', 'Either Password/Email is incorrect');
        }
    }

    // Logout user
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session completely
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('account.login')
            ->with('success', 'You have successfully logged out');
    }

    // Show profile
    public function profile()
    {
        $userId = Auth::id();
        $countries = Country::orderBy('name', 'ASC')->get();
        $user = User::find($userId);
        $address = CustomerAddress::where('user_id', $userId)->first();

        return view('front.account.profile', [
            'user' => $user,
            'countries' => $countries,
            'address' => $address
        ]);
    }

    // Update profile
    public function updateProfile(Request $request)
    {
        $userId = Auth::id();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'required'
        ]);

        if ($validator->passes()) {
            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            session()->flash('success', 'Your Profile Updated Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Your Profile Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    // Update or add address
    public function updateAddress(Request $request)
    {
        $userId = Auth::id();
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'country_id' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
            'apartment' => 'required',
        ]);

        if ($validator->passes()) {
            CustomerAddress::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_id' => $userId,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country_id,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'apartment' => $request->apartment,
                    'zip' => $request->zip,
                ]
            );

            session()->flash('success', 'Address Updated Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Address Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    // Orders list
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->get();
        return view('front.account.order', ['orders' => $orders]);
    }

    // Order detail
    public function orderDetail($id)
    {
        $order = Order::where('user_id', Auth::id())->where('id', $id)->first();
        $orderItems = OrderItem::where('order_id', $id)->get();

        return view('front.account.orderdetail', [
            'order' => $order,
            'orderItem' => $orderItems,
            'orderItemCount' => $orderItems->count(),
        ]);
    }

    // Wishlist
    public function wishlist()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())->with('product')->get();
        return view('front.account.wishlist', ['wishlists' => $wishlists]);
    }

    // Remove product from wishlist
    public function removeProductFromWishlist(Request $request)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('product_id', $request->id)->first();
        if (!$wishlist) {
            session()->flash('error', 'Product already removed.');
            return response()->json(['status' => false]);
        }

        $wishlist->delete();
        session()->flash('success', 'Product removed successfully.');
        return response()->json(['status' => true]);
    }

    // Show change password form
    public function showChangePasswordForm()
    {
        return view('front.account.change-password');
    }

    // Change password
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = Auth::user();
        if (!Hash::check($request->old_password, $user->password)) {
            session()->flash('error', 'Your old password is incorrect.');
            return response()->json(['status' => false]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        session()->flash('success', 'Password changed successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully',
        ]);
    }

    // Forgot password
    public function forgotPassword()
    {
        return view('front.account.forgot-password');
    }

    public function processForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
        }

        $token = Str::random(60);
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        \DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $user = User::where('email', $request->email)->first();
        Mail::to($request->email)->send(new ResetPasswordEmail([
            'token' => $token,
            'user' => $user,
            'mailSubject' => 'You have requested to reset your password',
        ]));

        return redirect()->route('front.forgotPassword')->with('success', 'Please check your email to reset your password');
    }

    // Reset password form
    public function resetPassword($token)
    {
        $hasToken = \DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$hasToken) {
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid request');
        }

        return view('front.account.reset-password', ['token' => $token]);
    }

    // Process reset password
    public function processResetPassword(Request $request)
    {
        $token = $request->token;
        $hasObj = \DB::table('password_reset_tokens')->where('token', $token)->first();

        if (!$hasObj) {
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid request');
        }

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:3',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return redirect()->route('front.resetPassword', $token)->withErrors($validator);
        }

        $user = User::where('email', $hasObj->email)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        \DB::table('password_reset_tokens')->where('email', $hasObj->email)->delete();

        return redirect()->route('account.login')->with('success', 'Password updated successfully');
    }
}