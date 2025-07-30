<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SubcategoryController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\admin\DiscountCodeController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\PermissionController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test',function(){
orderEmail(51);
});


Route::get('/',[FrontController::class,'index'])->name('front.home');

Route::get('/shop/{categorySlug?}/{subcategorySlug?}',[ShopController::class,'index'])->name('front.shop');

Route::get('/product/{slug}', [ShopController::class, 'product'])->name('front.product');

//cart
Route::get('/cart',[CartController::class,'cart'])->name('front.cart');

Route::post('/add-to-cart',[CartController::class,'addToCart'])->name('front.addToCart');

Route::post('/update-cart',[CartController::class,'updateCart'])->name('front.updateCart');

Route::post('/delete-cart',[CartController::class,'deleteItem'])->name('front.deleteItem.cart');

Route::get('/checkout',[CartController::class,'checkout'])->name('front.checkout');

Route::post('/process-checkout',[CartController::class,'processCheckout'])->name('front.processCheckout');

Route::get('/thanks/{id}',[CartController::class,'thankyou'])->name('front.thankyou');

Route::post('/get-order-summery',[CartController::class,'getOrderSummery'])->name('front.getOrderSummery');

Route::post('/apply-discount',[CartController::class,'applyDiscount'])->name('front.applyDiscount');

Route::post('/remove-discount',[CartController::class,'removeCoupon'])->name('front.removeCoupon');

Route::post('/add-to-wishlist',[FrontController::class,'addToWishList'])->name('front.addToWishList');

Route::get('/page/{slug}',[FrontController::class,'page'])->name('front.page');

Route::post('/send-contact-email',[FrontController::class,'sendContactEmail'])->name('front.sendContactEmail');

Route::get('/forgot-password',[AuthController::class,'forgotPassword'])->name('front.forgotPassword');

Route::post('/process-forgot-password',[AuthController::class,'processForgotPassword'])->name('front.processForgotPassword');

Route::get('/reset-password/{token}',[AuthController::class,'resetPassword'])->name('front.resetPassword');

Route::post('/process-password',[AuthController::class,'processResetPassword'])->name('front.processResetPassword');




//

Route::group(['prefix' => 'account'], function() {

 Route::group(['middleware'=>'guest'],function(){
//Account log in nad register
Route::get('/register',[AuthController::class,'register'])->name('account.register');

Route::get('/login',[AuthController::class,'login'])->name('account.login');

Route::post('/process-register',[AuthController::class,'processRegister'])->name('account.processRegister');

   Route::post('/authenticate',[AuthController::class,'authenticate'])->name('account.authenticate');



 });

Route::group(['middleware'=>'auth'],function(){

Route::get('/profile',[AuthController::class,'profile'])->name('account.profile');

Route::post('/update-profile',[AuthController::class,'updateProfile'])->name('account.updateProfile');

Route::post('/update-address',[AuthController::class,'updateAddress'])->name('account.updateAddress');

Route::get('/logout',[AuthController::class,'logout'])->name('account.logout');

Route::get('/my-oders',[AuthController::class,'orders'])->name('account.orders');

Route::get('/oder-detail/{orderId}',[AuthController::class,'orderDetail'])->name('account.orderDetail');

Route::get('/my-wishlist',[AuthController::class,'wishlist'])->name('account.wishlist');

Route::post('/remove-product-from-wishlist',[AuthController::class,'removeProductFromWishlist'])->name('account.removeProductFromWishlist');

Route::get('/change-password',[AuthController::class,'showchangePasswordForm'])->name('account.showchangePasswordForm');

Route::Post('/process-change-password',[AuthController::class,'changePassword'])->name('account.changePassword');





 });
});

