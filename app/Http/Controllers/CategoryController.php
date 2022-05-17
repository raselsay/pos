<?php

namespace App\Http\Controllers;

use App\Category;
use App\Notification;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;

class CategoryController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function ManageCategory() {
        if (request()->ajax()) {
            $get = DB::select("select categories.id,categories.name,users.name as username from categories left join users on categories.user_id=users.id order by categories.id");
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
        return view('pages.products.category');
    }
    public function insertCategory(Request $r) {
        $validator = Validator::make($r->all(), [
            'name' => 'required|max:50|min:1|unique:categories,name',
        ]);

        //for image
        if ($validator->passes()) {
            $category = new Category;
            $category->name = $r->name;
            $category->user_id = Auth::user()->id;
            $category->save();
            return response()->json(['message' => 'Category Added Success']);
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
