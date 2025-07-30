<?php
use App\Models\Order;
use App\Mail\OrderEmail;
use App\Models\Country;
use App\Models\Page;
use App\Models\Category;
use App\Models\productImage;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Mail;
function getcategories(){
    return  Category::orderBy('name','ASC')->with('sub_categories')->where('status','1')->where('showHome','Yes')->get();
}

function getPorductImage($productId){
    return productImage::where('product_id',$productId)->first();
}

function orderEmail($orderId,$usertype="customer"){
$order = Order::where('id',$orderId)->with('items')->first();
if ($usertype == 'customer') {
    $subject ='Thanks for your order';
    $email = $order->email;
}else{
    $subject='you receive order';
    $email = env('ADMIN_EMAIL');
}
$mailData=[
    'subject' =>$subject,
    'order'=>$order,
    'userType'=>$usertype
];
Mail::to($email)->send(new OrderEmail($mailData));
}

function getCountryInfo($id){
    return Country::where('id',$id)->first();
}

function staticPages(){
    $pages=Page::orderBy('name','ASC')->get();
    return $pages;
}
?>
