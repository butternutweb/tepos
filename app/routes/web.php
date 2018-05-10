<?php

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

Route::middleware('guest')->group(function() {
    Route::get('/login', 'AuthController@getLogin')->name('auth.login');
    Route::post('/login', 'AuthController@doLogin');
    Route::get('/signup', 'AuthController@getSignup')->name('auth.signup');
    Route::post('/signup', 'AuthController@doSignup');
    Route::get('/forgot', 'AuthController@getForgot')->name('auth.forgot');
    Route::post('/forgot', 'AuthController@doForgot');
    Route::get('/reset', 'AuthController@getReset')->name('auth.reset');
    Route::get('/verify', 'AuthController@getVerify')->name('auth.verify');
});
Route::middleware('auth')->post('/logout', 'AuthController@doLogout')->name('auth.logout.post');

Route::middleware('auth')->group(function() {
    Route::get('/', 'DashboardController@getIndex')->name('dashboard.index');
    
    Route::get('/profile', 'ProfileController@getIndex')->name('profile.index');
    Route::post('/profile', 'ProfileController@doIndex');
    Route::get('/profile/change', 'ProfileController@getChange')->name('profile.change');

    Route::post('/owner/ajax', 'OwnerController@indexAjax');
    Route::post('/owner/bulk-delete', 'OwnerController@bulkDelete')->name('owner.bulk-delete');
    Route::resource('/owner', 'OwnerController');

    Route::post('/subs-plan/ajax', 'SubscriptionPlanController@indexAjax');
    Route::post('/subs-plan/bulk-delete', 'SubscriptionPlanController@bulkDelete')->name('subs-plan.bulk-delete');
    Route::get('/subs-plan/getToken','SubscriptionPlanController@getToken');
    Route::resource('/subs-plan', 'SubscriptionPlanController');
    
    Route::post('/subs-trans/ajax', 'SubscriptionTransactionController@indexAjax');
    Route::post('/subs-trans/bulk-delete', 'SubscriptionTransactionController@bulkDelete')->name('subs-trans.bulk-delete');
    Route::resource('/subs-trans', 'SubscriptionTransactionController');

    Route::post('/store/{store}/staff/ajax', 'StaffController@indexAjax');
    Route::post('/store/{store}/staff/bulk-delete', 'StaffController@bulkDelete')->name('staff.bulk-delete');
    Route::resource('/store/{store}/staff', 'StaffController');

    Route::post('/store/{store}/cost/ajax', 'CostController@indexAjax');
    Route::post('/store/{store}/cost/bulk-delete', 'CostController@bulkDelete')->name('cost.bulk-delete');
    Route::resource('/store/{store}/cost', 'CostController');

    Route::post('/store/{store}/product/ajax', 'StoreProductController@indexAjax');
    Route::post('/store/{store}/product/bulk-delete', 'StoreProductController@bulkDelete')->name('store_.product.bulk-delete');
    Route::resource('/store/{store}/product', 'StoreProductController', [
        'as' => 'store_'
    ]);

    Route::post('/store/ajax', 'StoreController@indexAjax');
    Route::post('/store/bulk-delete', 'StoreController@bulkDelete')->name('store.bulk-delete');
    Route::resource('/store', 'StoreController');
    
    Route::post('/product/ajax', 'ProductController@indexAjax');
    Route::post('/product/bulk-delete', 'ProductController@bulkDelete')->name('product.bulk-delete');
    Route::resource('/product', 'ProductController');

    Route::post('/category/{category}/sub-category/ajax', 'SubCategoryController@indexAjax');
    Route::post('/category/{category}/sub-category/bulk-delete', 'SubCategoryController@bulkDelete')->name('sub-category.bulk-delete');
    Route::resource('/category/{category}/sub-category', 'SubCategoryController');

    Route::post('/category/ajax', 'CategoryController@indexAjax');
    Route::post('/category/bulk-delete', 'CategoryController@bulkDelete')->name('category.bulk-delete');
    Route::resource('/category', 'CategoryController');
    
    Route::post('/transaction/{transaction}/product/ajax', 'TransactionProductController@indexAjax');
    Route::post('/transaction/{transaction}/product/bulk-delete', 'TransactionProductController@bulkDelete')->name('transaction_.product.bulk-delete');
    Route::resource('/transaction/{transaction}/product', 'TransactionProductController', [
        'as' => 'transaction_'
    ]);
    
    Route::get('/transaction/invoice/{id}','TransactionController@getInvoice')->name('transaction.invoice');
    Route::get('/transaction/{transaction}/checkout', 'TransactionController@getCheckout')->name('transaction.checkout');
    Route::post('/transaction/{transaction}/checkout', 'TransactionController@doCheckout');
    Route::post('/transaction/addProduct', 'TransactionController@addProduct');
    Route::post('/transaction/ajax', 'TransactionController@indexAjax');
    Route::post('/transaction/bulk-delete', 'TransactionController@bulkDelete')->name('transaction.bulk-delete');
    Route::resource('/transaction', 'TransactionController');
});