Route::group(['prefix' => 'admin'], function() {
    Route::group(['middleware'=>'admin.guest'],function(){
        Route::get('/login',[AdminLoginController::class,'index'])->name('admin.login');
          Route::post('/authenticate',[AdminLoginController::class,'authenticate'])->name('admin.authenticate');


    });

    Route::group(['middleware'=>'admin.auth'],function(){
        Route::get('/dashboard',[HomeController::class,'index'])->name('admin.dashboard');

        Route::get('/logout',[HomeController::class,'logout'])->name('admin.logout');

        //change admin password
         Route::get('/change-password',[SettingController::class,'showChangePassword'])->name('admin.showChangePassword');

        Route::post('/process-change-password',[SettingController::class,'processShowChangePassword'])->name('admin.processShowChangePassword');
        //categories



         Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');

         Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
         Route::post('/categories',[CategoryController::class,'store'])->name('categories.store');

         Route::post('/upload-temo-image',[TempImageController::class,'create'])->name('temp-images.create');

         Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('categories.edit');

         Route::put('/categories/{category}',[CategoryController::class,'update'])->name('categories.update');


         Route::delete('/categories/{category}',[CategoryController::class,'destroy'])->name('categories.delete');

         //subcategory
         Route::get('/sub-categories',[SubCategoryController::class,'index'])->name('sub-categories.index');
         Route::get('/sub-categories/create',[SubcategoryController::class,'create'])->name('sub-categories.create');
        Route::post('/sub-categories',[SubcategoryController::class,'store'])->name('sub-categories.store');

        Route::get('/sub-categories/{subcategory}/edit',[SubCategoryController::class,'edit'])->name('sub-categories.edit');

        Route::put('/sub-categories/{subcategory}',[SubCategoryController::class,'update'])->name('sub-categories.update');

        Route::delete('/sub-categories/{subcategory}',[SubCategoryController::class,'destroy'])->name('sub-categories.delete');

        //Brand
        Route::get('/brands',[BrandController::class,'index'])->name('brands.index');
        Route::get('/brands/create',[BrandController::class,'create'])->name('brands.create');
        Route::post('//brands',[BrandController::class,'store'])->name('brands.store');
        Route::get('/brands/{brands}/edit',[BrandController::class,'edit'])->name('brands.edit');
        Route::put('/brands/{brands}',[BrandController::class,'update'])->name('brands.update');
        Route::delete('/brands/{brands}',[BrandController::class,'destroy'])->name('brands.delete');
        //users

        Route::get('/users',[UserController::class,'index'])->name('users.index');
        Route::get('/users/create',[UserController::class,'create'])->name('users.create');
        Route::post('/users',[UserController::class,'store'])->name('users.store');
        Route::get('/users/{users}/edit',[UserController::class,'edit'])->name('users.edit');
        Route::put('/users/{users}',[UserController::class,'update'])->name('users.update');
        Route::delete('/users/{users}',[UserController::class,'destroy'])->name('users.delete');

         //Permissions

        Route::get('/permissions',[PermissionController::class,'index'])->name('permissions.index');
        Route::get('/permissions/create',[PermissionController::class,'create'])->name('permissions.create');
        Route::post('/permissions',[PermissionController::class,'store'])->name('permissions.store');
        Route::get('/permissions/{permissions}/edit',[PermissionController::class,'edit'])->name('permissions.edit');
        Route::put('/permissions/{permissions}',[PermissionController::class,'update'])->name('permissions.update');
        Route::delete('/permissions/{permissions}',[PermissionController::class,'destroy'])->name('permissions.delete');

        //Roles

        Route::get('/roles',[RoleController::class,'index'])->name('roles.index');
        Route::get('/roles/create',[RoleController::class,'create'])->name('roles.create');
        Route::post('/roles',[RoleController::class,'store'])->name('roles.store');
        Route::get('/roles/{roles}/edit',[RoleController::class,'edit'])->name('roles.edit');
        Route::put('/roles/{roles}',[RoleController::class,'update'])->name('roles.update');
        Route::delete('/roles/{roles}',[RoleController::class,'destroy'])->name('roles.delete');

        //Pages
        Route::get('/pages',[PageController::class,'index'])->name('pages.index');
        Route::get('/pages/create',[PageController::class,'create'])->name('pages.create');
        Route::post('/pages',[PageController::class,'store'])->name('pages.store');
        Route::get('/pages/{pages}/edit',[PageController::class,'edit'])->name('pages.edit');
        Route::put('/pages/{pages}',[PageController::class,'update'])->name('pages.update');
        Route::delete('/pages/{pages}',[PageController::class,'destroy'])->name('pages.delete');


        //products

        Route::get('/products',[ProductController::class,'index'])->name('products.index');
        Route::get('/products/create',[ProductController::class,'create'])->name('products.create');
        Route::post('/products',[ProductController::class,'store'])->name('products.store');
        Route::get('/products/{products}/edit',[ProductController::class,'edit'])->name('products.edit');
        Route::put('/products/{products}',[ProductController::class,'update'])->name('products.update');
        Route::delete('/products/{products}',[ProductController::class,'destroy'])->name('products.delete');
        Route::post('/upload-temo-image',[TempImageController::class,'create'])->name('temp-images.create');

        Route::get('/get-products',[ProductController::class,'getProducts'])->name('products.getProducts');


        Route::post('/product_images/update',[ProductImageController::class,'update'])->name('product_images.update');
         Route::delete('/product_images',[ProductImageController::class,'destroy'])->name('product_images.destroy');
        //sub Product

        Route::get('/product-subcategories',[ProductSubCategoryController::class,'index'])->name('product-subcategories.index');


        //shipping

        Route::get('/shipping/create',[ShippingController::class,'create'])->name('shipping.create');

        Route::post('/shipping',[ShippingController::class,'store'])->name('shipping.store');

        Route::get('/shipping/index',[ShippingController::class,'index'])->name('shipping.index');

         Route::get('/shipping/{id}',[ShippingController::class,'edit'])->name('shipping.edit');

          Route::put('/shipping/{id}',[ShippingController::class,'update'])->name('shipping.update');


        Route::delete('/shipping/{id}',[ShippingController::class,'destroy'])->name('shipping.delete');

        //discount coupon
        Route::get('/coupons/index',[DiscountCodeController::class,'index'])->name('coupons.index');
        Route::get('/coupons/create',[DiscountCodeController::class,'create'])->name('coupons.create');
         Route::post('coupons',[DiscountCodeController::class,'store'])->name('coupons.store');
         Route::get('coupons/{id}',[DiscountCodeController::class,'edit'])->name('coupons.edit');
          Route::put('coupons/{id}',[DiscountCodeController::class,'update'])->name('coupons.update');
           Route::delete('coupons/{id}',[DiscountCodeController::class,'destroy'])->name('coupons.delete');

        //Orders
         Route::get('/orders',[OrderController::class,'index'])->name('orders.index');

          Route::get('/orders-detail/{orderId}',[OrderController::class,'detail'])->name('orders.detail');
           Route::post('/orders/change-status/{id}',[OrderController::class,'changeOrderStatus'])->name('orders.changeOrderStatus');
            Route::post('/orders/send-email/{id}',[OrderController::class,'sendInvoiceEmail'])->name('orders.sendInvoiceEmail');

         //ajax

          Route::get('/getSlug',function(Request $request){
            $slug = '';
           if (!empty($request->title)) {
                $slug =Str::slug( $request->title);
           }
           return response()->json([
            'status' => true,
            'slug' => $slug
           ]);
          })->name('getSlug');


    });
});
