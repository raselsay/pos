<?php

namespace App\Http\Controllers;

use App\ChildCategory;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;

class ChildCategoryController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function ManageCategory() {
        $category = DB::select('select id,name from categories');
        if (request()->ajax()) {
            $get = DB::select("SELECT child_categories.id,categories.name as cat_name,child_categories.name as childname,users.name as username from child_categories
inner join categories on categories.id=child_categories.cat_id
left join users on users.id=child_categories.user_id");
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('action',function($get){
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                    return $button;
                })
                ->make(true);
        }
        return view('pages.products.child_category', compact('category'));
    }
    public function insertCategory(Request $r) {
        $validator = Validator::make($r->all(), [
            'child_category' => 'required|max:50',
            'category' => 'required|max:10|regex:/^([0-9]+)$/',
        ]);
        //for image
        if ($validator->passes()) {
            $category = new ChildCategory;
            $category->cat_id = $r->category;
            $category->name = $r->child_category;
            $category->user_id = Auth::user()->id;
            $category->save();
            return response()->json(['message' => 'Child Category Aded Success']);
        }
        return response()->json($validator->getMessageBag());
    }
    public function getChildCat($id) {
        $validator = Validator::make(['cateogory_id' => $id], [
            'cateogory_id' => 'required|max:20|min:1',
        ]);
        if ($validator->passes()) {
            return $cat = DB::table('child_categories')->select('id', 'name')->where('cat_id', $id)->get();
        }
        return response()->json(['message' => $validator->getMessageBag()]);
    }
    public function getChildCatById($id){
        $validator = Validator::make(['child_id' => $id], [
            'child_id' => 'required|max:20|min:1',
        ]);
        if ($validator->passes()) {
            return $cat = DB::table('child_categories')->select('id', 'name')->where('id', $id)->get();
        }
        return response()->json(['message' => $validator->getMessageBag()]);
    }
    public function allChildCat() {
        $allCat = DB::table('child_categories')->select('id', 'name')->get();
        return [$allCat];
    }
    public function Update(Request $r,$id) {
        $validator = Validator::make($r->all(), [
            'child_category' => 'required|max:50',
            'category' => 'required|max:10|regex:/^([0-9]+)$/',
        ]);
        //for image
        if ($validator->passes()) {
            $category = ChildCategory::find($id);
            $category->cat_id = $r->category;
            $category->name = $r->child_category;
            $category->user_id = Auth::user()->id;
            $category->save();
            return response()->json(['message' => 'Child Category Updated Success']);
        }
        return response()->json($validator->getMessageBag());
    }
}
