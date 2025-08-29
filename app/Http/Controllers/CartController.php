<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Country;
use App\Models\DiscountCoupon;
use Illuminate\Support\Facades\Session;
use App\Models\CustomerAddress;
use App\Models\OrderItem;
use App\Models\shippingCharge;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\PaymentIntent;


class CartController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            (new Middleware('permission:carts view'))->only(['index']),
            (new Middleware('permission:carts edit'))->only(['edit']),
            (new Middleware('permission:carts create'))->only(['create']),
            (new Middleware('permission:carts destroy'))->only(['destroy']),
        ];
    }
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);
        if ($product == Null) {
            return response()->json([
                'status' => false,
                'message' => 'Product Not Found',
            ]);
        };

        if (Cart::count() > 0) {
            $cartcontent = Cart::content();
            $productAlreadyExist = false;
            foreach ($cartcontent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }
            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);

                $message = $product->title . 'Add to cart';
                Session()->flash('success', $message);
                $status = true;
                $message = $product->title . ' Add to cart';
            } else {
                $message = $product->title . ' ALready exist in cart';
                Session()->flash('success', $message);
                $status = false;
                $message = 'ALready exist in cart';
            }
        } else {

            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = $product->title . ' add to cart';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
    public function cart()
    {
        $cartcontent = Cart::Content();
        // dd($cartcontent);
        $data['cartcontent'] = $cartcontent;
        return view('front.cart', $data);
    }

    public function updateCart(Request $request)
    {

        $rowId = $request->rowId;
        $qty = $request->qty;

        $iteminfo = Cart::get($rowId);
        $product = Product::find($iteminfo->id);

        if ($product->track_qty == 'Yes') {

            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                $message = "Cart has been updated successfuly";
                Session()->flash('success', $message);
                $status = true;
            } else {

                $message = 'Request Qty(' . $qty . ') not available in stock';
                Session()->flash('error', $message);
                $status = false;
            }
        } else {
            Cart::update($rowId, $qty);
            $message = "Cart has been updated successfuly";
            Session()->flash('success', $message);
            $status = true;
        }

        return response()->json([
            'status' => $status,
            'message' => 'Cart has been updated successfuly'
        ]);
    }

    public function deleteItem(Request $request)
    {

        $iteminfo = Cart::get($request->rowId);
        if ($iteminfo == null) {
            $message = "Record not found";
            Session()->flash('error', $message);
            $status = false;
            return response()->json([
                'status' => $status,
                'message' => 'Record not found'
            ]);
        }

        Cart::remove($request->rowId);
        $message = "Cart has been updated successfuly";
        Session()->flash('success', $message);
        $status = true;
        return response()->json([
            'status' => $status,
            'message' => 'Cart has been updated successfuly'
        ]);
    }

    public function checkout()
    {
        $discount = 0;
        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        if (Auth::check() == false) {
            if (!Session()->has('url:.intended')) {


                Session(['url.intended' => url()->current()]);
            }
            return redirect()->route('account.login');
        }

        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();

        // dd(Auth::check(), Auth::id(), $customerAddress);


        Session()->forget('url.intended');

        $countries  = Country::orderBy('name', 'ASC')->get();

        $subtotal = Cart::subtotal(2, '.', '');
        if (Session()->has('code')) {
            $code = Session()->get('code');
            $discount = 0;
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subtotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        if ($customerAddress != '') {
            $userCountry = $customerAddress->country_id;

            $shippinginfo = shippingCharge::where('country_id', $userCountry)->first();



            $totalqty = 0;
            $totalshippingCharge = 0;
            $grandtotal = 0;
            foreach (Cart::content() as $item) {
                $totalqty += $item->qty;
            }
            $totalshippingCharge = $shippinginfo ? $totalqty * $shippinginfo->amount : 0;
            $grandtotal = ($subtotal - $discount) + $totalshippingCharge;
        } else {

            $grandtotal = ($subtotal - $discount);
            $totalshippingCharge = 0;
        }





        $data['countries'] = $countries;
        $data['customerAddress'] = $customerAddress;
        $data['totalshippingCharge'] = $totalshippingCharge;
        $data['grandtotal'] = $grandtotal;
        $data['discount'] = $discount;
        return view('front.checkout', $data);
    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'country' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'fix errors',
                'errors' => $validator->errors()
            ]);
        }

        $user = Auth()->user();
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'apartment' => $request->appartment,
                'zip' => $request->zip,
            ]
        );

        if ($request->payment_method == 'cod' || $request->payment_method == 'stripe') {

            $discountCodeId = null;
            $promoCode = null;
            $shipping = 0;
            $discount = 0;
            $subtotal = Cart::subtotal(2, '.', '');

            if (Session()->has('code')) {
                $code = Session()->get('code');
                $discount = ($code->type == 'percent')
                    ? ($code->discount_amount / 100) * $subtotal
                    : $code->discount_amount;

                $discountCodeId = $code->id;
                $promoCode = $code->code;
            }

            $totalqty = 0;
            foreach (Cart::content() as $item) {
                $totalqty += $item->qty;
            }

            $shippinginfo = shippingCharge::where('country_id', $request->country)->first();
            if (!$shippinginfo) {
                $shippinginfo = shippingCharge::where('country_id', 'rest_of_world')->first();
            }

            $shipping = $totalqty * $shippinginfo->amount;
            $grandtotal = ($subtotal - $discount) + $shipping;

            // Stripe Payment Processing
            if ($request->payment_method == 'stripe') {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

                try {
                    if (!$request->filled('payment_method_id')) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Payment method ID is required.',
                        ], 400);
                    }

                    $paymentIntent = \Stripe\PaymentIntent::create([
                        'amount' => intval($grandtotal * 100), // amount in cents
                        'currency' => 'usd',
                        'payment_method_types' => ['card'],
                        'payment_method' => $request->payment_method_id,
                        'confirmation_method' => 'manual',
                        'confirm' => true,
                    ]);

                    if ($paymentIntent->status === 'requires_action' && isset($paymentIntent->next_action->type) && $paymentIntent->next_action->type === 'use_stripe_sdk') {
                        return response()->json([
                            'status' => false,
                            'requires_action' => true,
                            'payment_intent_client_secret' => $paymentIntent->client_secret,
                        ]);
                    }

                    if ($paymentIntent->status === 'succeeded') {
                        // Create order after successful payment

                        $order = new Order;
                        $order->subtotal = $subtotal;
                        $order->shipping = $shipping;
                        $order->grand_total = $grandtotal;
                        $order->discount = $discount;
                        $order->status = 'pending';
                        $order->payment_status = 'paid';
                        $order->coupon_code_id = $discountCodeId;
                        $order->coupon_code = $promoCode;
                        $order->user_id = $user->id;
                        $order->first_name = $request->first_name;
                        $order->last_name = $request->last_name;
                        $order->email = $request->email;
                        $order->country_id = $request->country;
                        $order->address = $request->address;
                        $order->apartment = $request->appartment;
                        $order->city = $request->city;
                        $order->state = $request->state;
                        $order->zip = $request->zip;
                        $order->mobile = $request->mobile;
                        $order->notes = $request->order_notes;
                        $order->save();

                        foreach (Cart::content() as $item) {
                            $orderItem = new OrderItem;
                            $orderItem->product_id = $item->id;
                            $orderItem->order_id = $order->id;
                            $orderItem->name = $item->name;
                            $orderItem->qty = $item->qty;
                            $orderItem->price = $item->price;
                            $orderItem->total = $item->price * $item->qty;
                            $orderItem->save();

                            $product = Product::find($item->id);
                            if ($product && $product->track_qty === 'Yes') {
                                $product->qty -= $item->qty;
                                $product->save();
                            }
                        }


                        // // Wrap email sending so failure doesn't break order
                        // try {
                        //     orderEmail($order->id, 'customer');
                        // } catch (\Exception $mailEx) {
                        //     \Log::error('Order email failed: ' . $mailEx->getMessage());
                        // }

                        orderEmail($order->id, 'customer');

                        Cart::destroy();
                        Session::forget('code');

                        return response()->json([
                            'status' => true,
                            'message' => 'Stripe payment successful and order created.',
                            'orderId' => $order->id,
                        ]);
                    }

                    return response()->json([
                        'status' => false,
                        'message' => 'Stripe payment not completed.',
                    ], 400);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Stripe Error: ' . $e->getMessage(),
                    ], 400);
                }
            }

            // COD Order Creation
            $order = new Order;
            $order->subtotal = $subtotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandtotal;
            $order->discount = $discount;
            $order->status = 'pending';
            $order->payment_status = 'not paid';
            $order->coupon_code_id = $discountCodeId;
            $order->coupon_code = $promoCode;
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->country_id = $request->country;
            $order->address = $request->address;
            $order->apartment = $request->appartment;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->mobile = $request->mobile;
            $order->notes = $request->order_notes;
            $order->save();

            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price * $item->qty;
                $orderItem->save();

                $product = Product::find($item->id);
                if ($product && $product->track_qty === 'Yes') {
                    $product->qty -= $item->qty;
                    $product->save();
                }
            }

            orderEmail($order->id, 'customer');

            Cart::destroy();
            Session::forget('code');

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully',
                'orderId' => $order->id,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid payment method',
            ]);
        }
    }

    public function thankyou($id)
    {
        return  view('front.thank', [
            'id' => $id,
        ]);
    }

    public function getOrderSummery(Request $request)
    {
        $subtotal = Cart::subtotal(2, '.', '');
        $discount = 0;
        $discountString = '';
        if (Session()->has('code')) {
            $code = Session()->get('code');
            $discount = 0;
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subtotal;
            } else {
                $discount = $code->discount_amount;
            }
            $discountString = '<div class="mt-4" id="discount_row">
                                <strong>' . Session()->get('code')->code . '</strong>
                                <a class="btn btn-sm btn-danger" id="remove_discount"><i class="fa fa-times"></i></a>
                            </div>';
        }


        if ($request->country_id > 0) {
            $shippinginfo = shippingCharge::where('country_id', $request->country_id)->first();


            $totalqty = 0;
            $totalshippingCharge = 0;
            $grandtotal = 0;

            foreach (Cart::content() as $item) {
                $totalqty += $item->qty;
            }

            if ($shippinginfo != null) {
                $shippingCharge = $totalqty * $shippinginfo->amount;
                $grandtotal = ($subtotal - $discount) + $shippingCharge;
                return response()->json([
                    'status' => true,

                    'grandtotal' => number_format($grandtotal, 2),
                    'discount' => number_format($discount, 2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge, 2),

                ]);
            } else {
                $shippinginfo = shippingCharge::where('country_id', 'rest_of_world')->first();
                $shippingCharge = $totalqty * $shippinginfo->amount;
                $grandtotal = ($subtotal - $discount) + $shippingCharge;
                return response()->json([
                    'status' => true,

                    'grandtotal' => number_format($grandtotal, 2),
                    'discount' => number_format($discount, 2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge, 2),
                ]);
            }
        } else {
            return response()->json([
                'status' => true,
                'grandtotal' => number_format($subtotal - $discount, 2),
                'discount' => number_format($discount, 2),
                'discountString' => $discountString,
                'shippingCharge' => number_format(0, 2),

            ]);
        }
    }
    public function applyDiscount(Request $request)
    {
        $code = DiscountCoupon::where('code', $request->code)->first();

        if ($code == Null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Coupon Code',
            ]);
        }
        $now = Carbon::now();

        if ($code->start_at != '') {
            $startDate = Carbon::createFromformat('Y-m-d H:i:s', $code->start_at);

            if ($now->lt($startDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Coupon Code',
                ]);
            }
        }

        if ($code->expire_at != '') {
            $expireDate = Carbon::createFromformat('Y-m-d H:i:s', $code->expire_at);

            if ($now->gt($expireDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Coupon Code',
                ]);
            }
        }

        if ($code->max_uses > 0) {
            $couponUsed = Order::where('coupon_code_id', $code->id)->count();
            if ($couponUsed >= $code->max_uses) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Coupon Code',
                ]);
            }
        }

        if ($code->max_uses_user > 0) {
            $couponUsedByUsers = Order::where(['coupon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();
            if ($couponUsedByUsers >= $code->max_uses_user) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already used this coupon',
                ]);
            }
        }
        $subtotal = Cart::subtotal(2, '.', '');
        if ($code->min_amount > 0) {
            if ($subtotal < $code->min_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'You min amount must be' . $code->min_amount . '.',
                ]);
            }
        }
        Session::put('code', $code);
        return $this->getOrderSummery($request);
    }
    public function removeCoupon(Request $request)
    {
        Session::forget('code');
        return $this->getOrderSummery($request);
    }
}