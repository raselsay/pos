<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Ofcontact;
use DataTables;
use DB;
class OfcontractController extends Controller
{
    public function Form(){
    	 if (request()->ajax()) {
            $get = DB::select("
           select id,name,email,area,status from ofcontacts 
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
    	return view('pages.ofcontact.ofcontact');
    }
    public function Create(Request $r){
    	$validator=Validator::make($r->all(),[
    		'name'=>'required|max:100',
    		'email'=>'required|max:100',
    		'adress'=>'required|max:100',
    		'area'=>'required|max:100',
    		'status'=>'nullable|max:100',
    	]);
    	if ($validator->passes()) {
    		$spo=new Ofcontact;
    		$spo->name=$r->name;
    		$spo->email=$r->email;
    		$spo->adress=$r->adress;
    		$spo->area=$r->area;
    		$spo->status=$r->status;
    		$spo->save();
    		return response()->json(['message'=>'Spo Created Success']);
    	}
    	return response()->json($validator->getMessageBag());
    }
    public function SearchSpo(Request $r){
        $data = DB::select("SELECT id,name from ofcontacts where name like :key limit 10",['key'=>'%'.$r->searchTerm.'%']);
            foreach ($data as $value) {
                $set_data[] = ['id' => $value->id, 'text' => $value->name];
            }
            return $set_data;
    }
}
