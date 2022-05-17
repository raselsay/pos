<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Site;
use DB;
use Auth;
use DataTables;
use Validator;
class SiteController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Form(){
    	if (request()->ajax()) {
            $get = DB::select("
           select sites.id,customers.name as customer_name,sites.name,sites.adress,sites.mobile,sites.status from sites
           inner join customers on customers.id=sites.customer_id
            ");
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('status', function ($get) {
                    $status = $get->status==1 ? "Active" : "Deactive";
                    return $status;
                })
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                    return $button;
                })
                ->rawColumns(['action'])->make(true);
        }
    	return view('pages.customer_site.site');
    }
    public function Create(Request $r){
    	$validator=Validator::make($r->all(),[
    		'customer'=>'required|max:20',
    		'adress'=>'required|max:200',
    		'mobile'=>'required|max:100',
    		'mobile'=>'required|max:30',
    		'status'=>'nullable|max:1',
    	]);
    	if ($validator->passes()) {
    		$site=new Site;
    		$site->name=$r->name;
    		$site->customer_id=$r->customer;
    		$site->adress=$r->adress;
    		$site->mobile=$r->mobile;
    		$site->status=$r->status;
    		$site->user_id=Auth::user()->id;
    		$site->save();
    		return response()->json(['message'=>'Site Created Success']);
    	}
    	return response()->json($validator->getMessageBag());
    }
     public function searchSite(Request $r,$id) {
            $data = DB::select("SELECT id,name,adress from sites where (name like :term or  adress like :term) and customer_id=:id limit 10",['term'=>'%'.$r->searchTerm.'%','id'=>$id]);
            foreach ($data as $value) {
                $set_data[] = ['id' => $value->id, 'text' => $value->name . '(' . $value->adress .')'];
            }
            return $set_data;
    }
}
