<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use Validator;
class PayOrderController extends Controller
{
    public function Form(){
    	return view('payment.order');
    }
    public function Create(Request $r){
    	// return $r->all();
    	$validator = Validator::make($r->all(), [
            'name' 			=> 'required|max:150',
            'business_name' => 'required|max:150',
            'number' 		=> 'required|max:150|regex:/^([0-9]+)$/',
            'email' 		=> 'nullable|email|max:150',
            'adress' 		=> 'required|max:150',
            'current_adress'=> 'nullable|max:150',
            'payment_method'=> 'required|max:150',
            'wallet_number' => 'required|max:20',
            'transaction' 	=> 'required|max:100|regex:/^([a-zA-Z0-9]+)$/',
            'payment_ammount' => 'required|max:100|regex:/^([0-9]+)$/',
            'note' 	=> 'nullable|max:100',
        ]);
        //for image
        if ($validator->passes()) {
            $order = new Order;
            $order->name=$r->name;
            $order->business_name=$r->business_name;
            $order->number=$r->number;
            $order->email=$r->email;
            $order->adress=$r->adress;
            $order->current_adress=$r->current_adress;
            $order->payment_method=$r->payment_method;
            $order->wallet_number=$r->wallet_number;
            $order->transaction=$r->transaction;
            $order->payment_ammount=$r->payment_ammount;
            $order->note=$r->note;
            $order->save();
            return response()->json(['message' => 'Form Submited Success']);
        }
        return response()->json($validator->getMessageBag());
    }
}
