<?php

use App\Http\Controllers\Api\BarberApiController;
use App\Http\Controllers\Api\BarberAuthApiController;

// BARBER CONTROLLERS
use App\Http\Controllers\Api\BarberProductApiController;
use App\Http\Controllers\Api\marketplacecontroller;
use App\Http\Controllers\Api\parsonalinfoforjobandapply;

// MARKET PLACE CONTROLLERS
use App\Http\Controllers\Api\productcartapi;

// SUBSCRIPTION PACKAGE CONTROLLERS
use App\Http\Controllers\Api\subscriptioncontroller;

// PRODUCT CART CONTROLLER
use App\Http\Controllers\Api\product_rating_controller;
// PRODUCT wallet CONTROLLER
use App\Http\Controllers\ProductWalletController;

//  Qr code CONTROLLERS
use App\Http\Controllers\Api\Qrcodecontroller;

// PARSIONAL INFO FOR JOB
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentRescheduleController;
// CUSTOMER CONTROLLERS
use App\Http\Controllers\BarberController;
use App\Http\Controllers\CategoryController;

// APPOINTMENT CONTROLLER
use App\Http\Controllers\ContactusController;

// APPOINTMENT CONTROLLER
use App\Http\Controllers\CustomerAuthController;

// Cateogry
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FcmController;
// Products Payments
use App\Http\Controllers\OrderController;

// Rating
use App\Http\Controllers\PackageController;

// Product rating controller
use App\Http\Controllers\partnerjobcreatecontroller;

// Barber Api Controller
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;

// partner job creation
use Illuminate\Support\Facades\Route;

// onboarding images
use App\Http\Controllers\onboardingcontroller;

use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\RefundController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

/*
------------------------------                    ---------------------------------------------------
BARBER APIS
------------------------------                    ---------------------------------------------------- */

// For admin Pricing
Route::get('pricing', [CustomerController::class, 'pricing']);

// For Contact us
Route::post('contactus', [ContactusController::class, 'store']);

// Products
Route::get('products', [ProductController::class, 'productsforapi']);
Route::get('search_products', [ProductController::class, 'searchproducts'])->name('search_products');


// Categories
Route::get('category', [CategoryController::class, 'categoryforapi']);

