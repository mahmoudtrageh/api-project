<?php

/* Site login , logout  and Reset routes*/

Route::group(['namespace' => 'Api', 'prefix' => 'auth'], function () {

    Route::post('/register', 'AuthController@register');

    Route::post('/login', 'AuthController@login');

    Route::post('/pass-change', 'AuthController@pass_change');

});


/* Routes With No Login Required */


Route::group(['namespace' => 'Api'], function () {

    // Hotels

    Route::get('hotels', 'HomeController@get_hotels');

    // book hotel

    Route::post('create-booking', 'HomeController@book');

    // Search Hotels

    Route::get('search-hotels', 'HomeController@search_hotels');

    // update booking

    Route::post('update-booking-status', 'HomeController@updateBookingStatus');

    // get booking

    Route::get('get-bookings', 'HomeController@getBooking');

     // get facilities

     Route::get('facilities', 'HomeController@get_facilities');

      // create hotel

    Route::post('create-hotel', 'HomeController@create_hotel');

     // create facility

     Route::post('create-facility', 'HomeController@create_facility');
    

});

/* Routes With Login Required */

Route::group(['namespace' => 'Api', 'middleware' => 'Api'], function () {

    Route::group([ 'prefix' => 'auth'], function () {

        Route::get('profile-info', 'AuthController@profile_info');

        Route::post('update-info', 'AuthController@update_data');
    
	});

});
