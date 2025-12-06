<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HomeController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ContactusController;
use App\Http\Controllers\ProductDashController;
use App\Http\Controllers\BarberProductController;
use App\Http\Controllers\BarberDashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\jobcreatepartner;
use App\Http\Controllers\marketplace;
use App\Http\Controllers\jobapplycontroller;
use App\Http\Controllers\outsidemarketplace;
use App\Http\Controllers\onboardingcontroller;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Web\CheckoutPageController;
use App\Http\Controllers\Web\PaymentReturnController;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    // Edit Profile Route

    Route::get('/barbersajax/{id}', [BarberController::class, 'barbersajax'])->name('barbersajax');



    // Admin side Route
    Route::get('/adminDashboard',[AdminDashboardController::class, 'index'])->name('adminDashboard');
    Route::get('listjobsadmin',[AdminDashboardController::class, 'adminjobslist'])->name('listjobsadmin');
        Route::get('Admin/Job/Delete/{id}',[AdminDashboardController::class, 'deletejob'])->name('Admin.Job.Delete');
   // view jobs
   Route::get('jobview/{id}', [AdminDashboardController::class,'job_view'])->name('jobview');


    Route::get('marketplacerentadmin',[AdminDashboardController::class, 'adminmarketrentlist'])->name('marketplacerentadmin');
    Route::get('marketplacesalonadmin',[AdminDashboardController::class, 'adminmarketsalonlist'])->name('marketplacesalonadmin');
    Route::get('marketplaceproductadmin',[AdminDashboardController::class, 'adminmarketproductlist'])->name('marketplaceproductadmin');

    // view market place product
    Route::get('marketplac_product_view/{id}',[AdminDashboardController::class, 'marketplace_productview'])->name('marketplac_product_view');
    Route::get('marketplace_rent_view/{id}',[AdminDashboardController::class, 'marketplace_rentview'])->name('marketplace_rent_view');
    Route::get('marketplace_salon_view/{id}',[AdminDashboardController::class, 'marketplace_salonview'])->name('marketplace_salon_view');


    Route::resource('contactus',ContactusController::class);

    Route::resource('commission',CommissionController::class);
    // Barbars Route--------------------------------------------

        // All Barbers
        Route::resource('barbers',BarberController::class);
        Route::get('barberactivestatus/{id}', [BarberController::class,'barberactivestatus'])->name('barberactivestatus');
        Route::get('paybarberAmount/{id}', [BarberController::class,'paybarberAmount'])->name('paybarberAmount');
        Route::get('barbercommitionpayment/{id}', [BarberController::class,'barbercommitionpayment'])->name('barbercommitionpayment');

        // // Delete
        Route::get('barbarDelete/{id}', [BarberController::class,'destroy'])->name('barbarDelete');
        // disable status
        Route::get('disabledstatus/{id}', [BarberController::class,'disabledstatus'])->name('disabledstatus');
        // Active Status
        Route::get('activestatus/{id}', [BarberController::class,'activestatus'])->name('activestatus');

        // Barber Profiler Routes  ProfileController
        Route::get('profile', [ProfileController::class,'index'])->name('profile');
        Route::get('barberprofileadmin/{id}', [ProfileController::class,'barberprofileadmin'])->name('barberprofileadmin');
        Route::get('barberappointmenthistory/{id}', [ProfileController::class,'barberappointmenthistory'])->name('barberappointmenthistory');
        Route::get('barberwallethistory/{id}', [ProfileController::class,'barberwallethistory'])->name('barberwallethistory');
        Route::patch('/updateprofile/{id}', [ProfileController::class, 'update'])->name('updateprofile');
        Route::post('/addbusinessbarber', [ProfileController::class, 'addBusnissBarber'])->name('addbusinessbarber');

        Route::post('add_documetns', [ProfileController::class,'adddocumetns'])->name('add_documetns');
        Route::post('add_slot', [ProfileController::class,'addslot'])->name('add_slot');
        Route::get('deleteslot/{id}', [ProfileController::class,'deleteslot'])->name('deleteslot');


        Route::get('completedStatus/{id}',[AppointmentController::class,'completedstatus'])->name('completedStatus');
        Route::get('refundPayment/{id}',[AppointmentController::class,'refundPayment'])->name('refundPayment');


        // Payment Hstory Fo Barber
        Route::get('barberpayment', [WalletController::class,'barberpayment'])->name('barberpayment');



    // Barbers Routes Ends



    // Appoiments Route--------------------------------------------

        Route::resource('appointment', AppointmentController::class);
        Route::get('barberAppointment', [AppointmentController::class,'barberAppointment'])->name('barberAppointment');
        // Route::get('appointmentDelete', [AppointmentController::class,'destroy'])->name('appointmentDelete');





    // Appoiments Route Ends--------------------------------------------

    // Services Route--------------------------------------------

        Route::resource('services',ServiceController::class);
        Route::get('servicesDelete/{id}', [ServiceController::class,'destroy'])->name('servicesDelete');


        // Route::post('servicesCreate', [ServiceController::class,'store'])->name('servicesCreate');

        // Route::get('servicesEdit/{id}', [ServiceController::class,'edit'])->name('servicesEdit');
        // Route::post('servicesUpdate', [ServiceController::class,'update'])->name('servicesUpdate');




    // Services Route Ends--------------------------------------------


    // Category Routes ---------------------------------------------------

    Route::resource('category', CategoryController::class);
    Route::get('categorydestroy/{id}', [CategoryController::class,'destroy'])->name('categorydestroy');

    // Category Routes Ends ---------------------------------------------------



     //Products Routes --------------------------------------------------
     Route::resource('productdash',ProductDashController::class);

    //  Route::get('products', [ProductController::class,'index'])->name('products');


     // Add Products
     Route:: resource('adminproducts',ProductController::class);

     Route:: post('productstore', [ProductController::class,'store'])->name('productstore');
    //  Stock Routes
     Route:: get('stocklist', [ProductController::class,'stocklist'])->name('stocklist');
     Route:: get('stock', [ProductController::class,'stock'])->name('stock');
     Route:: post('stockstore', [ProductController::class,'stockstore'])->name('stockstore');
     Route:: get('stockdelete/{id}', [ProductController::class,'stockdelete'])->name('stockdelete');
     Route:: get('currentstock', [ProductController::class,'currentstock'])->name('currentstock');

     // Update And Delete
     Route::get('productedit/{id}', [ProductController::class,'edit'])->name('productedit');
     Route::get('productdestroy/{id}', [ProductController::class,'destroy'])->name('productdestroy');


     // Products Routes End --------------------------------------------------------------------


     Route::resource('barberproducts', BarberProductController::class);
     Route::get('assignproductdelete/{id}', [BarberProductController::class,'destroy'])->name('assignproductdelete');


    //  Ajax Request
     Route::post('categoryProduct', [BarberProductController::class,'categoryProduct'])->name('categoryProduct');




     Route::get('subscription/create', [SubscriptionController::class,'index'])->name('subscription.create');
     Route::post('order-post', [SubscriptionController::class,'orderPost'])->name('order-post');


    // Wallet Controller
    Route::get('wallet', [WalletController::class,'index']);
    Route::get('productwallet', [WalletController::class,'productwallet'])->name('productwallet');

    Route::post('ratingstore', [RatingController::class,'store'])->name('ratingstore');





    // BARBER PRODUCTS
    Route::get('barberproduct', [BarberProductController::class,'barberproduct'])->name('barberproduct');
    Route::get('approve/{id}', [BarberProductController::class,'approve'])->name('approve');
    Route::get('barberstock', [BarberProductController::class,'barberstock'])->name('barberstock');