Route::group(['prefix' => 'barber'], function () {
    // BARBER Registration From  Website
    Route::post('register', [BarberController::class, 'signup']);

    // BARBER APis For App____________________________//////////

    // Registration
    Route::post('registration', [BarberAuthApiController::class, 'registration']);
    // Login
    Route::post('login', [BarberAuthApiController::class, 'login']);

    // For change Password
    Route::post('request_change_password_barber', [BarberApiController::class, 'request_change_password_barber']);
    Route::post('request_update_password_barber', [BarberApiController::class, 'request_update_password_barber']);

    // BARBER QR code api
    Route::get('Qrcode/{id}', [Qrcodecontroller::class, 'qrcode'])->name('Qrcode');

    Route::group(['middleware' => ['assign.guard:barber', 'jwt.auth']], function () {

        // Barber Profile
        Route::get('profile', [BarberAuthApiController::class, 'profile']);

        // Check Token !is same or not
        Route::post('checktoken', [BarberAuthApiController::class, 'tokenupdate']);

        // Barber Dashboard
        Route::get('dashboard', [BarberApiController::class, 'dashboard']);

        Route::get('check-available-slots', [BarberApiController::class, 'checkavailableslots']);

        // Barber Adding Documents
        Route::post('adddocumetns', [BarberApiController::class, 'adddocumetns']);

        // Barber Documents
        Route::get('documents', [BarberApiController::class, 'documents']);

        // Barber All Slot
        Route::get('slots', [BarberApiController::class, 'slots']);

        // Barber Add Slot
        Route::post('addslot', [BarberApiController::class, 'addslot']);

        // Barber Add Slot
        Route::get('deleteslot/{id}', [BarberApiController::class, 'deleteslot']);

        Route::post('/appointment-reschedule-request/{appointmentId}', [AppointmentRescheduleController::class, 'requestReschedule']);
        Route::get('/appointment-reschedules', [AppointmentRescheduleController::class, 'listBarberReschedules']);
        // Barber Apointments
        Route::get('barberAppointment', [BarberApiController::class, 'barberAppointment']);
        Route::post('update_app', [BarberApiController::class, 'update_app'])->name('update_app');
        Route::post('updatepro_wallet', [BarberApiController::class, 'update_wallet'])->name('updatepro_wallet');
        Route::get('barber_notification', [BarberApiController::class, 'barber_notification'])->name('barber_notification');
        Route::get('barber_count_notification', [BarberApiController::class, 'barber_countnotification'])->name('barber_count_notification');

        // Update Profile
        Route::post('updateprofile', [BarberApiController::class, 'updateprofile']);

        // Barber Apointments
        Route::get('completeAppoint/{id}', [BarberApiController::class, 'completeAppoint']);

        // add Services for barber

        // Barber Add Services
        Route::post('addservice', [BarberApiController::class, 'addservice']);
        // Salon Services
        Route::get('services', [BarberApiController::class, 'services']);
        Route::get('servicedelete/{id}', [BarberApiController::class, 'servicedelete']);

        // -----------------------------Products Related Routes --------------------------------------

        Route::get('barberproduct', [BarberProductApiController::class, 'barberproduct']);

        // Barber Product Stock (Current)
        Route::get('barberstock', [BarberProductApiController::class, 'barberstock']);

        // Barber Product Code Verification APi
        Route::post('codeverifcation', [BarberProductApiController::class, 'codeverifcation']);

        // Barber Product Deliver  APi
        Route::get('productdeliver/{id}/{order}', [BarberProductApiController::class, 'productdeliver']);

        // Barber Stock Approval APi
        Route::get('stockapprove/{id}', [BarberProductApiController::class, 'stockapprove']);

        // Barber Trasaction details
        Route::get('barberpayment', [BarberApiController::class, 'barberpayment']);

        // Barber Orders details
        Route::get('orders', [BarberProductApiController::class, 'orders']);

        Route::post('/addbusinessbarber', [BarberApiController::class, 'addBusnissBarber']);
        Route::get('/showbarbertosalon', [BarberApiController::class, 'ShowBarberToSalon']);
        Route::get('/barberrating/{id}', [BarberApiController::class, 'barberRating']);
          Route::get('delete_barber/{id}', [BarberApiController::class, 'deletebarber'])->name('delete_barber');

        //    create job
        Route::post('createjob', [partnerjobcreatecontroller::class, 'store'])->name('createjob');
        Route::get('getalljobs/{id}', [partnerjobcreatecontroller::class, 'getalljobs'])->name('getalljobs');
        Route::post('updatecreatedjob', [partnerjobcreatecontroller::class, 'updatecreatedjob'])->name('updatecreatedjob');

        Route::get('getcreatejobdata', [partnerjobcreatecontroller::class, 'getdata'])->name('getcreatejobdata');
        Route::get('viewcreatejob_details/{id}', [partnerjobcreatecontroller::class, 'viewjobdetails'])->name('viewcreatejob_details');
        Route::get('deletecreaterjob/{id}', [partnerjobcreatecontroller::class, 'deletejob'])->name('deletecreaterjob');

        // market place routes
        //  market rent a chair
        Route::post('marketrentstore', [marketplacecontroller::class, 'storerent'])->name('marketrentstore');
        Route::get('getmarketrent', [marketplacecontroller::class, 'getmarketrent'])->name('getmarketrent');
        Route::get('getmarketrentdetail/{id}', [marketplacecontroller::class, 'getmarketrentdetails'])->name('getmarketrentdetail');
        Route::get('getallmarketrent', [marketplacecontroller::class, 'getallmarketrent'])->name('getallmarketrent');

Route::get('getmarketrentdelete/{id}', [marketplacecontroller::class, 'deletemarketrent'])->name('getmarketrentdelete');

        // market salon sales
        Route::post('marketsalonstore', [marketplacecontroller::class, 'storesalonsell'])->name('marketsalonstore');
        Route::get('getmarketsalon', [marketplacecontroller::class, 'getmarketsalon'])->name('getmarketsalon');
        Route::get('getmarketsalonsaledetails/{id}', [marketplacecontroller::class, 'getmarketsalonsaledetails'])->name('getmarketsalonsaledetails');
        Route::get('getmarketsalonall', [marketplacecontroller::class, 'getmarketsalonall'])->name('getmarketsalonall');
   Route::get('getmarketsalondelete/{id}', [marketplacecontroller::class, 'deletesalon'])->name('getmarketsalondelete');
        // market place products
        Route::post('marketproductstore', [marketplacecontroller::class, 'storeproduct'])->name('marketproductstore');
        Route::get('getmarketproducts', [marketplacecontroller::class, 'getmarketproduct'])->name('getmarketproducts');
        Route::get('getmarketproductdetail/{id}', [marketplacecontroller::class, 'getmarketproductdetails'])->name('getmarketproductdetail');
        Route::get('getmarketproductsall', [marketplacecontroller::class, 'getmarketproductsall'])->name('getmarketproductsall');
    Route::get('getmarketproductdelete/{id}', [marketplacecontroller::class, 'deleteproduct'])->name('getmarketproductdelete');
        // product add to cart
        Route::post('addtocart', [productcartapi::class, 'storecart'])->name('addtocart');

        //  personal info for job routes
        Route::post('storepersonalforjob', [parsonalinfoforjobandapply::class, 'storeinfo'])->name('storepersonalforjob');
        Route::get('getpersonalinfo/{id}', [parsonalinfoforjobandapply::class, 'getinfo'])->name('getpersonalinfo');
        Route::post('storeupdateinfo', [parsonalinfoforjobandapply::class, 'storeinfoupdate'])->name('storeupdateinfo');

        //   job apply
        Route::post('jobapply', [parsonalinfoforjobandapply::class, 'jobapply'])->name('jobapply');
        Route::get('getjobapplication/{id}', [parsonalinfoforjobandapply::class, 'getapplication'])->name('getjobapplication');

        Route::get('getappliedjobs', [parsonalinfoforjobandapply::class, 'getappliedjobs'])->name('getappliedjobs');

    });

});

