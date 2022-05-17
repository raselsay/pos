<?php

namespace App\Http\Controllers;
use App\Test;
use App\Voucer;
use DB;
use Illuminate\Http\Request;

class TestController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function test(Request $r) {
        $category = new Category;
        $category->name = '2004';
        $category->user_id = Auth::user()->id;
        $category->save();
        return response()->json(['message' => 'success']);
    }
    public function page() {
        return view('pages.testfile.test');
    }
    public function array() {
        return false;
    }
    public function select2(Request $r) {
        if (!preg_match("/[^a-zA-Z0-9. ]/", $r->searchTerm)) {
            $data = DB::select("SELECT id,product_name from products where product_name like '%" . $r->searchTerm . "%' or product_code like '%" . $r->searchTerm . "%' order by product_name asc limit 100");
            foreach ($data as $value) {
                $set_data[] = ['id' => $value->id, 'text' => $value->product_name];
            }
            return $set_data;
        }
    }

    public function testArray(Request $r) {
        $r['name'] = json_decode($r->name);
        return $r->all();
        return $x[1];
        // return json_decode($r->name);
    }
}
