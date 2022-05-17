<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Group;
use DataTables;
use DB;
use Auth;
class GroupController extends Controller
{
     public function __construct() {
        $this->middleware('auth');
    }
    public function Form() {
        if (request()->ajax()) {
            $get = DB::select("select groups.id,groups.name,users.name as username from groups left join users on groups.user_id=users.id order by groups.id");
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                    return $button;
                })
                ->rawColumns(['action'])->make(true);
        }
        return view('pages.customer_group.group');
    }
    public function Create(Request $r) {
        $validator = Validator::make($r->all(), [
            'name' => 'required|max:50|min:1|unique:groups,name',
        ]);
        if ($validator->passes()) {
            $group = new Group;
            $group->name = $r->name;
            $group->user_id = Auth::user()->id;
            $group->save();
            return response()->json(['message' => 'Group Added Success']);
        }
        return response()->json($validator->getMessageBag());
    }
    public function getCat() {
        $category = DB::table('categories')->select('id', 'name')->get();
        return [$category];
    }
    public function getCatById($id) {
        $get = Category::select('name')->where('id', $id)->first();
        return ['name' => $get->name];
    }
    public function Delete($id = null) {
        return "sorry! you dont have to permission for delete!";
        $name = Category::select('name')->where('id', $id)->first();
        $delete = Category::where('id', $id)->delete();
        if ($delete) {
            $notification = new Notification;
            $notification->details = 'Product Category <strong>' . $name->name . '(' . $id . ')</strong>' . ' deleted by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
            $notification->action = 'delete';
            $save = $notification->save();
            if ($save) {
                return response()->json(['message' => 'success']);
            }
        }
    }
    public function Update(Request $r, $id = null) {
        $validator = Validator::make($r->all(), [
            "name" => 'required|max:100|min:1|unique:categories,name,'.$id,
        ]);
        if ($validator->passes()) {
            $category = Category::find($id);
            $category->name = $r->name;
            $category->user_id = Auth::user()->id;
            $save = $category->save();
            if ($save) {
                $notification = new Notification;
                $notification->details = 'Category <strong>' . $r->name . '(' . $id . ')</strong>' . ' Updated by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
                $notification->action = 'update';
                $notification->save();
                return ['message' => 'success'];
            }

        }
        return response()->json($validator->getMessageBag());
    }
    public function SearchCategory(Request $r) {
            $data = DB::select("SELECT id,name from categories where name like '%" . $r->searchTerm . "%' or  id like '%" . $r->searchTerm . "%' limit 10",['term'=>'%'.$r->searchTerm.'%']);
            foreach ($data as $value) {
                $set_data[] = ['id' => $value->id, 'text' => $value->name];
            }
            if (isset($set_data)) {
                return $set_data;
            }
    }
}
