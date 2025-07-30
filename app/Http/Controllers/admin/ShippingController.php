<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ShippingController implements HasMiddleware
{
    public static function middleware(): array
        {
          return [
            //(new Middleware('permission:shipping view'))->only(['index']),
            (new Middleware('permission:shipping edit'))->only(['edit']),
            (new Middleware('permission:shipping create'))->only(['create']),
            (new Middleware('permission:shipping destroy'))->only(['destroy']),
        ];
        }
    public function create(){
        $countries =Country::get();
        $shippingCharges=shippingCharge::select('shipping_charges.*','countries.name')->leftjoin('countries','countries.id','shipping_charges.country_id')->get();
        $data['countries'] = $countries;
        $data['shippingCharges'] =$shippingCharges;



        return view('admin.shipping.create',$data);


    }
    public function store(Request $request){
        $validator =Validator::make($request->all(),[
            'country'=>'required',
            'amount'=>'required|numeric',
        ]);
        if ($validator->passes()) {
            $country = shippingCharge::where('country_id',$request->country)->count();
            if ($country > 0) {
                session()->flash('error','Record Already Exits');
                 return response()->json([
                'status' => true,
                'error'=>'Record Already Exits',

            ]);
            }

            $shipping = new ShippingCharge;
            $shipping->country_id=$request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success','Shipping Apply successfuly Apply');
            return response()->json([
                'status' => true,
                'success'=>'Shipping Apply successfuly Apply',

            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors(),

            ]);
        }
    }
    public function edit($id){

        $shippingCharge=shippingCharge::find($id);
        $countries = country::get();
        $data['countries'] =$countries;
        $data['shippingCharge'] = $shippingCharge;

        return view('admin.shipping.edit',$data);


    }

    public function update(Request $request,$id){
            $validator =Validator::make($request->all(),[
            'country'=>'required',
            'amount'=>'required|numeric',
        ]);
        if ($validator->passes()) {

            $shipping = shippingCharge::find($id);
            $shipping->country_id=$request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success','Shipping Update successfuly');
            return response()->json([
                'status' => true,
                'success'=>'Shipping Updated successfuly',

            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors(),

            ]);
        }
    }

    public function destroy($id){

         $shippingCharge = shippingCharge::find($id);
         if ($shippingCharge == Null) {

            session()->flash('error','Record not found');

            return response()->json([
                'status' => true,

            ]);
         }
         $shippingCharge->delete();
        session()->flash('success','Shipping delete successfuly');
            return response()->json([
                'status' => true,
                'success'=>'Shipping delete successfuly',

            ]);
    }
}
