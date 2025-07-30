<?php

namespace App\Http\Controllers\admin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OrderController implements HasMiddleware
{
     public static function middleware(): array
        {
           return [
            (new Middleware('permission:orders view'))->only(['index']),
            (new Middleware('permission:orders detail'))->only(['detail']),
            (new Middleware('permission:orders changeOrderStatus'))->only(['changeOrderStatus']),
            (new Middleware('permission:orders sendInvoiceEmail'))->only(['sendInvoiceEmail']),
        ];
        }
    public function index(Request $request){

        $orders = Order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders= $orders->leftjoin('users','users.id','orders.user_id');
        if ($request->get('keyword') != "") {

            $orders=$orders->where('users.name','like','%'.$request->keyword.'%');
             $orders=$orders->orwhere('users.email','like','%'.$request->keyword.'%');
              $orders=$orders->orwhere('orders.id','like','%'.$request->keyword.'%');
        }
        $orders=$orders->paginate(10);

        $data['orders'] = $orders;
        return view('admin.order.list',$data);
    }

    public function detail($orderId){
        $order = Order::select('orders.*','countries.name as countryName')->leftJoin('countries','countries.id','orders.country_id')->where('orders.id',$orderId)->first();
        $orderItem = OrderItem::where('order_id',$orderId)->get();

        $data['order'] = $order;
        $data['orderItem'] = $orderItem;
 return view('admin.order.detail',$data);
    }
    public function changeOrderStatus(Request $request,$orderId){
        $order=Order::find($orderId);
        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();
        Session()->flash('success','Status Change Successfuly');
        return response()->json([
            'status'=>true,
            'message'=>'status changes successfully',
        ]);
    }

    public function sendInvoiceEmail(Request $request,$orderId){
       OrderEmail($orderId,$request->userType);
       $message='Order email send successfuly';

       session()->flash('success',$message);
       return response()->json([
        'status' =>true,
        'message'=>$message,
       ]);
    }
}