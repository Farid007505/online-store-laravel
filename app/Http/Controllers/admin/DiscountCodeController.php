<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DiscountCoupon;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DiscountCodeController implements HasMiddleware
{
     public static function middleware(): array
        {
            return [
            (new Middleware('permission:coupons view'))->only(['index']),
            (new Middleware('permission:coupons edit'))->only(['edit']),
            (new Middleware('permission:coupons create'))->only(['create']),
            (new Middleware('permission:coupons destroy'))->only(['destroy']),
        ];
        }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $coupons =DiscountCoupon::latest();



        if (!empty($request->get('keyword'))) {
            $coupons = $coupons->where('name','like','%'.$request->get('keyword').'%');
        }
        $coupons =$coupons->paginate(10);
        $data['coupons'] = $coupons;
        return view('admin.coupon.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


        return view('admin.coupon.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'code'=>'required',
            'type'=>'required',
            'discount_amount'=>'required|numeric',
            'status'=>'required',


                ]);
                if ($validator->passes()) {

                    if (!empty($request->start_at)) {
                        $now =Carbon::now();
                        $startAt= Carbon::createFromFormat('Y-m-d H:i:s',$request->start_at);
                        if ($startAt->lte($now) == true) {
                             return response()->json([
                            'status'=>false,
                            'errors'=> ['start_at' => 'Start date could not less then current date']
                ]);
                        }
                    }

                    if (!empty($request->start_at) && !empty($request->expire_at)) {
                        $expireAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expire_at);
                        $startAt= Carbon::createFromFormat('Y-m-d H:i:s',$request->start_at);
                        if ($expireAt->gt($startAt) == false) {
                             return response()->json([
                            'status'=>false,
                            'errors'=> ['expire_at' => 'Expire date must be greater then start date']
                ]);
                        }
                    }

                    $discountcoupon =new DiscountCoupon();

                    $discountcoupon->code = $request->code;
                    $discountcoupon->name = $request->name;
                     $discountcoupon->description = $request->description;
                    $discountcoupon->max_uses = $request->max_uses;
                    $discountcoupon->max_uses_user = $request->max_uses_user;
                    $discountcoupon->type = $request->type;
                    $discountcoupon->discount_amount = $request->discount_amount;
                    $discountcoupon->min_amount = $request->min_amount;
                    $discountcoupon->status = $request->status;
                    $discountcoupon->start_at = $request->start_at;
                    $discountcoupon->expire_at = $request->expire_at;
                    $discountcoupon->save();
                    session()->flash('success' , 'Discount Coupon Added Successfully');
                     return response()->json([
                    'status'=>true,
                    'success'=>'Discount Coupon Added Successfully',
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $coupons = DiscountCoupon::find($id);
        if ($coupons == Null) {
            session()->flash('success' , 'Record not found');
            return redirect()->route('coupons.index');
        }
        $data['coupons'] =$coupons;
        return view('admin.coupon.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
          $discountcoupon = DiscountCoupon::find($id);
          if ($discountcoupon == Null) {
            session()->flash('success' ,'Record Not Found');
                     return response()->json([
                    'status'=>true,
                    'success'=>'Record Not Foundy',
                ]);
          }
            $validator=Validator::make($request->all(),[
            'code'=>'required',
            'type'=>'required',
            'discount_amount'=>'required|numeric',
            'status'=>'required',


                ]);
                if ($validator->passes()) {

                //     if (!empty($request->start_at)) {
                //         $now =Carbon::now();
                //         $startAt= Carbon::createFromFormat('Y-m-d H:i:s',$request->start_at);
                //         if ($startAt->lte($now) == true) {
                //              return response()->json([
                //             'status'=>false,
                //             'errors'=> ['start_at' => 'Start date could not less then current date']
                // ]);
                //         }
                //     }

                    if (!empty($request->start_at) && !empty($request->expire_at)) {
                        $expireAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expire_at);
                        $startAt= Carbon::createFromFormat('Y-m-d H:i:s',$request->start_at);
                        if ($expireAt->gt($startAt) == false) {
                             return response()->json([
                            'status'=>false,
                            'errors'=> ['expire_at' => 'Expire date must be greater then start date']
                ]);
                        }
                    }



                    $discountcoupon->code = $request->code;
                    $discountcoupon->name = $request->name;
                     $discountcoupon->description = $request->description;
                    $discountcoupon->max_uses = $request->max_uses;
                    $discountcoupon->max_uses_user = $request->max_uses_user;
                    $discountcoupon->type = $request->type;
                    $discountcoupon->discount_amount = $request->discount_amount;
                    $discountcoupon->min_amount = $request->min_amount;
                    $discountcoupon->status = $request->status;
                    $discountcoupon->start_at = $request->start_at;
                    $discountcoupon->expire_at = $request->expire_at;
                    $discountcoupon->save();

                    session()->flash('success' , 'Discount Coupon updated Successfully');
                     return response()->json([
                    'status'=>true,
                    'success'=>'Discount Coupon updated Successfully',
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
        $discountcoupon = DiscountCoupon::find($id);

        if ($discountcoupon == Null) {
            session()->flash('success' ,'Record Not Found');
                     return response()->json([
                    'status'=>true,
                    'success'=>'Record Not Foundy',
                ]);
          }
       $discountcoupon->delete();
       session()->flash('success','Record has been deleted');
        return response()->json([
            'status' =>true,
            'success'=>'Record has been deleted'
        ]);
    }
}