// barber partner jobs

Route::get('partnercreatejob', [jobcreatepartner::class,'index'])->name('partnercreatejob');
Route::post('storepartnerjob', [jobcreatepartner::class,'storepartnerjob'])->name('storepartnerjob');
Route::get('job_view_barber/{id}', [jobcreatepartner::class,'job_view'])->name('job_view_barber');


// barber market place rent a chairs

Route::get('marketrentchair', [marketplace::class,'marketrent'])->name('marketrentchair');
Route::post('storemarketrentchair', [marketplace::class,'storerentchair'])->name('storemarketrentchair');
Route::get('deletemarketrentchair/{id}', [marketplace::class,'deletemarketrent'])->name('deletemarketrentchair');
Route::get('editmarketrent/{id}', [marketplace::class,'editmarketrent'])->name('editmarketrent');
Route::post('storeeditrentachair', [marketplace::class,'storeeditrentachair'])->name('storeeditrentachair');
// all market rent chairs
Route::get('marketallrentchair', [marketplace::class,'marketrentall'])->name('marketallrentchair');

// barber market place salon sell

Route::get('marketsalonsell', [marketplace::class,'marketsalon'])->name('marketsalonsell');
Route::post('storemarketsalon', [marketplace::class,'storesalon'])->name('storemarketsalon');
Route::get('deletesalonsell/{id}', [marketplace::class,'deletesalon'])->name('deletesalonsell');
Route::get('editmarketsalon/{id}', [marketplace::class,'editmarketsalon'])->name('editmarketsalon');
Route::post('storemarketsalonupdated', [marketplace::class,'editsalonsell'])->name('storemarketsalonupdated');
// all market place salons
Route::get('marketallsalonsell', [marketplace::class,'marketallsalon'])->name('marketallsalonsell');


