<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Artisan;
use Illuminate\Support\Facades\Storage;
class BackupController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Form(){
    	return view('pages.backup.form');
    }
    public function Backup(){
    	Artisan::call("backup:run",['--only-db'=>true]);
    }
    public function FileName(){
    	$data=scandir(storage_path('app/Laravel'));
    	return response()->json($data);
    }
    public function Download($data){
    	$path=storage_path('app/Laravel/'.$data);
    	// $header = [
     //    	'Content-Type' => 'application/*',
    	// ];
    	return response()->download($path,$data, array('Content-Type: application/*','Content-Length: '. filesize($path)))->deleteFileAfterSend(true);;
    }
    public function Delete($data){
    	$delete=Storage::delete("Laravel/".$data);
    	if ($delete) {
    		return response()->json(['message'=>'success']);
    	}
    }
}
