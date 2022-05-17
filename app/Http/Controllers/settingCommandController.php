<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
class settingCommandController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function storageLink(){
    	Artisan::call("storage:link",[]);
    }
    public function routeCache(){
    	Artisan::call("route:cache",[]);
    }
}
