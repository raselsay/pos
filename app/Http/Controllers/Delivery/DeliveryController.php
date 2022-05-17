<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class DeliveryController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Search(Request $r){
    	$data = DB::select("SELECT id,name from deliveries where name like :key limit 10",['key'=>'%'.$r->searchTerm.'%']);
            foreach ($data as $value) {
                $set_data[] = ['id' => $value->id, 'text' => $value->name];
            }
            return $set_data;
    }
}
