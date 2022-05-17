<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DOMPDF;
use DNS2D;
use DNS1D;
// use Auth;
use DB;
class BarcodeController extends Controller
{
    public function __construct(){
        $this->middleware('auth'); 
    }
    public function Form(){
    	return view('pages.barcode.barcode');
    }
    public function Generate(Request $r){
    	$validate=$r->validate([
            'product'=>'required|min:1|max:100|',
            'qantity'=>'required|min:1|max:3|regex:/^([0-9]+)$/',
    	]);

        $data=explode('|',$r->product);
        $p=DB::table('products')->select('sale_price')->where('product_code',$data[0])->first();
    	if ($data[0]==='0') {
    		$barcode="<h2 style='text-align:center;color:red;margin-top:100px;'>Product Code not found!! Please give us valid code</h2>";
            return $barcode;
    	}else{
            
            // return json_encode($data);
            for ($i=0; $i <$r->qantity ; $i++){
                $barcode[]= DNS1D::getBarcodeSVG(str_pad($data[0],10, "0", STR_PAD_LEFT),'I25');
                $text[]=$data[1];
                $price[]=$p->sale_price;
            }
            
    	}
    	// return view('pages.reports.barcode.pdf',compact('barcode','text','price'));
        $pdf = DOMPDF::loadView('pages.reports.barcode.pdf',compact('barcode','text','price'));
        return $pdf->stream('barcode_'.($data[1]).'.pdf');
    }
}




