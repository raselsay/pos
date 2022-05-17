<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use DB;
use Validator;
use DataTables;
use Auth;
use App\Notification;
use Illuminate\Support\Facades\Storage;
class EmployeeController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function ManageEmployee(){
    	if (request()->ajax()) {
            $total_bal=500;
            $get=DB::select("select id,name,email,adress,phone from employees order by id desc");
        return DataTables::of($get)
              ->addIndexColumn()
              ->addColumn('action',function($get){
          $button  ='<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="'.$get->id.'" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="'.$get->id.'"><i class="fas fa-trash-alt"></i></button>
                    </div>';
        return $button;
        })
       ->rawColumns(['action'])->make(true);
        }
        return view('pages.employee.employee');
    }
    public function insertEmployee(Request $r){
    	$validator = Validator::make($r->all(),[
        'name'       	 => "required|max:50",
        'email'      	 => 'nullable|email|unique:employees,email|max:100',
        'phone'          => 'required|max:15|min:3|unique:employees,phone|regex:/^([0-9]+)$/',
        'adress'         => "required|max:100",
        'experience' 	 => 'nullable|max:100',
        'nid'        	 => 'nullable|max:20|regex:/^([0-9]+)$/',
        'salary'         => 'required|max:12|regex:/^([0-9.]+)$/',
        'job_department' => 'required|max:100',
        'city'       	 => 'nullable|max:50',
        'photo'      	 => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

    //for image
    if ($validator->passes()) {
    	$employee= new Employee;
        $employee->name              = $r->name;
        $employee->email             = $r->email;
        $employee->phone           	 = $r->phone;
        $employee->adress            = $r->adress;
        $employee->experience        = $r->experience;
        $employee->nid               = $r->nid;
        $employee->salary            = $r->salary;
        $employee->job_dept   		 = $r->job_department;
        $employee->city   			 = $r->city;
        $employee->users_id   		 = Auth::user()->id;
        if ($r->hasFile('photo')) {
        	$r->photo->store('public/employee_img');
        }
        $employee->save();
        return response()->json(['message'=>'success']);
    }
    return response()->json([$validator->getMessageBag()]);
    }
    public function getEmployee($id){
         $data=DB::table('employees')->select('name','email','phone','adress','nid','experience','job_dept','city','salary','photo')->where('id',$id)->first();
         return response()->json([$data]);
    }

    public function Delete($id){
        return "sorry! you dont have to permission delete.";
        $name=DB::table('employees')->select('name')->where('id',$id)->first();
        $delete=Employee::where('id',$id)->delete();
        if($delete) {
            $notification=new Notification;
            $notification->details='Employee <strong>'.$name->name.'('.$id.')</strong>'.' deleted by <strong>'.Auth::user()->name.'('.Auth::user()->id.')</strong>';
            $notification->action='delete';
            $notification->save();
            return response()->json(['message'=>'success']);
        }
    }
    public function Update(Request $r,$id){
        // return $r->all();
            $validator = Validator::make($r->all(),[
            'name'              => "required|max:50",
            'email'             => 'nullable|email|unique:employees,email,'.$id,
            'phone'             => 'required|max:20|regex:/^([0-9]+)$/|unique:employees,phone,'.$id,
            'adress'            => "required|max:100",
            'experience'        => 'nullable|max:100',
            'nid'               => 'nullable|max:20|regex:/^([a-zA-Z0-9., ]+)$/',
            'salary'            => 'required|max:12|regex:/^([0-9.]+)$/',
            'job_department'    => 'required|max:100',
            'city'              => 'nullable|max:50',
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

        //for image
        if ($validator->passes()){
            $employee=Employee::find($id);
            $employee->name              = $r->name;
            $employee->email             = $r->email;
            $employee->phone             = $r->phone;
            $employee->adress            = $r->adress;
            $employee->experience        = $r->experience;
            $employee->nid               = $r->nid;
            $employee->salary            = $r->salary;
            $employee->job_dept          = $r->job_department;
            $employee->city              = $r->city;
            $employee->users_id          = Auth::user()->id;
            if ($r->hasFile('photo')){
                $photo= DB::table('employees')->select('photo')->where('id',$id)->first();
                Storage::delete("public/employee_img/".$photo->photo);
                $ext=$r->photo->getClientOriginalExtension();
                $name=Auth::user()->id.str_replace(' ','_',$r->name.$r->phone).time().'.'.$ext;
                $r->photo->storeAs('public/employee_img',$name);
                $employee->photo=$name;
            }
            $save=$employee->save();
            if ($save) {
            $notification=new Notification;
            $notification->details='Employee <strong>'.$r->name.'('.$id.')</strong>'.' Updated by <strong>'.Auth::user()->name.'('.Auth::user()->id.')</strong>';
            $notification->action='update';
            $notification->save();
            return response()->json(['message'=>'success']);
            }
        }
        return response()->json([$validator->getMessageBag()]);
    }
    public function SearchEmployee(Request $r){
        if (!preg_match("/[^a-zA-Z0-9. ]/", $r->searchTerm)) {
            $data = DB::select("SELECT id,name,phone from employees where name like '%" . $r->searchTerm . "%' or  phone like '%" . $r->searchTerm . "%' limit 10");
            foreach ($data as $value) {
                $set_data[] = ['id' => $value->id, 'text' => $value->name . '(' . $value->phone . ')'];
            }
            return $set_data;
        }
    }
    public function getBasicSalary($id){
       $data= DB::table('employees')->select('salary')->first();
       return response()->json($data);
    }
}
