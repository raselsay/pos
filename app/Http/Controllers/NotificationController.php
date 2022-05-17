<?php

namespace App\Http\Controllers;

use App\Notification;
use DataTables;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Super-Admin');
    }
    public function Form() {
        if (request()->ajax()) {
            $total_bal = 500;
            $get = Notification::select('details', 'action')->get();
            return DataTables::of($get)
                ->addIndexColumn()
                ->setRowAttr(['style' => function ($get) {
                    if ($get->action == 'delete') {
                        return 'background:red;color:white';
                    } elseif ($get->action == 'update') {
                        return 'background:yellow;color:green';
                    }
                }])
                ->escapeColumns([])
                ->make(true);
        }
        return view('pages.notification.notification');
    }
}