// outside marketplace api
Route::get('getallmarketrents', [marketplacecontroller::class, 'getallmarketrents'])->name('getallmarketrents');
Route::get('getallmarketsalon', [marketplacecontroller::class, 'getallmarketsalon'])->name('getallmarketsalon');
Route::get('getallmarketproduct', [marketplacecontroller::class, 'getallmarketproduct'])->name('getallmarketproduct');

// outside job portal

Route::get('getoutsidejobs', [partnerjobcreatecontroller::class, 'getalljoboutside'])->name('getoutsidejobs');

// onboarding
Route::get('startimages', [onboardingcontroller::class, 'get_startimages'])->name('startimages');
Route::get('homeimages', [onboardingcontroller::class, 'get_homeimages'])->name('homeimages');


/*
------------------------------                    ---------------------------------------------------
CUSTOEMERS APIS
------------------------------                    ---------------------------------------------------- */

Route::group(['prefix' => 'customer'], function () {
    // CUSTOMER Registration
    Route::post('register', [CustomerController::class, 'signup']);

    // CUSTOMER Login
    Route::post('login', [CustomerAuthController::class, 'login']);

    Route::post('barbersforhome', [BarberController::class, 'barbersforhome']);

    // api for getting slote of the barber
    Route::get('businessBarberSlot/{id}', [BarberController::class, 'businessBarberSlot']);

    // api for getting barbers Againts Business
    Route::get('barberagainstbusiness/{id}', [BarberController::class, 'barberagainstbusiness']);

    Route::get('notification', [BarberApiController::class, 'notificatoin']);

    Route::get('barberrating/{id}', [BarberAuthApiController::class, 'getbarberratinglist']);

    // For change Password
    Route::post('request_change_password', [CustomerController::class, 'request_change_password']);
    Route::post('request_update_password', [CustomerController::class, 'request_update_password']);

    Route::group(['middleware' => ['assign.guard:customer', 'jwt.auth']], function () {
        // PROFILE
        Route::get('profile', [CustomerController::class, 'index']);

        Route::post('customer_profile_update', [CustomerController::class, 'profileUpdate']);

        //  Barber For Appointment
        Route::post('barbers', [BarberController::class, 'barbers']);

        //  Barber Details For Appointment
        Route::get('barberdetails/{id}', [BarberController::class, 'barberdetails']);

        // Appointments
        Route::post('appointment', [AppointmentController::class, 'store']);

        // Create payment
        Route::post('/payment-order', [PaymentController::class, 'createPaymentOrder']);

        Route::get('/reschedule-requests', [AppointmentRescheduleController::class, 'listCustomerReschedules']);

        Route::post('/appointment-reschedule-response/{rescheduleId}', [AppointmentRescheduleController::class, 'respondReschedule']);

        Route::post('appointmentajax', [AppointmentController::class, 'appointmentajax']);
        Route::post('cancleapp', [AppointmentController::class, 'cancleapp']);
        Route::get('cancleApi/{id}', [AppointmentController::class, 'cancleApi']);
        Route::get('showappdetals/{id}', [AppointmentController::class, 'show']);
        Route::get('notification_appointment', [AppointmentController::class, 'notificationappointment'])->name('notification_appointment');
	  Route::post('update-appointment/{id}', [AppointmentController::class, 'updateappointment'])->name('update-appointment');
        Route::get('count_notification', [AppointmentController::class, 'countnotification'])->name('count_notification');



        // CUSTOEMR DASHBOARDS ROUTE
        Route::get('customerappoinments', [CustomerController::class, 'customerappoinments']);
          Route::get('customersorders', [CustomerController::class, 'cusorders']);
        Route::get('customerorders', [CustomerController::class, 'customerorders']);
          Route::get('customerorderscancl/{id}', [CustomerController::class, 'customerordercancl'])->name('customerorderscancl');
        Route::get('orderdetail/{id}', [CustomerController::class, 'orderdetail']);

        Route::get('serviceamount/{id}', [CustomerController::class, 'serviceamount'])->name('serviceamount');

        Route::post('payment', [CustomerController::class, 'orderPost'])->name('payment');
        //  Payment From Api Side
        Route::post('mobilepayment', [CustomerController::class, 'orderPostfromapi']);

        //  Product Payments
        Route::post('productPayment', [OrderController::class, 'productPayment']);
        Route::post('mobileproductPayment', [OrderController::class, 'mobileproductPayment']);

        //  Rating Apppointment
        Route::post('rating', [RatingController::class, 'store']);
        Route::get('getbarberrating/{id}', [RatingController::class, 'getbarberrating']);

        // Check Token !is same or not
        Route::post('checktoken', [CustomerController::class, 'tokenupdate']);

        // get slote of the barber to show customer
        Route::get('slots/{id}', [BarberApiController::class, 'slots']);

        Route::get('check-available-slots/{barberId}', [BarberApiController::class, 'checkavailableslotsForCustomer']);

        //  Package APis
        Route::post('package', [PackageController::class, 'store']);
        Route::get('packages', [PackageController::class, 'index']);

        // admin service
        Route::get('package_services', [PackageController::class, 'package_services']);

        // product rating apis
        Route::post('product_rating', [product_rating_controller::class, 'store_product_rating'])->name('product_rating');

        // product rating apis
        Route::get('salon_rating/{id}', [RatingController::class, 'salon_rating'])->name('salon_rating');

      // subscriptin package apis
      Route::post('subscription_package', [subscriptioncontroller::class, 'subscription'])->name('subscription_package');
      // product wallet update api
      Route::post('update_product_wallet', [ProductWalletController::class, 'updateproductwallet'])->name('update_product_wallet');

    });

});


Route::post('sendNotification', [FcmController::class, 'sendNotification']);


// Payment routes

Route::get('/payment-order/{order}/status', [PaymentController::class, 'orderStatus']);
// Route::post('/transactions', [PaymentController::class, 'registerTransaction']); // receives cardIdentifier from drop-in
Route::post('/payment-order/{order}/refund', [PaymentController::class, 'refund']);

Route::post('transactions', [PaymentController::class, 'registerTransaction']);
