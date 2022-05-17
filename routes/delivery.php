<?php

Route::get("/delivery","Delivery\LoginController@showLoginForm")->name("delivery.login");
Route::post("/delivery","Delivery\LoginController@login")->name("delivery.login.submit");
Route::get("/delivery/home","Delivery\HomeController@index")->name("delivery.home");
Route::get("/delivery/all_order","Delivery\HomeController@AllOrder");
Route::get("admin/delivery/get_order_sales/{id}","Delivery\HomeController@OrderSales");
Route::post("admin/delivery/confirm/{id}","Delivery\HomeController@ConfirmOrder");
Route::post("admin/delivery/search","Delivery\DeliveryController@Search");
Route::post("admin/delivery/store_search","Delivery\HomeController@SearchStoreByDelivery");
Route::get("admin/delivery/get_qantity/{product_id}/{store_id}","Delivery\HomeController@getQantity");
Route::post("admin/delivery/get_transport_export/","Delivery\HomeController@getTransportExport");
?>