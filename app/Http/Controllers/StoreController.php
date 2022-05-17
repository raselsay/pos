<?php

namespace App\Http\Controllers;

use App\Store;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;

class StoreController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function Form() {
        if (request()->ajax()) {
            $get = DB::select("
               select id,name,adress,capacity,type,status from stores
                order by id desc");
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                    return $button;
                })
                ->addColumn('status', function ($get) {
                    if ($get->status == 1) {
                        $status = 'Active';
                    } else {
                        $status = 'Deactive';
                    }
                    return $status;
                })
                ->rawColumns(['status','action'])->make(true);
        }
        return view('pages.store.store');
    }

    public function Create(Request $r) {
        $validation = Validator::make($r->all(), [
            'name' => 'required|max:100',
            'adress' => 'nullable|max:100',
            'capacity' => 'nullable|max:100|regex:/^([0-9.]+)$/',
            'type' => 'nullable|max:100',
            'status' => 'required|max:100',

        ]);
        if ($validation->passes()) {
            $store = new Store;
            $store->name = $r->name;
            $store->adress = $r->adress;
            $store->capacity = $r->capacity;
            $store->type = $r->type;
            $store->status = $r->status;
            $store->user_id = Auth::user()->id;
            $store->save();
            if ($store) {
                return response()->json(['message' => 'Store Inserted Success']);
            }
        }
    }
    public function Update(Request $r,$id) {
        $validation = Validator::make($r->all(), [
            'name' => 'required|max:100',
            'adress' => 'nullable|max:100',
            'capacity' => 'nullable|max:100|regex:/^([0-9.]+)$/',
            'type' => 'nullable|max:100',
            'status' => 'required|max:100',

        ]);
        if ($validation->passes()) {
            $store = Store::find($id);
            $store->name = $r->name;
            $store->adress = $r->adress;
            $store->capacity = $r->capacity;
            $store->type = $r->type;
            $store->status = $r->status;
            $store->user_id = Auth::user()->id;
            $store->save();
            if ($store) {
                return response()->json(['message' => 'Store Updated Success']);
            }
        }
        return response()->json($validation->getMessageBag());
    }
    public function getData($id){
        return response()->json(Store::find($id));
    }
    public function getStore(Request $r) {
            $data = DB::select("SELECT id,name from stores where name like :term limit 10",['term'=>'%'.$r->searchTerm.'%']);
            foreach ($data as $value) {
                $set_data[] = ['id' => $value->id, 'text' => $value->name];
            }
            if (isset($set_data)) {
                return $set_data;
            }
            
    }
    public function getStoreByAuthUser(Request $r) {
        if (auth()->user()->hasRole('Super-Admin')) {
            if (!preg_match("/[^a-zA-Z0-9. ]/", $r->searchTerm)) {
                $data = DB::select("SELECT id,name from stores where name like '%" . $r->searchTerm . "%' limit 10");
                foreach ($data as $value) {
                    $set_data[] = ['id' => $value->id, 'text' => $value->name];
                }
                if (isset($set_data)) {
                    return response()->json($set_data);
                } else {
                    return response()->json(['message' => 'not found']);
                }
            }
        } else {
            $data = DB::select("SELECT stores.id,stores.name from warehouse_permissions
            inner join stores on warehouse_permissions.store_id=stores.id
             where  warehouse_permissions.user_id=:id and stores.name like '%" . $r->searchTerm . "%' limit 10", ['id' => auth()->user()->id]);
            foreach ($data as $value) {
                $set_data[] = ['id' => $value->id, 'text' => $value->name];
            }
            if (isset($set_data)) {
                return response()->json($set_data);
            } else {
                return response()->json(['message' => 'not found']);
            }
        }

    }
    
}
