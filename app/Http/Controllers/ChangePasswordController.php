<?php

namespace App\Http\Controllers;
use App\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Validator;

class ChangePasswordController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function Form() {
        return view('pages.profile.change-password');
    }
    public function Change(Request $r) {
        // return $r->all();
        $validator = Validator::make($r->all(), [
            'old_password' => 'required|max:30|min:8|in:' . $r->old_password,
            'password' => 'required|max:30|min:8|confirmed',
        ]);
        if ($validator->passes()) {
            if (Hash::check($r->old_password, Auth::user()->password)) {
                $user = User::find(Auth::user()->id);
                $user->password = Hash::make($r->password);
                $user->save();
                return response()->json(['message' => 'Password Changed Success']);
            } else {
                return response()->json(['error' => ['old_password' => ['Old Password Not Matched']]]);
            }
        }
        return response()->json(['error' => $validator->getMessageBag()]);
    }
}