// barber market place products

Route::get('marketproducts', [marketplace::class,'marketproduct'])->name('marketproducts');
Route::post('storemarketproducts', [marketplace::class,'storeproducts'])->name('storemarketproducts');
Route::get('deletemarketproduct/{id}', [marketplace::class,'deleteproduct'])->name('deletemarketproduct');
Route::get('editmarketproduct/{id}', [marketplace::class,'editmarketproduct'])->name('editmarketproduct');
Route::post('storeeditproduct', [marketplace::class,'editmarket'])->name('storeeditproduct');
// list all products
Route::get('marketallproducts', [marketplace::class,'marketallproduct'])->name('marketallproducts');

// market place view
    // view market place product
    Route::get('marketproduct_view/{id}',[marketplace::class, 'marketplace_productview'])->name('marketproduct_view');
    Route::get('marketrent_view/{id}',[marketplace::class, 'marketplace_rentview'])->name('marketrent_view');
    Route::get('marketsalon_view/{id}',[marketplace::class, 'marketplace_salonview'])->name('marketsalon_view');


    //  personal info for job routes
    Route::get('infojobapply', [jobapplycontroller::class,'index'])->name('infojobapply');
    Route::post('storepersonalinfo', [jobapplycontroller::class,'storeinfo'])->name('storepersonalinfo');
    Route::get('editpersonalinfo', [jobapplycontroller::class,'editjobinfo'])->name('editpersonalinfo');
    Route::post('storeeditpersonalinfo', [jobapplycontroller::class,'storeinfoupdate'])->name('storeeditpersonalinfo');

    //    //   job apply
    Route::get('getjobdata/{id}', [jobapplycontroller::class,'getjobdata'])->name('getjobdata');
    Route::post('jobapplynow', [jobapplycontroller::class,'jobapplynow'])->name('jobapplynow');
    Route::get('getappliedjobs', [jobapplycontroller::class,'getappliedjobs'])->name('getappliedjobs');

    // Route::get('getjobapplication/{id}', [parsonalinfoforjobandapply::class,'getapplication'])->name('getjobapplication');


       //    //   onboarding screens
       Route::get('boarding_screen', [onboardingcontroller::class,'index'])->name('boarding_screen');
       Route::post('storeonboardingimage', [onboardingcontroller::class,'startscreenimage'])->name('storeonboardingimage');
       Route::get('deleteonboardingstart/{id}', [onboardingcontroller::class,'deletestartimage'])->name('deleteonboardingstart');
       Route::get('boarding_home_screen', [onboardingcontroller::class,'homeimage'])->name('boarding_home_screen');
       Route::post('storeonboarding_home_image', [onboardingcontroller::class,'homescreenimage'])->name('storeonboarding_home_image');
       Route::get('deleteonboardinghome/{id}', [onboardingcontroller::class,'deletehomeimage'])->name('deleteonboardinghome');






    // BARBER PRODUCT ORDER
    Route::resource('orders', OrderController::class);


    // BARBER PRODUCT ORDER
    Route::resource('codes', CodeController::class);
    Route::get('confirm/{id}/{order}', [CodeController::class,'show'])->name('confirm');

    // ORDER LIST FOR ADMIN
    Route::get('adminproductorder',[OrderController::class,'adminproductorder'])->name('adminproductorder');
    Route::get('orderInvoice/{id}',[OrderController::class,'orderInvoice'])->name('orderInvoice');
    Route::get('orderInvoiceViewToBarber/{id}',[OrderController::class,'orderInvoiceViewToBarber'])->name('orderInvoiceViewToBarber');
    Route::get('barberproductorder',[OrderController::class,'barberproductorder'])->name('barberproductorder');

});

Route::get('/testemail',function(){
    return view('admin.Email.cancel');
});

// outside market place

Route::get('outsidemarket', [outsidemarketplace::class,'marketpage'])->name('outsidemarket');
// Route::post('storemarketproducts', [marketplace::class,'storeproducts'])->name('storemarketproducts');
// Route::get('deletemarketproduct/{id}', [marketplace::class,'deleteproduct'])->name('deletemarketproduct');
// Route::get('editmarketproduct/{id}', [marketplace::class,'editmarketproduct'])->name('editmarketproduct');
// Route::post('storeeditproduct', [marketplace::class,'editmarket'])->name('storeeditproduct');
// Route::get('/checkout/{order}', [CheckoutPageController::class,'show']); // serves HTML + opayoCheckout
// Route::get('/payment-return', [PaymentReturnController::class,'handle']);


Route::get('/checkout/{order}/{appointment}', [PaymentController::class,'checkoutPage'])->name('checkout.page');
Route::post('/payment-return', [PaymentController::class,'paymentReturn']); // optional callback/return page
