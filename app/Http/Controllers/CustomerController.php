<?php

namespace App\Http\Controllers;
use App\Customer;
use App\Notification;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class CustomerController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function CustomerForm() {
        return view('pages.customer.customer');
    }
    public function CreateNew(Request $r) {
        $array = $r->all();
        if ($array['opening_balance'] === null) {
            $array['opening_balance'] = 0;
        }
        if ($array['maximum_due'] === null) {
            $array['maximum_due'] = 500;
        }
        if ($array['stutus'] === null) {
            $array['stutus'] = 1;
        }
        $validator = Validator::make($array, [
            'company_name' => "nullable|max:50",
            'name' => "required|max:50",            
            'spo' => "nullable|max:25|regex:/^([0-9]+)$/",
            'contact' => "nullable|max:25|regex:/^([0-9]+)$/",
            'opening_balance' => 'nullable|max:14|regex:/^([0-9.]+)$/',
            'balance_type' => 'required|max:1|regex:/^([0-1]+)$/',
            'maximum_due' => 'nullable|max:16|regex:/^([0-9.]+)$/',
            'phone1' => 'required|regex:/^([0-9]+)$/|max:20|unique:customers,phone1,' . $array['phone1'],
            'phone2' => 'nullable|max:20|regex:/^([0-9]+)$/|unique:customers,phone2,' . $array['phone2'],
            'email' => 'nullable|max:100|email|unique:customers,email,' . $array['email'],
            'birth_date' => 'nullable|max:100|date_format:d-m-Y',
            'mariage_date' => 'nullable|max:100|date_format:d-m-Y',
            'adress' => "nullable|max:100",
            'city' => 'nullable|max:50',
            'postal_code' => 'nullable|max:20|regex:/^([0-9]+)$/',
            'stutus' => 'nullable|max:1|regex:/^([0-1]+)$/',
            'group_types' => 'nullable|max:50|regex:/^([a-zA-Z0-9]+)$/',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2024',
        ]);

        //for image
        if ($validator->passes()) {
            $customer = new Customer;
            $customer->company_name = $array['company_name'];
            $customer->name = $array['name'];            
            $customer->spo_id = $array['spo'];
            $customer->ofcontact_id = $array['contact'];
            if (intval($array['balance_type']) === 0) {
                $customer->opening_balance = -abs($array['opening_balance']);
            } elseif (intval($array['balance_type']) === 1) {
                $customer->opening_balance = abs($array['opening_balance']);
            }
            $customer->maximum_due = $array['maximum_due'];
            $customer->phone1 = $array['phone1'];
            $customer->phone2 = $array['phone2'];
            $customer->email = $array['email'];
            $customer->birth_date = $array['birth_date'];
            $customer->marriage_date = $array['mariage_date'];
            $customer->adress = $array['adress'];
            $customer->city = $array['city'];
            $customer->postal_code = $array['postal_code'];
            $customer->stutus = $array['stutus'];
            $customer->group_types = $array['group_types'];
            $customer->users_id = Auth::user()->id;
            if ($r->hasFile('photo')) {
                $ext = $r->photo->getClientOriginalExtension();
                $name = Auth::user()->id . '_' . str_replace(" ", "_", $array['name']) . '_' . $array['phone1'] . '_' . time() . '.' . $ext;
                $r->photo->storeAs('public/customer', $name);
                $customer->photo = $name;
            }
            $customer->save();
            return response()->json(['message' => 'Customer Added Success']);
        }
        return response()->json([$validator->getMessageBag()]);
    }

    public function searchCustomer(Request $r) {
            $data = DB::select("SELECT id,name,phone1,adress from customers where name like :term or  phone1 like :term or  adress like :term limit 10",['term'=>'%'.$r->searchTerm.'%']);
            foreach ($data as $value) {
                $set_data[] = ['id' => $value->id, 'text' => $value->name . '(' . $value->phone1 .($value->adress!=null ? '-'.$value->adress : '').')'];
            }
            return $set_data;
    }

    public function getBalance($id) {
        if (!preg_match("/[^0-9]/", $id)) {
            $get = DB::select("
            SELECT
    cast(((t.Deposit+t.total_payablebacks)-(t.Expence+t.total_payable))+t.op_blnce as decimal(16,2)) as total
from(
    select
    ifnull(sum(ifnull(voucers.debit,0)),0) as Deposit,
    ifnull(sum(ifnull(voucers.credit,0)),0) as Expence,
    ifnull((select sum(total_payable) from invoices where customer_id=:id and (action_id=0 or action_id=3)),0) as total_payable,
    ifnull((select sum(total_payable) from invoices where customer_id=:id and action_id=2),0) as total_payablebacks,
    (select opening_balance from customers where id=:id) as op_blnce
    from voucers
    left join names on names.id=voucers.category  where (voucers.data_id=:id and voucers.nickname='customer') or (voucers.data_id=:id and names.table_name='customers')
    ) t", ['id' => $id]);
            return $get;
        } else {
            return ['data' => 'something wrong here'];
        }
    }
    public function getAll() {
        $groups=DB::select("
            SELECT id,name from groups
            ");
        if (request()->ajax()) {
            $get = DB::select("SELECT customers.id,customers.name,spos.name as spo_name,customers.phone1,customers.adress from customers
                left join spos on customers.spo_id=spos.id
             order by id desc");
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('spo_name',function($get){
                    if($get->spo_name==null){
                            return "Not Exist";
                        }else{
                            return $get->spo_name;
                        } 
                })
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                    return $button;
                })
                ->rawColumns(['action','spo_name'])->make(true);
        }
        return view('pages.customer.all_customer',compact('groups'));
    }
    public function Delete($id = null) {
        return 'sorry! you dont have to permission for delete';
        $photo = DB::table('customers')->select('photo')->where('id', $id)->first();
        $delete = Customer::where('id', $id)->delete();
        if ($delete) {
            $notification = new Notification;
            $notification->details = 'customer <strong>' . $customer->name . '(' . $id . ')</strong>' . ' Deleted by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
            $notification->action = 'delete';
            $notification->save();
            if (isset($photo)) {
                Storage::delete("public/customer/" . $photo->photo);
            }
            return response()->json(['message' => 'success']);
        }
    }
    public function getCustomer($id = null) {
        // $res = Customer::where('id', $id)->get();
        $res=DB::select("
            SELECT customers.company_name,customers.name,customers.spo_id,spos.name as spo_name,customers.opening_balance,customers.maximum_due,customers.phone1,customers.phone2,customers.email,customers.birth_date,customers.marriage_date,customers.adress,customers.city,customers.postal_code,customers.stutus,customers.group_types,customers.photo from customers
            left join spos on customers.spo_id=spos.id
            where customers.id=:id
            ",['id'=>$id]);
        // $res = $res->makeHidden(['created_at', 'updated_at']);
        return response()->json($res);
    }
    public function Update(Request $r, $id) {
        $array = $r->all();
        if ($array['opening_balance'] === null) {
            $array['opening_balance'] = 0;
        }
        if ($array['maximum_due'] === null) {
            $array['maximum_due'] = 500;
        }
        if ($array['stutus'] === null) {
            $array['stutus'] = 1;
        }
        $validator = Validator::make($array, [
            'company_name' => "nullable|max:50",
            'name' => "required|max:50",
            'spo' => "nullable|max:25|regex:/^([0-9]+)$/",
            'contact' => "nullable|max:25|regex:/^([0-9]+)$/",
            'opening_balance' => 'nullable|max:14|regex:/^([0-9.-]+)$/',
            'maximum_due' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'phone1' => 'required|regex:/^([0-9]+)$/|max:20|unique:customers,phone1,' . $id,
            'phone2' => 'nullable|max:20|regex:/^([0-9]+)$/|unique:customers,phone2,' . $id,
            'email' => 'nullable|max:100|email|unique:customers,email,' . $id,
            'birth_date' => 'nullable|max:100|date_format:d-m-Y',
            'mariage_date' => 'nullable|max:100|date_format:d-m-Y',
            'adress' => "nullable|max:100",
            'city' => 'nullable|max:50',
            'postal_code' => 'nullable|max:20|regex:/^([a-zA-Z0-9]+)$/',
            'stutus' => 'nullable|max:1|regex:/^([0-1]+)$/',
            'group_types' => 'nullable|max:50|regex:/^([a-zA-Z0-9]+)$/',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2024',
        ]);

        //for image
        if ($validator->passes()) {
            $customer = Customer::find($id);
            $customer->company_name = $array['company_name'];
            $customer->name = $array['name'];
            $customer->spo_id = $array['spo'];
            $customer->ofcontact_id = $array['contact'];
            if (intval($array['balance_type']) === 0) {
                $customer->opening_balance = -abs($array['opening_balance']);
            } elseif (intval($array['balance_type']) === 1) {
                $customer->opening_balance = abs($array['opening_balance']);
            }
            $customer->maximum_due = $array['maximum_due'];
            $customer->phone1 = $array['phone1'];
            $customer->phone2 = $array['phone2'];
            $customer->email = $array['email'];
            $customer->birth_date = $array['birth_date'];
            $customer->marriage_date = $array['mariage_date'];
            $customer->adress = $array['adress'];
            $customer->city = $array['city'];
            $customer->postal_code = $array['postal_code'];
            $customer->stutus = $array['stutus'];
            $customer->group_types = $array['group_types'];
            $customer->users_id = Auth::user()->id;
            if ($r->hasFile('photo')) {
                $photo = DB::table('customers')->select('photo')->where('id', $id)->first();
                $ext = $r->photo->getClientOriginalExtension();
                $name = $name = Auth::user()->id . '_' . str_replace(" ", "_", $array['name']) . '_' . $array['phone1'] . '_' . time() . '.' . $ext;
                $r->photo->storeAs('public/customer', $name);
                $customer->photo = $name;
            }
            $save = $customer->save();
            if ($save) {
                $notification = new Notification;
                $notification->details = 'customer <strong>' . $customer->name . '(' . $id . ')</strong>' . ' updated by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
                $notification->action = 'update';
                $notification->save();
                if (isset($photo)) {
                    Storage::delete('public/customer/' . $photo->photo);
                }
                return response()->json(['message' => 'Customer Updated Success']);
            }
        }
        return response()->json([$validator->getMessageBag()]);
    }
    public function InvCreateNew(Request $r) {
        // return $r->all();
        $validator = Validator::make($r->all(), [
            'customer_name' => "required|max:50|regex:/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z0-9 ]*)*$/",
            'customer_phone' => "required|max:20|unique:customers,phone1|max:50|regex:/^([0-9]+)$/",
        ]);
        if ($validator->passes()) {
            $cus = new Customer;
            $cus->name = $r->customer_name;
            $cus->phone1 = $r->customer_phone;
            $cus->user_id=Auth::user()->id;
            $cus->save();
            if ($cus) {
                return response()->json(['message' => 'Customer Added Success']);
            }
        }
        return response()->json($validator->getMessageBag());
    }
}
